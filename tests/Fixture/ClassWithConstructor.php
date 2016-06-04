<?php

namespace EasyMock\Test\Fixture;

class ClassWithConstructor
{
    public $constructorCalled = false;

    public function __construct()
    {
        $this->constructorCalled = true;
    }
}
