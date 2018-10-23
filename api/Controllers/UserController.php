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
        'email' => 'required'
    ];

    public function create(Request $request)
    {
        return $this->update($request, null);
    }

    public function retrieve($id)
    {
        $item = new User();

        $item->createTableIfNotExists(tenantId());
        $table = $item->getTable();
        $item  = User::query()->from($table)->where('id', $id)->first();
        $item->setTableName(tenantId(), 'user');
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
        $rules = $this->vrules;
        $this->validate($request, $rules);

        $inputs = $request->all();
        $item   = new User($inputs);
        if (isset($id)) {
            $item = $this->retrieve($id);
            $item->fill($inputs);
        } else {
            $item->createTableIfNotExists(tenantId());
        }

        if (!$item->save()) {
            throw new GeneralException(__('exceptions.user.update'));
        }

        return $item;
    }
}
