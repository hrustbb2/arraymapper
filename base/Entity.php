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
            if(isset($data[$attribute])){
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
            throw new \Exception('attribute already exist');
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
            if(!isset($this->attributes[$attribute])){
                throw new \Exception('attribute not exist');
            }
            if(isset($data[$attribute])){
                if(in_array(typeof($data[$attribute]), $scalarTypes)){
                    if($this->attributes[$attribute] != $data[$attribute]){
                        $this->updatedAttributes[$attribute] = $data[$attribute];
                    }
                }else{
                    $this->updatedAttributes[$attribute] = $data[$attribute];
                }
            }
        }
    }

    /**
     * Установить атрибут
     * @param $attributeName string Имя атрибута
     * @param $value mixed|null Значение атрибута
     */
    protected function setAttribute($attributeName, $value)
    {
        $scalarTypes = ['boolean', 'integer', 'double', 'string', 'NULL'];
        if(in_array(typeof($value), $scalarTypes)){
            //Только в случае скалярного типа можем проверить действительно ли обновлено значение
            if(isset($this->attributes[$attributeName]) && $this->attributes[$attributeName] != $value){
                $this->attributes[$attributeName] = $value;
                $this->updatedAttributes[$attributeName] = $value;
                return;
            }
        }
        $this->attributes[$attributeName] = $value;
        $this->updatedAttributes[$attributeName] = $value;
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