<?php

namespace Api\Models;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;

use Api\Models\Traits\CloudAuditable;
use Api\Models\Traits\DynamicModelTrait;
use Carbon\Carbon;

class Profile extends Authenticatable
{
    use CloudAuditable,
        DynamicModelTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uid', 'email', 'email_verified_at', 'password', 'password_updated_at', 'photo_url',
        'phone_country_code', 'phone', 'group', 'tfa_type', 'authy_id', 'authy_status',
        'google_tfa_secret', 'tfa_code', 'tfa_exp_at',

        'email_alt', 'first_name', 'last_name', 'address1', 'address2',
        'postal', 'city', 'state', 'country', 'email_list_optin_at',
        'is_retired_or_unemployed', 'occupation', 'employer',

        'stripe_customer_id', 'card_brand', 'card_last4',
        'extra_data', 'extra_meta', 'seen_at'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'extra_meta' => 'array',
        'extra_data' => 'array',
        'is_retired_or_unemployed' => 'boolean'
    ];

    /**
     * The attributes that should be casted by Carbon
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'password_updated_at',
        'email_verified_at',
        'tfa_exp_at',
        'seen_at'
    ];

    public function createTableIfNotExists($tenant)
    {
        $tableNew = $this->setTableName($tenant, 'profile');

        // only need to improve performance in prod
        if (config('env') === 'production' && \Cache::has($tableNew)) {
            return $tableNew;
        }

        if (!Schema::hasTable($tableNew)) {
            Schema::create($tableNew, function (Blueprint $table) {
                $table->bigIncrements('id');

                // client/consumer/external primary key
                // this allow client to prevent duplicate
                // for example, duplicate during bulk import
                $table->string('uid')->unique();

                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->timestamp('seen_at')->nullable();

                $table->string('password')->nullable();
                $table->timestamp('password_updated_at')->nullable();

                $table->string('photo_url')->nullable();
                $table->string('phone_country_code', 5)->default('1');
                $table->string('phone', 20)->nullable();

                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->enum('tfa_type', ['off', 'email', 'sms', 'call', 'google_soft_token', 'authy_soft_token', 'authy_onetouch'])->default('off');
                $table->string('authy_id')->unique()->nullable();
                $table->string('authy_status')->nullable();
                $table->string('google_tfa_secret')->nullable();
                $table->string('tfa_code')->nullable();
                $table->timestamp('tfa_exp_at')->nullable();

                // member, admin, etc...
                $table->string('group')->default('member');

                $table->string('email_alt')->nullable();
                $table->string('address1')->nullable();
                $table->string('address2')->nullable();
                $table->string('postal', 50)->nullable();
                $table->string('city')->nullable();
                $table->string('state')->nullable();
                $table->string('country')->nullable();
                $table->double('lat', 11, 8)->nullable();
                $table->double('lng', 11, 8)->nullable();

                // subscription info
                $table->timestamp('email_list_optin_at')->nullable();
                $table->boolean('is_retired_or_unemployed')->default(0);
                $table->string('occupation')->nullable();
                $table->string('employer')->nullable();

                $table->string('stripe_customer_id')->nullable();
                $table->string('card_brand', 50)->nullable();
                $table->string('card_last4', 4)->nullable();

                $table->timestamps();

                $table->mediumText('extra_meta')->nullable();
                $table->mediumText('extra_data')->nullable();
            });

            // cache database check for 12 hours or half a day
            \Cache::add($tableNew, 'true', 60*12);
        }

        return $tableNew;
    }

    public function setEmailAttribute($value)
    {
        $existing = $this->email;
        $new      = strtolower(trim($value));

        // reset authy id
        if ($existing != $new) {
            $this->authy_id            = null;
            $this->attributes['email'] = $new;
        }
    }

    public function setPhoneAttribute($value)
    {
        $existing = $this->phone;
        $new      = preg_replace('/\D+/', '', $value);

        if ($existing != $new) {
            $this->authy_id            = null;
            $this->attributes['phone'] = $new;
        }
    }

    public function setPhoneCountryCodeAttribute($value)
    {
        $this->attributes['phone_country_code'] = preg_replace('/[^0-9\+]/', '', $value);
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password']            = $value;
        $this->attributes['password_updated_at'] = Carbon::now();
    }

// <tfa
    public function generateTfaCode()
    {
        // Generate the code
        $length   = 8;
        $code     = openssl_random_pseudo_bytes($length);
        $userCode = '';
        $i        = 0;
        while (strlen($userCode) < $length) {
            $userCode .= hexdec(bin2hex($code{$i}));
            $i++;
        }
        $userCode = substr($userCode, 0, 8);
        return $userCode;
    }

    public function hasTfaExpired()
    {
        return $this->tfa_exp_at < Carbon::now();
    }

    public function getTfaCode()
    {
        $value = $this->attributes['tfa_code'];
        // return \Crypt::decryptString($value);
        return $value;
    }

    public function setTfaCode($value = null)
    {
        if (!isset($value)) {
            $value = $this->generateTfaCode();
            // $value = \Crypt::encryptString($value);
        }

        $this->attributes['tfa_code'] = $value;

        // set expire in 10 minutes
        $this->attributes['tfa_exp_at'] = Carbon::now()->addMinutes(10);
    }

    public function getGoogleTfaSecret()
    {
        $value = $this->attributes['google_tfa_secret'];
        // return \Crypt::decryptString($value);
        return $value;
    }

    public function setGoogleTfaSecret($value = null)
    {
        if (!isset($value)) {
            $google2fa = new Google2FA();
            $value     = $google2fa->generateSecretKey(16, $this->id);
            // $value = \Crypt::encryptString($value);
        }

        $this->attributes['google_tfa_secret'] = $value;
    }
// </tfa

    public function getPhotoUrlAttribute($value)
    {
        $defaultPhotoUrl = 'https://www.gravatar.com/avatar/'.md5(strtolower($this->email)).'.jpg?s=200&d=mm';
        return empty($value) ? $defaultPhotoUrl : url($value);
    }

    public function setFirstNameAttribute($value)
    {
        $this->attributes['first_name'] = ucfirst(trim($value));
    }

    public function setLastNameAttribute($value)
    {
        $this->attributes['last_name'] = ucfirst(trim($value));
    }

    public function getNameAttribute($value)
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
