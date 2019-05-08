<?php
namespace Api\Controllers;

use Api\Controllers\Controller;
use Api\Models\DemoContact;
use Niiknow\Laratt\Traits\ApiTableTrait;

class DemoContactController extends Controller
{
    use ApiTableTrait;

    /**
     * @var array
     */
    protected $vrules = [
    ];

    /**
     * @return mixed
     */
    protected function dumbingDownExample()
    {
        $query         = DemoContact::query();
        $dt            = DataTables::of($query);
        $action        = $request->query('action');
        $encode        = $request->query('encode');
        $escapeColumns = $request->query('escapeColumns');

        if (!isset($encode)) {
            $dt = $dt->escapeColumns([]);
        }

        if (isset($action)) {
            if (!$request->query('length')) {
                $dt = $dt->skipPaging();
            }

            // get the table name from model
            $request->validate(['action' => 'required|in:xlsx,ods,csv']);
            $query = $dt->getFilteredQuery();
            $file  = $table . '-' . time() . '.' . $action;

            return \Maatwebsite\Excel\Facades\Excel::download(
                new TableExporter($query, $item),
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
     * @return null
     */
    protected function getTableName()
    {
        return;
    }
}
