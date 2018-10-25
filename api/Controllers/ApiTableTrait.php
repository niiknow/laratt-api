<?php

namespace Api\Controllers;

use App\Exceptions\GeneralException;
use Api\Extra\RequestQueryBuilder;
use Api\Controllers\Controller;
use Api\Models\DynamicModel;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Validator;

use Carbon\Carbon;
use League\Csv\Reader;
use Yajra\DataTables\DataTables;

trait ApiTableTrait
{

    public function getModel($attrs = [])
    {
        return new DynamicModel($attrs);
    }

    public function getTable()
    {
        $table = request()->route('table');

        // length must be greater than 3 and less than 30
        // reserved tables: profile, user, recipe
        $rules = [
            'table' => 'required|regex:/[a-z0-9]{3,30}/|not_in:profile,user,recipe'
        ];

        $this->getValidationFactory()
             ->make(['table' => $table], $rules)
             ->validate();

        return $table;
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
    public function create(Request $request)
    {
        return $this->upsert($request, null);
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
    public function retrieve()
    {
        $table = $this->getTable();
        $uid   = $request->route('uid');
        $item  = $this->getModel()->tableFind($uid, $table);
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
    public function delete(Request $request)
    {
        $table = $this->getTable();
        $uid   = $request->route('uid');
        $item  = $this->getModel()->tableFind($uid, $table);

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
    public function list(Request $request)
    {
        $table = $this->getTable();
        $item  = $this->getModel();
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
        $table = $this->getTable();
        $item  = $this->getModel();
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
    public function upsert(Request $request)
    {
        $table = $this->getTable();
        $uid   = $request->route('uid');

        $rules = array();
        $rules = $this->vrules;
        $this->validate($request, $rules);

        $data   = $request->all();
        $inputs = array();
        foreach ($data as $key => $value) {
            array_set($inputs, $key, $value);
        }

        $item = $this->getModel($inputs);
        if (isset($uid)) {
            $input['uid'] = $uid;
            $item         = $item->tableFill($uid, $inputs, $table);

            // if we cannot find item, do insert
            if (!isset($item)) {
                $item = $this->getModel($inputs)->tableCreate($table);
            }
        } else {
            $item = $item->tableCreate($table);
        }

        if (!$item->save()) {
            throw new GeneralException(__('exceptions.tables.upsert'));
        }

        return $item;
    }

    public function processCsv($csv, &$data, $jobid)
    {
        $rowno = 0;
        $limit = config('admin.import_limit', 999);
        foreach ($csv as $row) {
            $inputs = ['job_id' => $jobid];

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
            $validator = Validator::make($inputs, $this->vrules);

            // capture and provide better error message
            if ($validator->fails()) {
                return response()->json(
                    [
                        "error" => $validator->errors(),
                        "rowno" => $rowno,
                        "row" => $inputs
                    ],
                    422
                );
            }

            $data[] = $inputs;
            if ($rowno > $limit) {
                // we must improve a limit due to memory/resource restriction
                return response()->json(
                    ['error' => "Each import must be less than $limit records"],
                    422
                );
            }
            $rowno += 1;
        }
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
    public function import(Request $request)
    {
        $table = $this->getTable();

        // validate that the file import is required
        $this->validate($request, ['file' => 'required']);

        $file = $request->file('file')->openFile();
        $csv  = \League\Csv\Reader::createFromFileObject($file)
            ->setHeaderOffset(0);

        $data  = [];
        $jobid = (string) Str::uuid();
        $rst   = $this->processCsv($csv, $data, $jobid);
        if ($rst) {
            return $rst;
        }

        $rst  = array();
        $item = $this->getModel();
        $item->createTableIfNotExists(tenantId());

        // wrap import in a transaction
        \DB::transaction(function () use ($data, &$rst, $jobid, $table) {
            $rowno = 0;
            foreach ($data as $inputs) {
                // get uid
                $uid  = isset($inputs['uid']) ? $inputs['uid'] : null;
                $item = $this->getModel($inputs);
                if (isset($uid)) {
                    $inputs['uid'] = $uid;
                    $item          = $item->tableFill($uid, $inputs, $table);

                    // if we cannot find item, insert
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
                            "row" => $item,
                            "job_id" => $jobid
                        ],
                        422
                    );

                    // throw exception to rollback transaction
                    throw new GeneralException(__('exceptions.import'));
                }

                $rst[]  = $item->toArray();
                $rowno += 1;
            }
        });

        // import success response
        $out = array_pluck($rst, 'uid');
        return response()->json(["data" => $out, "job_id" => $jobid], 200);
    }

    /**
     * @OA\Post(
     *   path="/tables/{table}/truncate",
     *   tags={"tables"},
     *   summary="delete everything from the table.  Why not?
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
    public function truncate(Request $request)
    {
        $table = $this->getTable();
        $item  = $this->getModel();
        $item->createTableIfNotExists(tenantId(), $table);

        \DB::table($item->getTable())->truncate();
        return response()->json();
    }

    /**
     * helper method to return response
     *
     * @param  integer $code the http response code
     * @param  object  $rsp  the response object
     * @return Response       the http response
     */
    public function rsp($code, $rsp = null)
    {
        if ($code == 404) {
            return response()->json([ "error" => "not found" ], 404);
        }

        if ($code == 422) {
            return response()->json([ "error" => $rsp ]);
        }

        return response()->json($rsp, $code);
    }
}
