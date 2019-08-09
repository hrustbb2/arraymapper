<?php

namespace hrustbb2\tests;

use PHPUnit\Framework\TestCase;

class TestEntity extends TestCase {

    public function testLoad()
    {
        $user = new UserEntity();
        $userData = [
            'id' => 1,
            'name' => 'name',
            'email' => 'email@mail.ru'
        ];
        $user->load($userData);

        $this->assertEquals($user->getEmail(), 'email@mail.ru');
    }

    public function testUpdate()
    {
        $user = new UserEntity();
        $userData = [
            'id' => 1,
            'name' => 'name',
            'email' => 'email@mail.ru'
        ];
        $user->load($userData);

        $user->setName('name2');

        $this->assertEquals($user->getForUpdate(), ['name' => 'name2']);
    }

    public function testGet()
    {
        $user = new UserEntity();
        $userData = [
            'id' => 1,
            'name' => 'name',
            'email' => 'email@mail.ru'
        ];
        $user->load($userData);

        $user->setName('name2');

        $this->assertEquals($user->getName(), 'name2');
    }

}