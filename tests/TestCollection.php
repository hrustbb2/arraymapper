<?php

namespace hrustbb2\tests;

use PHPUnit\Framework\TestCase;

class TestCollection extends TestCase {

    public function testIterator()
    {
        /** @var UserEntity[] $users */
        $users = new UsersCollection();
        $counter = 1;
        foreach ($users as $key=>$user){
            $this->assertEquals($user->getEmail(), 'm@n'.$counter);
        }
    }

    public function testArray()
    {
        /** @var UserEntity[] $users */
        $users = new UsersCollection();
        $this->assertEquals($users['one']->getName(), 'n1');
    }

    public function testCount()
    {
        /** @var UserEntity[] $users */
        $users = new UsersCollection();
        $count = count($users);
        $this->assertEquals($count, 3);
    }

}