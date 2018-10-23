<?php

namespace Api\Controllers;

use League\Csv\Reader;
use Illuminate\Http\Request;
use App\Exceptions\GeneralException;
use Api\Extra\RequestQueryBuilder;
use Api\Controllers\Controller;
use Api\Models\User;

class ServiceController extends Controller
{
    /**
     * @var array
     */
    protected $vrules = [
        'id'    => 'required|integer'
    ];

    protected function validateTable($table)
    {
        $rules = ['table' => 'required|regex:/[a-z0-9]+/|min:3|max:30'];
        $this->validate($request, $rules);
    }

    public function create(Request $request, $table)
    {
        $this->validateTable();
        $this->update($request, $table, null)
    }

    public function retrieve($table, $id)
    {
        $this->validateTable();
        $this->validate($request, $this->vrules);

        $item = new DynamicModel();
        $item->createTableIfNotExists(tenantId(), $table);
        $item = \DB::table($item->getTable())->where('id', $id)->first();
        return $item;
    }

    public function delete(Request $request, $table, $id)
    {
        $this->validateTable();
        $item = $this->retrieve($table, $id);

        if ($item && !$item->delete()) {
            throw new GeneralException(__('exceptions.user.delete'));
        }
    }

    public function list(Request $request, $table)
    {
        $this->validateTable();
        $item = new DynamicModel();
        $item->createTableIfNotExists(tenantId(), $table);
        $qb = new RequestQueryBuilder(DB::table($item->getTable()));

        return $qb->select(["*"]);
    }

    public function update(Request $request, $table, $id)
    {
        $this->validateTable();

        $rules = array();
        $rules = $this->rules;
        if (!isset($id)) {
            $rules['id'] = null;
        }

        $this->validate($request, $rules);

        $inputs = $request->all();
        $item   = new DynamicModel($inputs);
        $item->createTableIfNotExists(tenantId(), $table);
        if (isset($id)) {
            $item = \DB::table($item->getTable())->where('id', $id)->first();
            $item->fill($inputs);
        }

        if (!$item->save()) {
            throw new GeneralException(__('exceptions.user.update'));
        }

        return $item
    }
}
