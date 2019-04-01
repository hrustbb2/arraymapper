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
     * ArrayMapper constructor.
     * @param string $prefix
     * @param ArrayMapper[] $subMappers
     */
    public function __construct($prefix = '', $subMappers = [])
    {
        $this->prefix = $prefix;
        $this->subMappers = $subMappers;
    }

    /**
     * @return string
     */
    private function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param array $row
     * @return array
     */
    private function getMyValues($row)
    {
        $prefix = $this->prefix;
        $result = array_filter($row, function ($val, $key) use ($prefix) {
            return strpos($key, $prefix) === 0;
        }, ARRAY_FILTER_USE_BOTH);
        $r = array_map(function ($val) use ($prefix) {
            return substr_replace($val, '', 0, strlen($prefix));
        }, array_flip($result));
        return array_flip($r);
    }

    /**
     * @param array $row
     * @return array
     */
    private function getSubData($row)
    {
        $result = $this->getMyValues($row);
        foreach ($this->subMappers as $field=>$mapper) {
            if (isset($row[$mapper->getPrefix() . 'id'])) {
                $result[$field][$row[$mapper->getPrefix() . 'id']] = $mapper->getSubData($row);
            }
        }
        return $result;
    }

    /**
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
            $primaryKey = $row[$this->getPrefix() . 'id'];
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
