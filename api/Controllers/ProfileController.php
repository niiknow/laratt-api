<?php
namespace Api\Controllers;

use Api\Controllers\Controller;
use Niiknow\Laratt\Models\ProfileModel;
use Niiknow\Laratt\Traits\ApiTableTrait;

class ProfileController extends Controller
{
    use ApiTableTrait;

    /**
     * @var array
     */
    protected $vrules = [
        'uid'                      => 'nullable|string|max:50',
        'email'                    => 'required|email|max:190',
        'email_verified_at'        => 'nullable|date',
        'seen_at'                  => 'nullable|date',
        'photo_url'                => 'nullable|url|max:190',
        'phone_country_code'       => 'nullable|regex:/^(\+?\d{1,3}|\d{1,4})$/',
        'phone'                    => 'nullable|regex:/\d{7,20}+/',
        'group'                    => 'nullable|string|max:190',
        'access'                   => 'nullable|string|max:190',
        'tfa_type'                 => 'nullable|in:off,email,sms,call',
        'authy_id'                 => 'nullable|string|max:190',
        'authy_status'             => 'nullable|string|max:190',
        'tfa_code'                 => 'nullable|string|max:190',
        'tfa_exp_at'               => 'nullable|date',
        'email_alt'                => 'nullable|email|max:190',
        'first_name'               => 'nullable|string|max:190',
        'last_name'                => 'nullable|string|max:190',
        'address1'                 => 'nullable|string|max:190',
        'address2'                 => 'nullable|string|max:190',
        'postal'                   => 'nullable|string|max:50',
        'city'                     => 'nullable|string|max:190',
        'state'                    => 'nullable|string|max:190',
        'country'                  => 'nullable|string|max:190',
        'email_list_optin_at'      => 'nullable|date',
        'is_retired_or_unemployed' => 'nullable|boolean',
        'occupation'               => 'nullable|string|max:190',
        'employer'                 => 'nullable|string|max:190',
        'pay_customer_id'          => 'nullable|string|max:190',
        'pay_type'                 => 'nullable|string|max:190',
        'pay_brand'                => 'nullable|string|max:50',
        'pay_last4'                => 'nullable|string|max:4',
        'pay_month'                => 'nullable|string|max:2',
        'pay_year'                 => 'nullable|string|max:4',
        'data.*'                   => 'nullable',
        'meta.*'                   => 'nullable'
    ];

    /**
     * @param  array   $attrs
     * @return mixed
     */
    public function getModel($attrs = [])
    {
        $item = new ProfileModel($attrs);
        $item->createTableIfNotExists(
            TenancyResolver::resolve(),
            'profile'
        );

        return $item;
    }

    public function getTableName()
    {
        return 'profile';
    }
}
