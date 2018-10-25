<?php

namespace Api\Controllers;

use Illuminate\Http\Request;
use Api\Controllers\Controller;
use Api\Models\Profile;

class ProfileController extends Controller
{
    use ApiTabletrait;

    /**
     * @var array
     */
    protected $vrules = [
        'email' => 'required|email|max:190',
        'email_verified_at' => 'nullable|date',
        'seen_at' => 'nullable|date',
        'image_url' => 'nullable|url|max:190',
        'phone_country_code' => 'nullable|regex:/^(\+?\d{1,3}|\d{1,4})$/',
        'phone' => 'nullable|regex:/\d{7,20}+/',
        'group' => 'nullable|string|max:190',
        'access' => 'nullable|string|max:190',
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
        'data.*' => 'nullable',
        'meta.*' => 'nullable'
    ];

    public function getTable()
    {
        return 'profile';
    }

    public function getModel($attrs = [])
    {
        return new Profile($attrs);
    }

    /**
     * @OA\Post(
     *   path="/profiles/create",
     *   tags={"profiles"},
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
     *   path="/profiles/{uid}/retrieve",
     *   tags={"profiles"},
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
     *   path="/profiles/{uid}/delete",
     *   tags={"profiles"},
     *   summary="delete a single profile, also accept method: POST
     *   See also /list for bulk delete by query.",
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
     *   path="/profiles/list",
     *   tags={"profiles"},
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
     *   path="/profiles/data",
     *   tags={"profiles"},
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
     *   path="/profiles/{uid}/upsert",
     *   tags={"profiles"},
     *   summary="upsert profile",
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
     *   path="/profiles/import",
     *   tags={"profiles"},
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
     *   path="/profiles/truncate",
     *   tags={"profiles"},
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
}
