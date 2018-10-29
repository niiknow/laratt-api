<?php

namespace Api\Controllers;

use League\Csv\Reader;
use Illuminate\Http\Request;
use App\Exceptions\GeneralException;
use Api\Extra\RequestQueryBuilder;
use Api\Controllers\Controller;
use Yajra\DataTables\DataTables;
use Validator;

use Niiknow\Laratt\Traits\ApiTableTrait;

class TableController extends Controller
{
    use ApiTableTrait;

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

    /**
     * @OA\Post(
     *   path="/tables/{table}/drop",
     *   tags={"tables"},
     *   summary="drop the table.  Why not?"
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
}
