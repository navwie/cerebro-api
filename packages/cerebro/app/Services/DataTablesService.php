<?php

namespace App\Services;


class DataTablesService
{
    /**
     *  /**
     * Prepare data for Datatables component.
     * @param $data
     * @param $columns
     * @return array
     */
    public static function prepareData($data, $columns):array
    {
        $out = [];

        for ($i = 0, $ien = count($data); $i < $ien; $i++) {
            $row = [];

            for ($j = 0, $jen = count($columns); $j < $jen; $j++) {
                $column = $columns[$j];

                if (!array_key_exists($column['db'], $data[$i])) {
                    $row[$column['dt']] = $column['formatter'](null, $data[$i]);
                    continue;
                }
                // Is there a formatter?
                if (isset($column['formatter'])) {
                    $row[$column['dt']] = $column['formatter']($data[$i][$column['db']], $data[$i]);
                } else {
                    $row[$column['dt']] = $data[$i][$columns[$j]['db']];
                }
            }
            $out[] = $row;
        }

        return $out;
    }

    /**
     * Generate ORDER BY query for Datatables component.
     * @return string
     */
    public static function columnOrder($columns)
    {
        $order = '';
        if (isset($_REQUEST['order']) && count($_REQUEST['order'])) {
            $orderBy = array();
            $dtColumns = self::pluck($columns, 'dt');
            for ($i = 0, $ien = count($_REQUEST['order']); $i < $ien; $i++) {
                // Convert the column index into the column data property
                $columnIdx = intval($_REQUEST['order'][$i]['column']);
                $_REQUESTColumn = $_REQUEST['columns'][$columnIdx];
                $columnIdx = array_search($_REQUESTColumn['data'], $dtColumns);
                $column = $columns[$columnIdx];
                if ($_REQUESTColumn['orderable'] == 'true') {
                    $dir = $_REQUEST['order'][$i]['dir'] === 'asc' ?
                        'ASC' :
                        'DESC';
                    $orderBy[] = $column['db'] . ' ' . $dir;
                }
            }
            if (count($orderBy)) {
                $order = 'ORDER BY ' . implode(', ', $orderBy);
            }
        }
        return $order;
    }

    /**
     * Pluck Datatables method.
     * @return string
     */
    static function filter($request, $columns, $addWhere = true, &$bindings = [])
    {
        $requestSearch = $request->get('search');
        $requestColumns = $request->get('columns');

        $globalSearch = [];
        $columnSearch = [];
        $dtColumns = self::pluck($columns, 'dt');

        if (isset($requestSearch) && $requestSearch['value'] != '') {
            $str = $requestSearch['value'];

            for ($i = 0, $ien = count($requestColumns); $i < $ien; $i++) {
                $requestColumn = $requestColumns[$i];
                $columnIdx = array_search($requestColumn['data'], $dtColumns);
                $column = $columns[$columnIdx];

                if ($requestColumn['searchable'] == 'true') {
                    $binding = self::bind($bindings, '"%' . $str . '%"', 'VARCHAR');
                    $globalSearch[] = $column['where'] ?? $column['db'] . " LIKE " . $binding;
                }
            }
        }

        // Individual column filtering
        if (isset($requestColumns)) {
            for ($i = 0, $ien = count($requestColumns); $i < $ien; $i++) {
                $requestColumn = $requestColumns[$i];
                $columnIdx = array_search($requestColumn['data'], $dtColumns);
                $column = $columns[$columnIdx];

                $str = $requestColumn['search']['value'];

                if ( $requestColumn['searchable'] == 'true' &&
                    $str != '' ) {
                    $binding = self::bind( $bindings, '"%'.$str.'%"', 'VARCHAR' );
                    $columnSearch[] = ($column['where'] ?? $column['db']) . " LIKE " . $binding;
                }
            }
        }

        // Combine the filters into a single string
        $where = '';

        if (count($globalSearch)) {
            $where = '(' . implode(' OR ', $globalSearch) . ')';
        }

        if (count($columnSearch)) {
            $where = $where === '' ?
                implode(' AND ', $columnSearch) :
                $where . ' AND ' . implode(' AND ', $columnSearch);
        }

        if (!$addWhere) {
            return $where;
        }

        if ($where !== '') {
            $where = 'WHERE ' . $where;
        } else {
            $where = 'WHERE 1';
        }

        return $where;
    }

    /**
     * Pluck Datatables method.
     * @return array
     */
    public static function pluck($a, $prop)
    {
        $out_pluck = [];
        for ($i = 0, $len = count($a); $i < $len; $i++) {
            $out_pluck[] = $a[$i][$prop];
        }
        return $out_pluck;
    }

    /**
     * Bind Datatables method.
     * @return string
     */
    public static function bind(&$a, $val, $type)
    {
        $key = ':binding_' . count($a);
        $a[] = [
            'key' => $key,
            'val' => $val,
            'type' => $type
        ];

        return $val;
    }
}
