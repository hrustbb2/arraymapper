<?php

namespace hrustbb2\base;

abstract class Collection implements Iterator {

    private $cursor = 0;

    abstract protected function build($key);

    abstract protected function isExists($key);

    abstract protected function getKeys();

    public function rewind()
    {
        $this->cursor = 0;
    }

    public function current()
    {
        $key = $this->getKeys()[$this->cursor];
        return $this->build($key);
    }

    public function key()
    {
        return $this->cursor;
    }

    public function next()
    {
        ++$this->cursor;
    }

    public function valid()
    {
        $key = $this->getKeys()[$this->cursor];
        return $this->isExists($key);
    }

    public function offsetSet($offset, $value)
    {
        //Not impossible
    }

    public function offsetExists($key)
    {
        return $this->isExists($key);
    }

    public function offsetUnset($offset) {
        //Not impossible
    }

    public function offsetGet($key) {

        return ($this->isExists($key)) ? $this->build($key) : null;
    }

}