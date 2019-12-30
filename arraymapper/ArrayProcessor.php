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
                $this->buildPathMap($field, $currentPath);
                $currentPath = [];
            }
        }
    }

    public function process($conf, $array)
    {
        $this->pathMap = new \stdClass();
        $this->pathMap->{$conf['prefix']} = [];
        $this->buildPathMap($conf);
        $prefix = $conf['prefix'];
        $this->result = new \stdClass();

        foreach ($array as $line){
            $rowId = ((array) $line)[$prefix . 'id'];
            if(!isset($this->result->{$rowId})){
                $this->result->{$rowId} = new \stdClass();
            }
            $currentRow = $this->result->{$rowId};

            foreach ($line as $field=>$val){
                $fieldSegments = explode('_', $field);
                $fieldPrefix = array_shift($fieldSegments);
                $fieldName = join('_', $fieldSegments);

                $path = $this->pathMap->{$fieldPrefix . '_'};
                $c = $currentRow;

                foreach ($path as $p=>$step){
                    $id = $line[$p . 'id'];
                    if(!isset($c->{$step})){
                        $c->{$step} = new \stdClass();
                    }
                    if(!isset($c->{$step}->{$id})){
                        $c->{$step}->{$id} = new \stdClass();
                    }
                    $c = $c->{$step}->{$id};
                }

                if($val['id'] !== null){
                    $c->{$fieldName} = $val;
                }
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
        $str = json_encode($this->result);
        return json_decode($str, true);
    }

}