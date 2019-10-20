<?php

namespace hrustbb2\tests;

use hrustbb2\base\Collection;

class UsersCollection extends Collection {

    /**
     * @var UserEntity[]
     */
    private $data = [];

    public function __construct()
    {
        $keys = ['one', 'two', 'three'];
        foreach ($keys as $i=>$key){
            $this->data[$key] = new UserEntity();
            $this->data[$key]->load([
                'id' => $i,
                'name' => 'n' . $i,
                'email' => 'm@n' . $i,
            ]);
        }
    }

    protected function build($key)
    {
        return $this->data[$key];
    }

    protected function isExists($key)
    {
        return array_key_exists($key, $this->data);
    }

    protected function getKeys()
    {
        return array_keys($this->data);
    }

}