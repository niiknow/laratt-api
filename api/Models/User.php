<?php

namespace Api\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Carbon\Carbon;
use Wildside\Userstamps\Userstamps;

use Api\Extra\FileManager;
use App\Notifications\PasswordResetNotification;
use Api\Models\Traits\HasUuid;
use Api\Models\Traits\HasTeams;
use Api\Models\Traits\PermissionsTrait;
use Api\Models\Traits\Auditable;

class User extends Authenticatable implements JWTSubject
{
    use Auditable,
        Notifiable,
        Userstamps,
        HasUuid,
        SoftDeletes,
        HasTeams,
        PermissionsTrait;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * eager load
     * @var array
     */
    protected $with = [];

    /**
     * @var array
     */
    protected $appends = [
        'is_admin', 'name'
    ];

    /**
     * The attributes that should be casted by Carbon
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'email_verified_at',
        'tfa_exp_at'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'photo_url', 'locale', 'timezone',
        'tfa_type', 'email_verified_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'google_tfa_secret', 'tfa_code', 'tfa_exp',
        'authy_id', 'authy_status', 'password_updated_at'
    ];

    /**
     * @var string
     */
    protected $table = 'user';

    /**
     * Override Password Reset Default Built in Laravel
     *
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new PasswordResetNotification($token));
    }

    public function getPhotoUrlAttribute($value)
    {
        $defaultPhotoUrl = 'https://www.gravatar.com/avatar/'.md5(strtolower($this->email)).'.jpg?s=200&d=mm';
        return empty($value) ? $defaultPhotoUrl : url($value);
    }

    public function getIsAdminAttribute()
    {
        return $this->access === 'admin';
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
        if (\Hash::needsRehash($value)) {
            $value = bcrypt($value);
        }

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
        return $this->custom_tfa_exp < Carbon::now();
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
        $this->attributes['tfa_exp'] = Carbon::now()->addMinutes(10);
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

// <jwt
    /**
    * Get the identifier that will be stored in the subject claim of the JWT.
    *
    * @return mixed
    */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
    * Return a key value array, containing any custom claims to be added to the JWT.
    *
    * @return array
    */
    public function getJWTCustomClaims()
    {
        return $this->getPermsAttribute();
    }

    /**
     * The user has been authenticated.
     *
     * @param  Request $request
     * @param  mixed $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        $user = $user->append('perms');
        $data = [
          'access_token' => $this->getJWTToken($request, $user),
          'expires_in'   => $this->getTTl(),
          'type'         => 'bearer'
        ];

        return response()->json($data);
    }

    protected function getJWTToken(Request $request, $user)
    {
        /** @var JWTGuard $guard */
        $guard = \Auth::guard();
        $user  = $user->append('perms');
        return $guard->login($user);
    }

    public function getTTl()
    {
        $guard = \Auth::guard();

        return $guard->factory()->getTTL() * 60;
    }

    public function logout(Request $request)
    {
        try {
            $this->guard()->logout();
            $request->session()->invalidate();
        } catch (\Exception $exception) {
            // swallow
        }

        return response()->json(['succeeded' => true]);
    }
// </jwt

    public function contact()
    {
        return $this->hasOne(UserContact::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function uploadUserFile(Request $request, $requestName = 'file')
    {
        $uploader = new FileManager(
            's3-public2',
            config('admin.upload.path.user', 'user') . '/' . $this->id
        );

        return $uploader->uploadFromRequest($request, $requestName);
    }
}
