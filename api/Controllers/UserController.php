<?php

namespace Api\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\GeneralException;
use Api\Extra\RequestSearchQuery;
use Api\Controllers\Controller;
use Api\Models\User;

class UserController extends Controller
{
    public function destroy(Request $request, User $model)
    {
        // user cannot delete themself
        $self = $request->user();
        if ($self->id == $model->id) {
            throw new GeneralException(__('exceptions.user.delete_self'));
        }

        if (!$model->delete()) {
            throw new GeneralException(__('exceptions.user.delete'));
        }
    }

    public function index(Request $request)
    {
        $requestSearchQuery = new RequestSearchQuery($request, User::query(), [
            'id',
            'first_name',
            'last_name',
            'email'
        ]);

        return $requestSearchQuery->result();
    }

    public function restore($id)
    {
        User::withTrashed()->findOrFail($id)->restore();
    }

    public function store(Request $request)
    {
        $this->validate($request, ['name' => 'required|max:255',]);

        $inputs = $request->all();
        $item   = User::create($inputs);
        return $item;
    }

    public function show(User $model)
    {
        return $model->load('contact', 'organization');
    }

    public function update(Request $request, User $model)
    {
        // $this->authorize('update', $model);
        $this->validate($request, [
            'name' => 'required|max:255'
        ]);

        $inputs = $request->all();
        $model->fill($inputs);

        if (!$model->save()) {
            throw new GeneralException(__('exceptions.user.update'));
        }
    }

    public function bulk(Request $request)
    {
        $this->validate($request, [
            'action' => 'required',
            'selected' => 'required'
        ]);

        // do nothing for now
        $ids = $request->input('selected');

        // use switch statement to call different method
        $action = $request->input('action');
    }

    public function listTeam(Request $request, User $model)
    {
        return response()->json(['data' => $model->teams]);
    }

    public function updatePhoto(Request $request, User $model, $fileParam = 'file')
    {
        return response()->json($model->uploadUserFile($request, $fileParam));
    }
}
