<?php
namespace Api\Controllers;

use Api\Controllers\Controller;
use Niiknow\Laratt\Traits\ApiTableTrait;

class TableController extends Controller
{
    use ApiTableTrait;

    /**
     * @var mixed
     */
    public $tableName;

    /**
     * @var array
     */
    protected $vrules = [
        'uid'        => 'nullable|string|max:50',
        'started_at' => 'nullable|date|date_format:Y-m-d',
        'ended_at'   => 'nullable|date|date_format:Y-m-d',
        'private.*'  => 'nullable',
        'public.*'   => 'nullable'
    ];

    /**
     * @return mixed
     */
    public function getTableName()
    {
        return $this->tableName;
    }
}
