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
        'data' => 'nullable',
        'meta' => 'nullable',
        'var' => 'nullable',
    ];

    protected function validateTable($table)
    {
        // length must be greater than 3 and less than 30
        // reserved tables: profile, user, recipe
        $rules = [
            'table' => 'required|regex:/[a-z0-9]+/|min:3|max:30|not_in:profile,user,recipe'
        ];
        $this->getValidationFactory()
             ->make(['table' => $table], $rules)->validate();
    }

    public function create(Request $request, $table)
    {
        return $this->update($request, $table, null);
    }

    public function retrieve($table, $uid)
    {
        $this->validateTable($table);

        $item = new DynamicModel();
        return $item->tableFind($uid, $table);
    }

    public function delete(Request $request, $table, $uid)
    {
        $item = $this->retrieve($table, $uid);

        if ($item && !$item->delete()) {
            throw new GeneralException(__('exceptions.table.delete'));
        }
    }

    public function list(Request $request, $table)
    {
        $item = new DynamicModel();
        $item->createTableIfNotExists(tenantId(), $table);

        $qb = new RequestQueryBuilder(\DB::table($item->getTable()));
        return $qb->applyRequest($request);
    }

    public function update(Request $request, $table, $uid)
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
            throw new GeneralException(__('exceptions.table.update'));
        }

        return $item;
    }
}
