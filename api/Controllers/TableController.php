<?php

namespace Api\Controllers;

use League\Csv\Reader;
use Illuminate\Http\Request;
use App\Exceptions\GeneralException;
use Api\Extra\RequestQueryBuilder;
use Api\Controllers\Controller;
use Api\Models\DynamicModel;
use Illuminate\Validation\Validator;

class TableController extends Controller
{
    /**
     * @var array
     */
    protected $vrules = [
    ];

    protected function validateTable($table)
    {
        $rules = ['table' => 'required|regex:/[a-z0-9]+/|min:3|max:30'];
        $this->getValidationFactory()
             ->make(['table' => $table], $rules)->validate();
    }

    public function create(Request $request, $table)
    {
        return $this->update($request, $table, null);
    }

    public function retrieve($table, $id)
    {
        $this->validateTable($table);

        $item = new DynamicModel();
        return $item->tableFind($id, $table);
    }

    public function delete(Request $request, $table, $id)
    {
        $item = $this->retrieve($table, $id);

        if ($item && !$item->delete()) {
            throw new GeneralException(__('exceptions.table.delete'));
        }
    }

    public function list(Request $request, $table)
    {
        $this->validateTable($table);

        $item = new DynamicModel();
        $item->createTableIfNotExists(tenantId(), $table);
        $qb = new RequestQueryBuilder(DB::table($item->getTable()));

        return $qb->select(["*"]);
    }

    public function update(Request $request, $table, $id)
    {
        $this->validateTable($table);

        $rules = array();
        $rules = $this->vrules;
        $this->validate($request, $rules);

        $inputs = $request->all();
        $item   = new DynamicModel($inputs);
        if (isset($id)) {
            $item = $item->tableFill($id, $inputs, $table);
        } else {
            $item = $item->tableCreate($table);
        }

        if (!$item->save()) {
            throw new GeneralException(__('exceptions.table.update'));
        }

        return $item;
    }
}
