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
     * @param $attributeNames Массив с именами атрибутов (ключи из массива $data), которые необходимо инициировать
     * @param $data Данные для инициализации
     */
    protected function initAttributes($attributeNames, $data)
    {
        foreach ($attributeNames as $attribute){
            if(isset($data[$attribute])){
                $this->attributes[$attribute] = $data[$attribute];
            }
        }
    }

    /**
     * Обновить атрибуты
     * @param $attributeNames Массив с именами атрибутов (ключи из массива $data), которые необходимо обновить
     * @param $data
     */
    protected function updateAttributes($attributeNames, $data)
    {
        foreach ($attributeNames as $attribute){
            if(isset($data[$attribute])){
                $this->updatedAttributes[$attribute] = $data[$attribute];
            }
        }
    }

    /**
     * Установить атрибут
     * @param $attributeName Имя атрибута
     * @param $value Значение атрибута
     */
    protected function setAttribute($attributeName, $value)
    {
        $this->attributes[$attributeName] = $value;
        $this->updatedAttributes[$attributeName] = $value;
    }

    /**
     * Получить значения атрибута
     * @param $attributeName Имя атрибута
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