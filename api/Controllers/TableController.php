<?php

namespace Api\Controllers;

use League\Csv\Reader;
use Illuminate\Http\Request;
use App\Exceptions\GeneralException;
use Api\Extra\RequestQueryBuilder;
use Api\Controllers\Controller;
use Api\Models\DynamicModel;
use Yajra\DataTables\DataTables;
use Validator;

class TableController extends Controller
{
    use ApiResponseTrait;
    /**
     * @var array
     */
    protected $vrules = [
        'uid' => 'nullable|string|max:190',
        'name' => 'nullable|string|max:190',
        'label' => 'nullable|string|max:190',
        'teaser' => 'nullable|string|max:190',
        'group' => 'nullable|string|max:190',
        'started_at' => 'nullable|date|date_format:Y-m-d',
        'ended_at' => 'nullable|date|date_format:Y-m-d',
        'priority' => 'nullable|integer|max:32000',
        'title' => 'nullable|string|max:190',
        'summary' => 'nullable|string|max:190',
        'image_url' => 'nullable|url|max:190',
        'geos' => 'nullable|string|max:190',
        'keywords' => 'nullable|string|max:190',
        'tags' => 'nullable|string|max:190',
        'hostnames' => 'nullable|string|max:190',
        'week_schedules' => 'nullable|url|max:190',
        'analytic_code' => 'nullable|url|max:190',
        'imp_pixel' => 'nullable|url|max:190',
        'msrp' => 'nullable|integer',
        'price' => 'nullable|integer',
        'sale_price' => 'nullable|integer',
        'sale_qty' => 'nullable|integer|max:32000',
        'skus' => 'nullable|string|max:190',
        'gtins' => 'nullable|string|max:190',
        'brands' => 'nullable|string|max:190',
        'cat1' => 'nullable|string|max:190',
        'cat2' => 'nullable|string|max:190',
        'cat3' => 'nullable|string|max:190',
        'cat4' => 'nullable|string|max:190',
        'map_coords' => 'nullable|string|max:190',

        'clk_url' => 'nullable|url|max:500',
        'content' => 'nullable|string',
        'data.*' => 'nullable',
        'meta.*' => 'nullable',
        'var.*' => 'nullable',
    ];

    protected function validateTable($table)
    {
        // length must be greater than 3 and less than 30
        // reserved tables: profile, user, recipe
        $rules = [
            'table' => 'required|regex:/[a-z0-9]{3,30}/|not_in:profile,user,recipe'
        ];
        $this->getValidationFactory()
             ->make(['table' => $table], $rules)->validate();
    }

    /**
     * @OA\Post(
     *   path="/tables/{table}/create",
     *   tags={"tables"},
     *   summary="create new object and store in {table}",
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
     *     name="table",
     *     in="path",
     *     description="specified table name",
     *     required=true,
     *     @OA\Schema(
     *       type="string"
     *     ),
     *     style="form"
     *   ),
     *   @OA\Response(
     *     response=422,
     *     description="invalid table name"
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="new object in specified {table}"
     *   )
     * )
     */
    public function create(Request $request, $table)
    {
        return $this->upsert($request, $table, null);
    }


    /**
     * @OA\Get(
     *   path="/tables/{table}/{uid}/retrieve",
     *   tags={"tables"},
     *   summary="get object of specified table",
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
     *     name="table",
     *     in="path",
     *     description="specified table name",
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
     *     description="object not found"
     *   ),
     *   @OA\Response(
     *     response=422,
     *     description="invalid table name"
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="found object"
     *   )
     * )
     */
    public function retrieve($table, $uid)
    {
        $this->validateTable($table);

        $item = (new DynamicModel())->tableFind($uid, $table);
        return $this->rsp(isset($item) ? 200 : 404, $item);
    }

    /**
     * @OA\Delete(
     *   path="/tables/{table}/{uid}/delete",
     *   tags={"tables"},
     *   summary="delete object of specified table, also accept method: POST
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
     *     name="table",
     *     in="path",
     *     description="specified table name",
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
     *     description="object not found"
     *   ),
     *   @OA\Response(
     *     response=422,
     *     description="invalid table name"
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="deleted object"
     *   )
     * )
     */
    public function delete(Request $request, $table, $uid)
    {
        $this->validateTable($table);

        $item = (new DynamicModel())->tableFind($uid, $table);

        if ($item && !$item->delete()) {
            throw new GeneralException(__('exceptions.tables.delete'));
        }

        return $this->rsp(isset($item) ? 200 : 404, $item);
    }

