<?php

namespace Api\Extra;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Class RequestQueryBuilder
 * @package src
 */
class RequestQueryBuilder
{
    /**
     * @var Builder
     */
    protected $builder;

    /**
     * Maps a query column to the underlying table field.
     * @var array
     */
    protected $maps;

    /**
     * select columns
     * @var array
     */
    public $columns = ["*"];

    /**
     * QueryBuilder constructor.
     * @param Builder $builder
     * @param array $maps
     */
    public function __construct($builder, array $maps = array())
    {
        $this->builder = $builder;
        $this->maps    = $maps;
    }

    /**
     * Applies the request's queries to the builder.
     * @param Request $request
     * @return Builder
     */
    public function applyRequest(Request $request)
    {
        $this->applyRequestColumns($request);
        $this->applyRequestFilters($request);
        $this->applyRequestSorts($request);

        $limit = $request->query('limit');
        if ($limit && is_numeric($limit)) {
            $limit = intVal($limit);
        } else {
            $limit = 15;
        }

        $page = $request->query('page');
        if ($page && is_numeric($page)) {
            $page = intVal($page);
        } else {
            $page = 1;
        }

        if ($request->isMethod('delete')) {
            return $this->builder->delete();
        }

        // \Log::info($qb->columns);
        return $this->builder->paginate(
            $limit,
            $this->columns,
            'page',
            $page
        );
    }

    public function applyRequestColumns(Request $request)
    {
        $sel = $request->query('select');
        if (isset($sel)) {
            // do not allow caps in column name
            $sel     = mb_strtolower(sel);
            $pattern = '/[^a-z0-9_]+/i';
            $cols    = collect(explode(",", $sel))->map(function ($col) {
                // sanitize column name
                return trim(preg_replace($pattern, '', $col));
            })->filter(function ($col) {
                // only return valid column name
                return strlen($col) > 1;
            });

            // finally set column as array
            $this->columns = $cols->toArray();
        }

        if (count($this->columns) <= 0) {
            $this->columns = ["*"];
        }
    }

    /**
     * Applies the request's sort queries to the builder.
     * Sort queries are of the following format: ?sort[]=column:direction(asc|desc)
     * @param Request $request
     * @return Builder
     */
    public function applyRequestSorts(Request $request)
    {
        if (!($sort = $request->query('sort'))) {
            return $this->builder;
        }

        foreach ((is_array($sort) ? $sort : array($sort)) as $item) {
            $this->applyRequestSort($item);
        }

        return $this->builder;
    }

    /**
     * Applies the request's sort query to the builder.
     * @param string $sort
     * @return Builder
     */
    protected function applyRequestSort($sort)
    {
        list($column, $direction) = array_pad(
            explode(':', trim($sort), 2),
            2,
            null
        );

        $column = trim($column);
        $column = array_key_exists($column, $this->maps) ? $this->maps[$column] : $column;

        $direction = strtolower(trim($direction));

        if (empty($column) or !in_array($direction, array('asc', 'desc'))) {
            return $this->builder;
        }

        return $this->builder->orderBy($column, $direction);
    }

    /**
     * Applies the request's filter queries to the builder.
     * Filter queries are of the following format:
     *  - Equals: ?filter[]=column:eq:value
     *  - Not equals: ?filter[]=column:neq:value
     *  - Greater than: ?filter[]=column:gt:value
     *  - Greater than or equal: ?filter[]=column:gte:value
     *  - Less than: ?filter[]=column:lt:value
     *  - Less than or equal: ?filter[]=column:lte:value
     *  - Contains: ?filter[]=column:ct:value
     *  - Does not contains: ?filter[]=column:nct:value
     *  - Starts with: ?filter[]=column:sw:value
     *  - Does not start with: ?filter[]=column:nsw:value
     *  - Ends with: ?filter[]=column:ew:value
     *  - Does not end with: ?filter[]=column:new:value
     *  - Between: ?filter[]=column:bt:value1|value2
     *  - Not between: ?filter[]=column:nbt:value1|value2
     *  - In: ?filter[]=column:in:value1|value2|value3|...
     *  - Not in: ?filter[]=column:nin:value1|value2|value3|...
     *  - Null: ?filter[]=column:nl
     *  - Not null: ?filter[]=column:nnl
     *
     * Or queries are of the following format: ?filter[]=column:operator:value,column:operator:value,...
     * And queries are of the following format: ?filter[]=column:operator:value&filter[]=column:operator:value
     * Values should be 'rawurlencoded' if they contain special characters.
     * @param Request $request
     * @return Builder
     */
    public function applyRequestFilters(Request $request)
    {
        if (!($filter = $request->query('filter'))) {
            return $this->builder;
        }

        foreach ((is_array($filter) ? $filter : array($filter)) as $item) {
            $orFilters = explode(',', $item);

            for ($i = 0; $i < count($orFilters); $i++) {
                if ($i === 0) { // Since it's the first filter, apply an 'and' clause.
                    $this->applyRequestFilter($orFilters[$i]);
                } else { // Since it's not the first filter, apply an 'or' clause.
                    $this->applyRequestFilter($orFilters[$i], 'or');
                }
            }
        }

        return $this->builder;
    }

