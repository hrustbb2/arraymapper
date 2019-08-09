<?php

namespace hrustbb2\tests;

use hrustbb2\base\Entity;

class UserEntity extends Entity {

    public function load($data)
    {
        $this->initAttributes(['id', 'name', 'email'], $data);
    }

    public function update($data)
    {
        $this->updateAttributes(['name', 'email'], $data);
    }

    public function getForUpdate()
    {
        return parent::getUpdatedAttributes(['name', 'email']);
    }

    public function getForInsert()
    {
        return parent::getAttributes([], ['id']);
    }

    public function getId()
    {
        return $this->getAttribute('id');
    }

    public function getName()
    {
        return $this->getAttribute('name');
    }

    public function setName($name)
    {
        $this->setAttribute('name', $name);
    }

    public function getEmail()
    {
        return $this->getAttribute('email');
    }

    public function setEmail($email)
    {
        $this->setAttribute('email', $email);
    }

}