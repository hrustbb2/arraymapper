<?php

namespace hrustbb2\arraymapper;

class ArrayProcessor {

    private $pathMap;

    private $result;

    private function buildPathMap($conf, $currentPath = [])
    {
        foreach ($conf as $key=>$field){
            if(is_array($field)){
                $prefix = $field['prefix'];
                $currentPath[$prefix] = $key;
                $this->pathMap->{$prefix} = $currentPath;
                buildPathMap($field, $this->pathMap, $currentPath);
            }
        }
    }

    public function process($conf, $array)
    {
        $this->pathMap = new \stdClass();
        $this->pathMap->{$conf['prefix']} = [];
        $this->buildPathMap($conf, $this->pathMap);
        $prefix = $conf['prefix'];
        $this->result = new \stdClass();

        foreach ($array as $line){
            $rowId = $line[$prefix . 'id'];
            if(!isset($result->{$rowId})){
                $this->result->{$rowId} = new \stdClass();
            }
            $currentRow = $this->result->{$rowId};
            $currentIds = [];
            $data = [];
            foreach ($line as $field=>$val){
                $fieldSegments = explode('_', $field);
                $fieldPrefix = array_shift($fieldSegments);
                $fieldName = join('_', $fieldSegments);
                if($fieldName == 'id'){
                    $currentIds[$fieldPrefix . '_'] = $val;
                }
                $data[$fieldPrefix][$fieldName] = $val;
            }
            foreach ($line as $field=>$val){
                $fieldSegments = explode('_', $field);
                $fieldPrefix = array_shift($fieldSegments);
                $fieldName = join('_', $fieldSegments);

                $path = $this->pathMap->{$fieldPrefix . '_'};
                $c = $currentRow;
                foreach ($path as $p=>$step){
                    if(!isset($c->{$step})){
                        $c->{$step} = new stdClass();
                    }
                    if(!isset($c->{$step}->{$currentIds[$p]})){
                        $c->{$step}->{$currentIds[$p]} = new stdClass();
                    }
                    $c = $c->{$step}->{$currentIds[$p]};
                }
                $c->{$fieldName} = $data[$fieldPrefix][$fieldName];
            }
        }

        return $this;
    }

    public function resultObj()
    {
        return $this->result;
    }

    public function resultArray()
    {
        return json_encode(json_decode($this->result));
    }

}