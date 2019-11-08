<?php

namespace hrustbb2\base;

abstract class Entity {

    /**
     * @var array Все атрибуты
     */
    private $attributes = [];

    /**
     * @var array Обновленные атрбуты
     */
    private $updatedAttributes = [];

    /**
     * Инициировать начальные значения атрибутов
     * @param $attributeNames array Массив с именами атрибутов (ключи из массива $data), которые необходимо инициировать
     * @param $data array Данные для инициализации
     * @throws \Exception
     */
    protected function initAttributes($attributeNames, $data)
    {
        foreach ($attributeNames as $attribute){
            if(key_exists($attribute, $data)){
                $this->initAttribute($attribute, $data[$attribute]);
            }
        }
    }

    /**
     * @param $attributeName
     * @param $value
     * @throws \Exception
     */
    protected function initAttribute($attributeName, $value)
    {
        if(key_exists($attributeName, $this->attributes)){
            throw new \Exception('attribute '.$attributeName.' already exist');
        }
        $this->attributes[$attributeName] = $value;
    }

    /**
     * Обновить атрибуты
     * @param $attributeNames array Массив с именами атрибутов (ключи из массива $data), которые необходимо обновить
     * @param $data
     * @throws \Exception
     */
    protected function updateAttributes($attributeNames, $data)
    {
        $scalarTypes = ['boolean', 'integer', 'double', 'string', 'NULL'];
        foreach ($attributeNames as $attribute){
            if(key_exists($attribute, $data)){
                if(!key_exists($attribute, $this->attributes)){
                    $this->setAttribute($attribute, $data[$attribute], true);
                }else{
                    if(in_array(gettype($data[$attribute]), $scalarTypes)){
                        if($this->attributes[$attribute] != $data[$attribute]){
                            $this->updatedAttributes[$attribute] = $data[$attribute];
                        }
                    }else{
                        $this->updatedAttributes[$attribute] = $data[$attribute];
                    }
                }
            }
        }
    }

    /**
     * Установить атрибут
     * @param $attributeName string Имя атрибута
     * @param $value mixed|null Значение атрибута
     * @param $asUpdated boolean Установить как обновленный
     */
    protected function setAttribute($attributeName, $value, $asUpdated = false)
    {
        if(key_exists($attributeName, $this->attributes)){
            if(! $this->compareAttributes($this->attributes[$attributeName], $value)){
                $this->updatedAttributes[$attributeName] = $value;
            }
        }else{
            $this->attributes[$attributeName] = $value;
            if($asUpdated){
                $this->updatedAttributes[$attributeName] = $value;
            }
        }
    }

    /**
     * Сравнивает два атрибута
     * @param $a
     * @param $b
     * @return bool
     */
    private function compareAttributes($a, $b)
    {
        $scalarTypes = ['boolean', 'integer', 'double', 'string', 'NULL'];
        if(in_array(gettype($a), $scalarTypes) && in_array(gettype($b), $scalarTypes)){
            return $a == $b;
        }
        return false;
    }

    /**
     * Если атрибут является массивом, то добавляет значение в конец этого массива
     * @param $attributeName
     * @param $value
     * @throws \Exception
     */
    protected function appendAttribute($attributeName, $value)
    {
        if(key_exists($attributeName, $this->attributes) && !is_array($this->attributes[$attributeName])){
            throw new \Exception('attribute '.$attributeName.' is not array');
        }
        $this->attributes[$attributeName][] = $value;
        $this->updatedAttributes[$attributeName][] = $value;
    }

    /**
     * @param $attributeName
     * @param $index
     * @return mixed
     * @throws \Exception
     */
    protected function getArrayItem($attributeName, $index)
    {
        if(!is_array($this->attributes[$attributeName])){
            throw new \Exception('attribute '.$attributeName.' is not array');
        }
        return $this->attributes[$attributeName][$index];
    }

    /**
     * @param $attributeName
     * @param $value
     * @param $index
     * @throws \Exception
     */
    protected function setArrayItem($attributeName, $value, $index)
    {
        if(!is_array($this->attributes[$attributeName])){
            throw new \Exception('attribute '.$attributeName.' is not array');
        }
        if(!key_exists($index, $this->attributes[$attributeName])){
            throw new \Exception('index '.$index.' in attribute '.$attributeName.' not exist');
        }
        $this->attributes[$attributeName][$index] = $value;
        $this->updatedAttributes[$attributeName][$index] = $value;
    }

    /**
     * Получить значения атрибута
     * @param $attributeName string Имя атрибута
     * @return mixed|null
     */
    protected function getAttribute($attributeName)
    {
        return $this->updatedAttributes[$attributeName] ?? $this->attributes[$attributeName] ?? null;
    }

    /**
     * @param $attributeName
     * @return mixed|null
     */
    protected function getCleanAttribute($attributeName)
    {
        return $this->attributes[$attributeName] ?? null;
    }

    /**
     * @param $attributeName
     * @return mixed|null
     */
    protected function getUpdatedAttribute($attributeName)
    {
        return $this->updatedAttributes[$attributeName] ?? null;
    }

    /**
     * Получить только обновленные атрибуты
     * @param array $attrs Имена атрибутов (если пустой, то все)
     * @param array $exclude Имена атрибутов, которые необходимо исключить
     * @return array
     */
    protected function getUpdatedAttributes($attrs = [], $exclude = [])
    {
        return $this->attributesFilter($this->updatedAttributes, $attrs, $exclude);
    }

    /**
     * Получить все атрибуты
     * @param array $attrs Имена атрибутов (если пустой, то все)
     * @param array $exclude Имена атрибутов, которые необходимо исключить
     * @return array
     */
    protected function getAttributes($attrs = [], $exclude = [])
    {
        $result = $this->updatedAttributes;
        $keys = array_keys($result);
        foreach ($this->attributes as $key=>$attribute){
            if(!in_array($key, $keys)){
                $result[$key] = $attribute;
                $keys[] = $key;
            }
        }
        return $this->attributesFilter($result, $attrs, $exclude);
    }

    private function attributesFilter($attrs, $includeKeys = [], $excludeKeys = [])
    {
        if(!empty($includeKeys)){
            $result = array_filter($attrs, function ($updatedAttrName) use ($includeKeys) {
                return in_array($updatedAttrName, $includeKeys);
            }, ARRAY_FILTER_USE_KEY);
        }else {
            $result = $attrs;
        }
        if(!empty($excludeKeys)){
            $result = array_filter($result, function ($updatedAttrName) use ($excludeKeys) {
                return !in_array($updatedAttrName, $excludeKeys);
            }, ARRAY_FILTER_USE_KEY);
        }
        return $result;
    }

}