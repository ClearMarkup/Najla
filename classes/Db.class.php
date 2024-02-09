<?php

use Medoo\Medoo;

class Db extends Core
{
    private $db;
    private $table;
    private $where = [];
    private $orderBy;
    private $limit;
    private $relTable;

    public function __construct()
    {
        global $config;
        $this->db = new Medoo($config->database);
    }

    private function resetState()
    {
        $this->table = null;
        $this->where = [];
        $this->orderBy = null;
        $this->limit = null;
        $this->relTable = null;
    }

    public function transaction($callback)
    {
        return $this->db->action($callback);
    }

    public function table($table)
    {
        $this->table = $table;
        return $this;
    }

    public function orderBy($column, $order = 'ASC')
    {
        if (is_array($column)) {
            $this->orderBy = $column;
        } else {
            $this->orderBy = [$column => $order];
        }
        return $this;
    }

    public function limit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    public function filter($where, $filter = null)
    {
        if (is_string($where)) {
            $where = [$where => $filter];
        }
        $this->where = $where;
        return $this;
    }




    private function prepareQuery($columns = '*', $operation = null)
    {
        if (is_string($columns)) {
            if ($columns === '*') {
                $result = '*';
            } else {
                $result = [$columns];
                if ($operation !== null) {
                    $columns = [$columns => $operation];
                } else {
                    $columns = [$columns];
                }
            }
        } else {
            $result = [];
            foreach ($columns as $key => $value) {
                if (is_callable($value)) {
                    $result[] = $key;
                } elseif (is_string($value)) {
                    $result[] = is_string($key) ? $key : $value;
                }
            }
        }

        return [$result, $columns];
    }







    public function rel($table, $where, $column)
    {
        $this->relTable = [
            'table' => $table,
            'where' => $where,
            'column' => $column
        ];
        return $this;
    }

    public function select($columns = '*', $operation = null)
    {
        list($result, $columns) = $this->prepareQuery($columns, $operation);

        if ($this->relTable !== null) {
            $relData = $this->db->select($this->relTable['table'], $this->relTable['column'], $this->relTable['where']);

            if (empty($relData)) {
                $this->resetState();
                return [];
            }
            $data = $this->db->select($this->table, $result, array_merge($this->where, [
                'id' => $relData,
                'ORDER' => $this->orderBy,
                'LIMIT' => $this->limit
            ]));
        } else {
            $data = $this->db->select($this->table, $result, array_merge($this->where, [
                'ORDER' => $this->orderBy,
                'LIMIT' => $this->limit
            ]));
        }

        if (!is_array($data)) {
            $this->resetState();
            return $data;
        }

        if ($result !== '*') {
            foreach ($data as $key => $value) {
                foreach ($value as $column => $columnValue) {
                    if (isset($columns[$column])) {
                        $operation = $columns[$column];
                        if (is_callable($operation)) {
                            $data[$key][$column] = $operation($columnValue);
                        } elseif (is_string($operation)) {
                            $operations = explode('|', $operation);
                            $data[$key][$column] = self::applyOperations($columnValue, $operations);
                        }
                    }
                }
            }
        }

        $this->resetState();
        return $data;
        exit;
    }

    public function get($columns = '*', $operation = null)
    {
        list($result, $columns) = $this->prepareQuery($columns, $operation);

        if ($this->relTable !== null) {
            $relData = $this->db->get($this->relTable['table'], $this->relTable['column'], $this->relTable['where']);

            if (empty($relData)) {
                $this->resetState();
                return null;
            }
            $data = $this->db->get($this->table, $result, array_merge($this->where, [
                'id' => $relData
            ]));
        } else {
            $data = $this->db->get($this->table, $result, $this->where);
        }

        if (!is_array($data)) {
            $this->resetState();
            return $data;
        }

        if ($result !== '*') {
            foreach ($data as $column => $columnValue) {
                if (isset($columns[$column])) {
                    $operation = $columns[$column];
                    if (is_callable($operation)) {
                        $data[$column] = $operation($columnValue);
                    } elseif (is_string($operation)) {
                        $operations = explode('|', $operation);
                        $data[$column] = self::applyOperations($columnValue, $operations);
                    }
                }
            }
        }

        if (count($data) === 1) {
            $this->resetState();
            return array_shift($data);
        } else {
            $this->resetState();
            return $data;
        }
        exit;
    }

    public function has()
    {
        if ($this->relTable !== null) {
            $relData = $this->db->get($this->relTable['table'], $this->relTable['column'], $this->relTable['where']);

            $data = $this->db->has($this->table, array_merge($this->where, [
                'id' => $relData
            ]));
        } else {
            $data = $this->db->has($this->table, $this->where);
        }

        $this->resetState();
        return $data;
        exit;
    }

    public function insert($data)
    {
        $data = $this->db->insert($this->table, $data);
        $this->resetState();
        return $data;
        exit;
    }

    public function update($data)
    {
        $data = $this->db->update($this->table, $data, $this->where);
        $this->resetState();
        return $data;
        exit;
    }

    public function delete()
    {
        $data = $this->db->delete($this->table, $this->where);
        $this->resetState();
        return $data;
        exit;
    }

    public function getIdFromSelector($tabel, $selector)
    {
        $data = $this->db->get($tabel, 'id', ['selector' => $selector]);
        return $data;
        exit;
    }
}
