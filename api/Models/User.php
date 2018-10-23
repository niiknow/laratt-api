<?php

namespace Api\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;

use Carbon\Carbon;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid', 'email', 'email_verified_at', 'password','photo_url',
        'phone_country_code', 'phone', 'group', 'tfa_type', 'authy_id',
        'authy_status', 'google_tfa_secret', 'tfa_code', 'tfa_exp_at',

        'email_alt', 'first_name', 'last_name', 'address1', 'address2',
        'postal', 'city', 'state', 'country', 'email_list_optin_at',
        'is_retired_or_unemployed', 'occupation', 'employer',

        'stripe_customer_id', 'card_brand', 'card_last4', 'meta1', 'meta2'
    ];

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
        $new      = preg_replace('/[^0-9\+]/', '', $value);

        if ($existing != $new) {
            $this->authy_id            = null;
            $this->attributes['phone'] = $new;
        }
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

    public function getPhotoUrlAttribute($value)
    {
        $defaultPhotoUrl = 'https://www.gravatar.com/avatar/'.md5(strtolower($this->email)).'.jpg?s=200&d=mm';
        return empty($value) ? $defaultPhotoUrl : url($value);
    }

    public function setFirstNameAttribute($value)
    {
        $this->attributes['first_name'] = ucfirst($value);
    }

    public function setLastNameAttribute($value)
    {
        $this->attributes['last_name'] = ucfirst($value);
    }

    public function getNameAttribute($value)
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
