<?php

namespace hrustbb2\arraymapper;

/**
 * Class ArrayMapper
 * @package src
 */
class ArrayMapper
{
    /**
     * @var string
     */
    private $prefix;

    /**
     * @var ArrayMapper[]
     */
    private $subMappers;

    /**
     * @var array
     */
    private $primaryKeys;

    /**
     * ArrayMapper constructor.
     * @param string $prefix
     * @param ArrayMapper[] $subMappers
     */
    public function __construct($prefix = '', $subMappers = [], $primaryKeys = ['id'])
    {
        $this->prefix = $prefix;
        $this->subMappers = $subMappers;
        $this->primaryKeys = $primaryKeys;
    }

    /**
     * @param $config
     * @return ArrayMapper
     */
    public static function build($config)
    {
        $prefix = '';
        $primaryKeys = ['id'];
        $subMappers = [];
        foreach ($config as $key=>$value){
            switch ($key) {
                case 'prefix':
                    $prefix = $value;
                    break;
                case 'primaryKeys':
                    $primaryKeys = $value;
                    break;
                default:
                    $subMappers[$key] = self::build($value);
                    break;
            }
        }
        return new self($prefix, $subMappers, $primaryKeys);
    }

    /**
     * Значения примари кейс
     * @param array $row
     * @return string
     */
    private function getPrimaryKey($row)
    {
        $prefix = $this->prefix;
        $keys = array_map(function ($k) use ($prefix, $row) {
            return $row[$prefix . $k];
        }, $this->primaryKeys);
        $filteredKeys = array_filter($keys, function($key){
            return $key !== null;
        });
        if(empty($filteredKeys)){
            return null;
        }
        return implode('.', $filteredKeys);
    }

    /**
     * Отрезает префиксы
     * @param $data
     * @return array
     */
    private function cutKeysPrefix($data)
    {
        $result = [];
        foreach ($data as $key=>$val) {
            $newKey = substr_replace($key, '', 0, strlen($this->prefix));
            $result[$newKey] = $val;
        }
        return $result;
    }

    /**
     * Достает данные к текущему сету
     * @param array $row
     * @return array
     */
    private function getMyValues($row)
    {
        $prefix = $this->prefix;
        $result = array_filter($row, function ($key) use ($prefix) {
            return strpos($key, $prefix) === 0;
        }, ARRAY_FILTER_USE_KEY);
        return $this->cutKeysPrefix($result);
    }

    /**
     * Существует ли примари кей
     * @param $row
     * @return bool
     */
    private function checkKeyExists($row)
    {
        foreach ($this->primaryKeys as $pk) {
            if (!array_key_exists($this->prefix . $pk, $row)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Метод достает подмассивы данных
     * @param array $row
     * @return array
     */
    private function getSubData($row)
    {
        $result = $this->getMyValues($row);
        foreach ($this->subMappers as $field=>$mapper) {
            $primaryKey = $mapper->getPrimaryKey($row);
            $subData = $mapper->getSubData($row);
            if ($mapper->checkKeyExists($row) && $primaryKey !== null) {
                $result[$field][$primaryKey] = $subData;
            }
        }
        return $result;
    }

    /**
     * Метод мерджит два многомерных массива
     * @param array $item
     * @param array $data
     * @return array
     */
    private function dataCombine($item, $data)
    {
        $result = $item;
        foreach ($data as $key=>$val) {
            if (is_array($val)) {
                if (!isset($result[$key])) {
                    $result[$key] = $data[$key];
                } else {
                    $result[$key] = $this->dataCombine($result[$key], $data[$key]);
                }
            }
        }
        return $result;
    }

    /**
     * @param array $array
     * @return array
     */
    public function map($array)
    {
        $result = [];
        foreach ($array as $row) {
            if(is_object($row)){
                $row = get_object_vars($row);
            }
            $primaryKey = $this->getPrimaryKey($row);
            if($primaryKey === null){
                continue;
            }
            $item = $this->getSubData($row);
            if (isset($result[$primaryKey])) {
                $result[$primaryKey] = $this->dataCombine($result[$primaryKey], $item);
            } else {
                $result[$primaryKey] = $item;
            }
        }
        return $result;
    }
}
