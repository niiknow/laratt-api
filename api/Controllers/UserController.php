<?php

namespace Api\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\GeneralException;
use Api\Extra\RequestQueryBuilder;
use Api\Controllers\Controller;
use Api\Models\User;

class UserController extends Controller
{
    /**
     * @var array
     */
    protected $vrules = [
        'email' => 'required|email|max:190',
        'email_verified_at' => 'nullable|date',
        'seen_at' => 'nullable|date',
        'photo_url' => 'nullable|url|max:190',
        'phone_country_code' => 'nullable|regex:/^(\+?\d{1,3}|\d{1,4})$/',
        'phone' => 'nullable|regex:/\d{7,20}+/',
        'group' => 'nullable|string|max:190',
        'tfa_type' => 'nullable|in:off,email,sms,call',
        'authy_id' => 'nullable|string|max:190',
        'authy_status' => 'nullable|string|max:190',
        'google_tfa_secret' => 'nullable|string|max:190',
        'tfa_code' => 'nullable|string|max:190',
        'tfa_exp_at' => 'nullable|date',

        'email_alt' => 'nullable|email|max:190',
        'first_name' => 'nullable|string|max:190',
        'last_name' => 'nullable|string|max:190',
        'address1' => 'nullable|string|max:190',
        'address2' => 'nullable|string|max:190',

        'postal' => 'nullable|string|max:50',
        'city' => 'nullable|string|max:190',
        'state' => 'nullable|string|max:190',
        'country' => 'nullable|string|max:190',
        'email_list_optin_at' => 'nullable|date',
        'is_retired_or_unemployed' => 'nullable|boolean',
        'occupation' => 'nullable|string|max:190',
        'employer' => 'nullable|string|max:190',

        'stripe_customer_id' => 'nullable|string|max:190',
        'card_brand' => 'nullable|string|max:50',
        'card_last4' => 'nullable|string|max:4',
        'extra_data' => 'nullable',
        'extra_meta' => 'nullable'
    ];

    public function create(Request $request)
    {
        return $this->update($request, null);
    }

    public function retrieve($id)
    {
        return (new User())->tableFind($id, 'user');
    }

    public function delete(Request $request, $id)
    {
        $item = $this->retrieve($id);

        if ($item && !$item->delete()) {
            throw new GeneralException(__('exceptions.user.delete'));
        }
    }

    public function list(Request $request)
    {
        $item = new User();
        $item->createTableIfNotExists(tenantId());

        $qb = new RequestQueryBuilder(\DB::table($item->getTable()));
        return $qb->applyRequest($request);
    }

    public function update(Request $request, $id)
    {
        $rules = array();
        $rules = $this->vrules;
        $this->validate($request, $rules);

        $inputs = $request->all();
        $item   = new User($inputs);
        if (isset($id)) {
            $item = $item->tableFill($id, $inputs, 'user');
        } else {
            $item = $item->tableCreate('user');
        }

        if (!$item->save()) {
            throw new GeneralException(__('exceptions.user.update'));
        }

        return $item;
    }
}
