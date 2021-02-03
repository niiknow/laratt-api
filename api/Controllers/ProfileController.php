<?php
namespace Api\Controllers;

use Api\Controllers\Controller;
use Niiknow\Laratt\Models\ProfileModel;
use Niiknow\Laratt\TenancyResolver;
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

    /**
     * @OA\Post(
     *   path="/profile/create",
     *   tags={"profile"},
     *   summary="create profile",
     *   @OA\Parameter(
     *     name="X-API-Key",
     *     in="header",
     *     description="api key",
     *     required=false,
     *     @OA\Schema(
     *       type="string"
     *     ),
     *     style="form"
     *   ),
     *   @OA\Parameter(
     *     name="X-Tenant",
     *     in="header",
     *     description="tenant id",
     *     required=true,
     *     @OA\Schema(
     *       type="string"
     *     ),
     *     style="form"
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="new profile object"
     *   )
     * )
     */

    /**
     * @OA\Get(
     *   path="/profile/{uid}/retrieve",
     *   tags={"profile"},
     *   summary="get profile",
     *   @OA\Parameter(
     *     name="X-API-Key",
     *     in="header",
     *     description="api key",
     *     required=false,
     *     @OA\Schema(
     *       type="string"
     *     ),
     *     style="form"
     *   ),
     *   @OA\Parameter(
     *     name="X-Tenant",
     *     in="header",
     *     description="tenant id",
     *     required=true,
     *     @OA\Schema(
     *       type="string"
     *     ),
     *     style="form"
     *   ),
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="uid",
     *     required=true,
     *     @OA\Schema(
     *       type="string"
     *     ),
     *     style="form"
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="profile not found"
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="profile object"
     *   )
     * )
     */

    /**
     * @OA\Delete(
     *   path="/profile/{uid}/delete",
     *   tags={"profile"},
     *   summary="delete a single profile, also accept method: POST
     *   See also /query for bulk delete by query.",
     *   @OA\Parameter(
     *     name="X-API-Key",
     *     in="header",
     *     description="api key",
     *     required=false,
     *     @OA\Schema(
     *       type="string"
     *     ),
     *     style="form"
     *   ),
     *   @OA\Parameter(
     *     name="X-Tenant",
     *     in="header",
     *     description="tenant id",
     *     required=true,
     *     @OA\Schema(
     *       type="string"
     *     ),
     *     style="form"
     *   ),
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="uid",
     *     required=true,
     *     @OA\Schema(
     *       type="string"
     *     ),
     *     style="form"
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="profile not found"
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="deleted profile object"
     *   )
     * )
     */

    /**
     * @OA\Get(
     *   path="/profile/query",
     *   tags={"profile"},
     *   summary="search or delete profile, use DELETE http method to bulk delete",
     *   @OA\Parameter(
     *     name="X-API-Key",
     *     in="header",
     *     description="api key",
     *     required=false,
     *     @OA\Schema(
     *       type="string"
     *     ),
     *     style="form"
     *   ),
     *   @OA\Parameter(
     *     name="X-Tenant",
     *     in="header",
     *     description="tenant id",
     *     required=true,
     *     @OA\Schema(
     *       type="string"
     *     ),
     *     style="form"
     *   ),
     *   @OA\Parameter(
     *     name="select",
     *     in="query",
     *     description="comma separated list of columns that you want to return",
     *     required=false,
     *     @OA\Schema(
     *       type="string"
     *     ),
     *     style="form"
     *   ),
     *   @OA\Parameter(
     *     name="filter",
     *     in="query",
     *     description="array of column_name:operator:value to filter result with",
     *     required=false,
     *     @OA\Schema(
     *       type="array",
     *       @OA\Items(type="string")
     *     ),
     *     style="form"
     *   ),
     *   @OA\Parameter(
     *     name="limit",
     *     in="query",
     *     description="limit the number of returned",
     *     required=false,
     *     @OA\Schema(
     *       type="integer"
     *     ),
     *     style="form"
     *   ),
     *   @OA\Parameter(
     *     name="sort",
     *     in="query",
     *     description="array of column_name:asc/desc to sort result",
     *     required=false,
     *     @OA\Schema(
     *       type="array",
     *       @OA\Items(type="string")
     *     ),
     *     style="form"
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Eloquent builder paginated json"
     *   )
     * )
     */

    /**
     * @OA\Get(
     *   path="/profile/data",
     *   tags={"profile"},
     *   summary="jQuery DataTable endpoint",
     *   @OA\Parameter(
     *     name="X-API-Key",
     *     in="header",
     *     description="api key",
     *     required=false,
     *     @OA\Schema(
     *       type="string"
     *     ),
     *     style="form"
     *   ),
     *   @OA\Parameter(
     *     name="X-Tenant",
     *     in="header",
     *     description="tenant id",
     *     required=true,
     *     @OA\Schema(
     *       type="string"
     *     ),
     *     style="form"
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="jQuery DataTable json"
     *   )
     * )
     */

    /**
     * @OA\Post(
     *   path="/profile/{uid}/update",
     *   tags={"profile"},
     *   summary="update profile",
     *   @OA\Parameter(
     *     name="X-API-Key",
     *     in="header",
     *     description="api key",
     *     required=false,
     *     @OA\Schema(
     *       type="string"
     *     ),
     *     style="form"
     *   ),
     *   @OA\Parameter(
     *     name="X-Tenant",
     *     in="header",
     *     description="tenant id",
     *     required=true,
     *     @OA\Schema(
     *       type="string"
     *     ),
     *     style="form"
     *   ),
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="uid",
     *     required=true,
     *     @OA\Schema(
     *       type="string"
     *     ),
     *     style="form"
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="new or updated profile object"
     *   )
     * )
     */

    /**
     * @OA\Post(
     *   path="/profile/import",
     *   tags={"profile"},
     *   summary="import csv of profiles",
     *   @OA\Parameter(
     *     name="X-API-Key",
     *     in="header",
     *     description="api key",
     *     required=false,
     *     @OA\Schema(
     *       type="string"
     *     ),
     *     style="form"
     *   ),
     *   @OA\Parameter(
     *     name="X-Tenant",
     *     in="header",
     *     description="tenant id",
     *     required=true,
     *     @OA\Schema(
     *       type="string"
     *     ),
     *     style="form"
     *   ),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\MediaType(
     *       mediaType="multipart/form-data",
     *       @OA\Schema(
     *         @OA\Property(
     *           description="csv file with header columns",
     *           property="file",
     *           type="string",
     *           format="file",
     *         ),
     *         required={"file"}
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response="422",
     *     description="import with error, rowno, and errored row"
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="imported list of uid(s)"
     *   )
     * )
     */

    /**
     * @OA\Post(
     *   path="/profile/truncate",
     *   tags={"profile"},
     *   summary="delete everything from the profile table. Why not?
     *   Hint: this is why 'uid' is better than system 'id'.",
     *   @OA\Parameter(
     *     name="X-API-Key",
     *     in="header",
     *     description="api key",
     *     required=false,
     *     @OA\Schema(
     *       type="string"
     *     ),
     *     style="form"
     *   ),
     *   @OA\Parameter(
     *     name="X-Tenant",
     *     in="header",
     *     description="tenant id",
     *     required=true,
     *     @OA\Schema(
     *       type="string"
     *     ),
     *     style="form"
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="nothing if success"
     *   )
     * )
     */

    /**
     * @OA\Post(
     *   path="/profile/drop",
     *   tags={"profile"},
     *   summary="drop the profile table. Why not?",
     *   @OA\Parameter(
     *     name="X-API-Key",
     *     in="header",
     *     description="api key",
     *     required=false,
     *     @OA\Schema(
     *       type="string"
     *     ),
     *     style="form"
     *   ),
     *   @OA\Parameter(
     *     name="X-Tenant",
     *     in="header",
     *     description="tenant id",
     *     required=true,
     *     @OA\Schema(
     *       type="string"
     *     ),
     *     style="form"
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="nothing if success"
     *   )
     * )
     */
    public function getTableName()
    {
        return 'profile';
    }
}
