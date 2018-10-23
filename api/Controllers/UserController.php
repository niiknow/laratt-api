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
        'id'    => 'required|integer'
    ];

    public function create(Request $request)
    {
        $this->update($request, null)
    }

    public function retrieve($id)
    {
        $item = new User();
        $item->createTableIfNotExists(tenantId());
        $item = \DB::table($item->getTable())->where('id', $id)->first();
        return $item;
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
        $qb = new RequestQueryBuilder(DB::table($item->getTable()));

        return $qb->select(["*"]);
    }

    public function update(Request $request, $id)
    {
        $rules = array();
        $rules = $this->rules;
        if (!isset($id)) {
            $rules['id'] = null;
        }

        $this->validate($request, $this->rules);

        $inputs = $request->all();
        $item   = new User($inputs);
        $item->createTableIfNotExists(tenantId());
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
