<?php
namespace Api\Controllers;

use Api\Controllers\Controller;
use Api\Models\DemoContact;
use Illuminate\Http\Request;
use Niiknow\Laratt\TableExporter;
use Niiknow\Laratt\Traits\ApiTableTrait;
use Yajra\DataTables\DataTables;

class DemoContactController extends Controller
{
    use ApiTableTrait;

    /**
     * @var array
     */
    protected $vrules = [
        'email'      => 'required|email|max:190',
        'photo_url'  => 'nullable|url|max:190',
        'phone'      => 'nullable|string|max:190',
        'first_name' => 'nullable|string|max:190',
        'last_name'  => 'nullable|string|max:190',
        'address1'   => 'nullable|string|max:190',
        'address2'   => 'nullable|string|max:190',
        'postal'     => 'nullable|string|max:50',
        'city'       => 'nullable|string|max:190',
        'state'      => 'nullable|string|max:190',
        'country'    => 'nullable|string|max:190',
        'occupation' => 'nullable|string|max:190',
        'employer'   => 'nullable|string|max:190',
        'note'       => 'nullable|string',
        'lat'        => 'nullable|numeric',
        'lng'        => 'nullable|numeric'
    ];

    /**
     * @return null
     */
    public function drop()
    {
        return;
    }

    /**
     * @return null
     */
    public function truncate()
    {
        return;
    }

    /**
     * @OA\Get(
     *   path="/democontact/example",
     *   tags={"democontact"},
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
    protected function dumbingDownExample(Request $request)
    {
        $query = DemoContact::query();
        $dt    = DataTables::of($query);

        // this is to handle export; otherwise we only need 3 lines
        $export = $request->query('export');
        if ($export !== null) {
            if ($request->query('length') === null) {
                $dt = $dt->skipPaging();
            }

            $request->validate(['export' => 'required|in:xlsx,ods,csv']);
            $query = $dt->getFilteredQuery();
            $file  = 'contacts-' . time() . '.' . $export;

            return \Maatwebsite\Excel\Facades\Excel::download(
                new TableExporter($query, new DemoContact()),
                $file
            );
        }

        return $dt->make(true);
    }

    protected function getIdField()
    {
        return 'id';
    }

    /**
     * @param array $attrs
     */
    protected function getModel($attrs = [])
    {
        return new DemoContact($attrs);
    }

    /**
     * @OA\Post(
     *   path="/democontact/create",
     *   tags={"democontact"},
     *   summary="create demo contact",
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
     *     description="new contact object"
     *   )
     * )
     */

    /**
     * @OA\Get(
     *   path="/democontact/{uid}/retrieve",
     *   tags={"democontact"},
     *   summary="get contact",
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
     *     description="contact not found"
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="contact object"
     *   )
     * )
     */

    /**
     * @OA\Delete(
     *   path="/democontact/{uid}/delete",
     *   tags={"democontact"},
     *   summary="delete a single contact, also accept method: POST
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
     *     description="contact not found"
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="deleted contact object"
     *   )
     * )
     */

    /**
     * @OA\Get(
     *   path="/democontact/query",
     *   tags={"democontact"},
     *   summary="search or delete contact, use DELETE http method to bulk delete",
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
     *   path="/democontact/data",
     *   tags={"democontact"},
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
     *   path="/democontact/{uid}/update",
     *   tags={"democontact"},
     *   summary="update contact",
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
     *     description="new or updated contact object"
     *   )
     * )
     */

    /**
     * @OA\Post(
     *   path="/democontact/import",
     *   tags={"democontact"},
     *   summary="import csv of contacts",
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
    protected function getTableName()
    {
        return;
    }
}
