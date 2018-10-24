<?php

namespace Api\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\GeneralException;
use Api\Extra\RequestQueryBuilder;
use Api\Controllers\Controller;
use Api\Models\Profile;
use Yajra\DataTables\DataTables;
use Validator;

class ProfileController extends Controller
{
    use ApiResponseTrait;

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

    /**
     * @OA\Post(
     *   path="/profiles/create",
     *   tags={"profile"},
     *   summary="create profile, accepts:POST,PUT,PATCH",
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
    public function create(Request $request)
    {
        return $this->upsert($request, null);
    }

    /**
     * @OA\Get(
     *   path="/profiles/{uid}/retrieve",
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
    public function retrieve($uid)
    {
        $item = (new Profile())->tableFind($uid, 'profile');
        return $this->rsp(isset($item) ? 200 : 404, $item);
    }

    /**
     * @OA\Delete(
     *   path="/profiles/{uid}/delete",
     *   tags={"profile"},
     *   summary="delete profile",
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
    public function delete(Request $request, $uid)
    {
        $item = (new Profile())->tableFind($uid, 'profile');

        if ($item && !$item->delete()) {
            throw new GeneralException(__('exceptions.profile.delete'));
        }

        return $this->rsp(isset($item) ? 200 : 404, $item);
    }

    /**
     * @OA\Get(
     *   path="/profiles/list",
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
    public function list(Request $request)
    {
        $item = new Profile();
        $item->createTableIfNotExists(tenantId());

        $qb = new RequestQueryBuilder(\DB::table($item->getTable()));
        return $qb->applyRequest($request);
    }

    /**
     * @OA\Get(
     *   path="/profiles/data",
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
    public function data(Request $request)
    {
        $item = new Profile();
        $item->createTableIfNotExists(tenantId());

        return DataTables::of(\DB::table($item->getTable()))->make(true);
    }

    /**
     * @OA\Post(
     *   path="/profiles/{uid}/upsert",
     *   tags={"profile"},
     *   summary="upsert profile, accepts:POST,PUT,PATCH",
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
    public function upsert(Request $request, $uid)
    {
        $rules = array();
        $rules = $this->vrules;
        $code  = 200;
        $this->validate($request, $rules);

        $inputs = $request->all();
        $item   = new Profile($inputs);
        if (isset($uid)) {
            $inputs['uid'] = $uid;
            $item          = $item->tableFill($uid, $inputs, 'profile');

            // if we cannot find item, insert
            if (!isset($item)) {
                $code = 201;
                $item = $item->tableCreate('profile');
            }
        } else {
            $code = 201;
            $item = $item->tableCreate('profile');
        }

        if (!$item->save()) {
            throw new GeneralException(__('exceptions.profile.upsert'));
        }

        return $this->rsp($code, $item);
    }

    /**
     * @OA\Post(
     *   path="/profiles/import",
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
     *     response="default",
     *     description=""
     *   )
     * )
     */
    public function import(Request $request)
    {
        // validate that the file import is required
        $this->validate($request, [
            'file' => 'required',
        ]);
        $file   = $request->file('file')->openFile();
        $reader = \League\Csv\Reader::createFromFileObject($file)->setHeaderOffset(0);
        $data   = $reader->fetchOne(1002);

        if (count($data) > 1001) {
            // we must improve a limit due to memory/resource restriction
            return response()->json(['error' => 'Each import must not be greater than 1000 records.'], 422);
        }

        $rst  = [];
        $self = $this;

        // wrap import in a transaction
        \DB::transaction(function () use ($data, $rst, $self) {
            foreach ($data as $row) {
                $array = array();

                // undot the csv array
                foreach ($row as $key => $value) {
                    $cell = $value;
                    if (!is_string($cell)) {
                        $cell = (string)$cell;
                    }

                    if ($cell === '' || $cell === 'null') {
                        $cell = null;
                    } elseif (is_numeric($cell)) {
                        $cell = $cell + 0;
                    }

                    array_set($array, $key, $cell);
                }

                // validate data
                $data = Validator::make($array, $this->$vrules)->validate();

                // TODO: provide better error message
                // like row number and validation error

                // get uid
                $uid  = $data['uid'];
                $item = new Profile($inputs);
                if (isset($uid)) {
                    $inputs['uid'] = $uid;
                    $item          = $item->tableFill($uid, $inputs, 'profile');

                    // if we cannot find item, insert
                    if (!isset($item)) {
                        $item = $item->tableCreate('profile');
                    }
                } else {
                    $item = $item->tableCreate('profile');
                }

                // disable audit for bulk import
                $item->no_audit = true;

                // something went wrong, error out
                if (!$item->save()) {
                    // TODO: provide better error message
                    // like row number
                    throw new GeneralException(__('exceptions.profile.import'));
                }

                $rst[] = $item;
            }
        });

        // import success response
        return response()->json(["data" = $rst], 200);
    }
}