    /**
     * @OA\Get(
     *   path="/tables/{table}/list",
     *   tags={"tables"},
     *   summary="search or delete a table, use DELETE http method to bulk delete",
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
     *     name="table",
     *     in="path",
     *     description="specified table name",
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
     *     response=422,
     *     description="invalid table name"
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Eloquent builder paginated json"
     *   )
     * )
     */
    public function list(Request $request, $table)
    {
        $this->validateTable($table);

        $item = new DynamicModel();
        $item->createTableIfNotExists(tenantId(), $table);

        $qb = new RequestQueryBuilder(\DB::table($item->getTable()));
        return $qb->applyRequest($request);
    }

    /**
     * @OA\Get(
     *   path="/tables/{table}/data",
     *   tags={"tables"},
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
     *   @OA\Parameter(
     *     name="table",
     *     in="path",
     *     description="specified table name",
     *     required=true,
     *     @OA\Schema(
     *       type="string"
     *     ),
     *     style="form"
     *   ),
     *   @OA\Response(
     *     response=422,
     *     description="invalid table name"
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="jQuery DataTable json"
     *   )
     * )
     */
    public function data(Request $request)
    {
        $this->validateTable($table);

        $item = new DynamicModel();
        $item->createTableIfNotExists(tenantId(), $table);

        return DataTables::of(\DB::table($item->getTable()))->make(true);
    }

    /**
     * @OA\Post(
     *   path="/tables/{table}/{uid}/upsert",
     *   tags={"tables"},
     *   summary="upsert object of specified table",
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
     *     name="table",
     *     in="path",
     *     description="specified table name",
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
     *     response=422,
     *     description="invalid table name"
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="new or updated object of specified table"
     *   )
     * )
     */
    public function upsert(Request $request, $table, $uid)
    {
        $this->validateTable($table);

        $rules = array();
        $rules = $this->vrules;
        $this->validate($request, $rules);

        $inputs = $request->all();
        $item   = new DynamicModel($inputs);
        if (isset($uid)) {
            $input['uid'] = $uid;
            $item         = $item->tableFill($uid, $inputs, $table);

            // if we cannot find item, do insert
            if (!isset($item)) {
                $item = $item->tableCreate($table);
            }
        } else {
            $item = $item->tableCreate($table);
        }

        if (!$item->save()) {
            throw new GeneralException(__('exceptions.tables.upsert'));
        }

        return $item;
    }

    /**
     * @OA\Post(
     *   path="/tables/{table}/import",
     *   tags={"tables"},
     *   summary="import csv of object",
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
     *     name="table",
     *     in="path",
     *     description="specified table name",
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
    public function import(Request $request, $table)
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
        \DB::transaction(function () use ($data, $rst, $table, $self) {
            $rowno = 0;
            foreach ($data as $row) {
                $inputs = array();

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

                    // undot array
                    array_set($inputs, $key, $cell);
                }

                // validate data
                $validator = Validator::make($inputs, $this->$vrules);

                // capture and provide better error message
                if ($validator->fails()) {
                    response()->json(
                        [
                            "error" => $validator->errors(),
                            "rowno" => $rowno,
                            "row" => $inputs
                        ],
                        422
                    );

                    // throw exception to rollback transaction
                    throw new GeneralException(__('exceptions.profile.import'));
                }

                // get uid
                $uid  = $inputs['uid'];
                $item = new DynamicModel($inputs);
                if (isset($uid)) {
                    $input['uid'] = $uid;
                    $item         = $item->tableFill($uid, $inputs, $table);

                    // if we cannot find item, do insert
                    if (!isset($item)) {
                        $item = $item->tableCreate($table);
                    }
                } else {
                    $item = $item->tableCreate($table);
                }

                // disable audit for bulk import
                $item->no_audit = true;

                // something went wrong, error out
                if (!$item->save()) {
                    response()->json(
                        [
                            "error" => "Error while attempting to import row",
                            "rowno" => $rowno,
                            "row" => $item
                        ],
                        422
                    );

                    // throw exception to rollback transaction
                    throw new GeneralException(__('exceptions.profile.import'));
                }

                $rst[] = $item;
            }
        });

        // import success response
        return response()->json(["data" => array_only($rst, ['uid'])], 200);
    }


    /**
     * @OA\Post(
     *   path="/tables/{table}/truncate",
     *   tags={"tables"},
     *   summary="delete everything from the table, why not?",
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
     *     name="table",
     *     in="path",
     *     description="specified table name",
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
    public function truncate(Request $request, $table)
    {
        $this->validateTable($table);

        $item = new DynamicModel();
        $item->createTableIfNotExists(tenantId(), $table);

        return \DB::table($item->getTable())->truncate();
    }
}
