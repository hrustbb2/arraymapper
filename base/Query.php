<?php

namespace hrustbb2\base;

abstract class Query {

    protected function getSelectSection($fields, $allowNames, $tableName, $prefix)
    {
        $intersection = $this->getIntersection($fields, $allowNames);
        return array_map(function ($field) use ($tableName, $prefix) {
            return $tableName . '.' . $field . ' AS ' . $prefix . $field;
        }, $intersection);
    }

    private function getIntersection($fields, $allowNames)
    {
        return array_filter($fields, function ($field) use ($allowNames) {
            return in_array($field, $allowNames);
        });
    }

}