    /**
     * Applies the request's filter query to the builder.
     * @param string $filter
     * @param string $clause The clause to apply to the builder: and|or
     * @return Builder
     */
    // phpcs:ignore
    protected function applyRequestFilter($filter, $clause = 'and')
    {
        list($column, $operator, $value) = array_pad(
            explode(':', trim($filter), 3),
            3,
            null
        );

        $column = trim($column);
        $column = array_key_exists($column, $this->maps) ? $this->maps[$column] : $column;

        $operator = strtolower(trim($operator));
        $value    = trim(rawurldecode($value));

        if (empty($column)) {
            return $this->builder;
        }

        if ('eq' === $operator and 'and' === $clause) {
            $this->builder->where($column, '=', $value);
        } elseif ('eq' === $operator) {
            $this->builder->orWhere($column, '=', $value);
        }

        if ('neq' === $operator and 'and' === $clause) {
            $this->builder->where($column, '<>', $value);
        } elseif ('neq' === $operator) {
            $this->builder->orWhere($column, '<>', $value);
        }

        if ('gt' === $operator and 'and' === $clause) {
            $this->builder->where($column, '>', $value);
        } elseif ('gt' === $operator) {
            $this->builder->orWhere($column, '>', $value);
        }

        if ('gte' === $operator and 'and' === $clause) {
            $this->builder->where($column, '>=', $value);
        } elseif ('gte' === $operator) {
            $this->builder->orWhere($column, '>=', $value);
        }

        if ('lt' === $operator and 'and' === $clause) {
            $this->builder->where($column, '<', $value);
        } elseif ('lt' === $operator) {
            $this->builder->orWhere($column, '<', $value);
        }

        if ('lte' === $operator and 'and' === $clause) {
            $this->builder->where($column, '<=', $value);
        } elseif ('lte' === $operator) {
            $this->builder->orWhere($column, '<=', $value);
        }

        if ('ct' === $operator and 'and' === $clause) {
            $this->builder->where($column, 'like', "%{$value}%");
        } elseif ('ct' === $operator) {
            $this->builder->orWhere($column, 'like', "%{$value}%");
        }

        if ('nct' === $operator and 'and' === $clause) {
            $this->builder->where($column, 'not like', "%{$value}%");
        } elseif ('nct' === $operator) {
            $this->builder->orWhere($column, 'not like', "%{$value}%");
        }

        if ('sw' === $operator and 'and' === $clause) {
            $this->builder->where($column, 'like', "{$value}%");
        } elseif ('sw' === $operator) {
            $this->builder->orWhere($column, 'like', "{$value}%");
        }

        if ('nsw' === $operator and 'and' === $clause) {
            $this->builder->where($column, 'not like', "{$value}%");
        } elseif ('nsw' === $operator) {
            $this->builder->orWhere($column, 'not like', "{$value}%");
        }

        if ('ew' === $operator and 'and' === $clause) {
            $this->builder->where($column, 'like', "%{$value}");
        } elseif ('ew' === $operator) {
            $this->builder->orWhere($column, 'like', "%{$value}");
        }

        if ('new' === $operator and 'and' === $clause) {
            $this->builder->where($column, 'not like', "%{$value}");
        } elseif ('new' === $operator) {
            $this->builder->orWhere($column, 'not like', "%{$value}");
        }

        if ('bt' === $operator and 'and' === $clause) {
            $this->builder->whereBetween($column, explode('|', $value));
        } elseif ('bt' === $operator) {
            $this->builder->orWhereBetween($column, explode('|', $value));
        }

        if ('nbt' === $operator and 'and' === $clause) {
            $this->builder->whereNotBetween($column, explode('|', $value));
        } elseif ('nbt' === $operator) {
            $this->builder->orWhereNotBetween($column, explode('|', $value));
        }

        if ('in' === $operator and 'and' === $clause) {
            $this->builder->whereIn($column, explode('|', $value));
        } elseif ('in' === $operator) {
            $this->builder->orWhereIn($column, explode('|', $value));
        }

        if ('nin' === $operator and 'and' === $clause) {
            $this->builder->whereNotIn($column, explode('|', $value));
        } elseif ('nin' === $operator) {
            $this->builder->orWhereNotIn($column, explode('|', $value));
        }

        if ('nl' === $operator and 'and' === $clause) {
            $this->builder->whereNull($column);
        } elseif ('nl' === $operator) {
            $this->builder->orWhereNull($column);
        }

        if ('nnl' === $operator and 'and' === $clause) {
            $this->builder->whereNotNull($column);
        } elseif ('nnl' === $operator) {
            $this->builder->orWhereNotNull($column);
        }

        return $this->builder;
    }
}
