<?php

namespace EasyMock\Test;

use EasyMock\EasyMock;
use EasyMock\Test\Fixture\ClassFixture;

/**
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class MockClassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function should_mock_objects()
    {
        $mock = EasyMock::mock('EasyMock\Test\Fixture\ClassFixture');

        $this->assertInstanceOf('PHPUnit_Framework_MockObject_MockObject', $mock);
        $this->assertNull($mock->foo());
    }

    /**
     * @test
     */
    public function should_mock_interfaces()
    {
        $mock = EasyMock::mock('EasyMock\Test\Fixture\InterfaceFixture');

        $this->assertInstanceOf('PHPUnit_Framework_MockObject_MockObject', $mock);
        $this->assertNull($mock->foo());
    }

    /**
     * @test
     */
    public function should_mock_existing_method_with_a_value()
    {
        /** @var ClassFixture $mock */
        $mock = EasyMock::mock('EasyMock\Test\Fixture\ClassFixture', array(
            'foo' => 'bar',
        ));

        $this->assertSame('bar', $mock->foo());
    }

    /**
     * @test
     */
    public function should_mock_existing_method_with_a_callback()
    {
        /** @var ClassFixture $mock */
        $mock = EasyMock::mock('EasyMock\Test\Fixture\ClassFixture', array(
            'foo' => function () {
                return 'bar';
            },
        ));

        $this->assertSame('bar', $mock->foo());
    }

    /**
     * @test
     */
    public function not_mocked_methods_should_return_null()
    {
        /** @var ClassFixture $mock */
        $mock = EasyMock::mock('EasyMock\Test\Fixture\ClassFixture');

        $this->assertSame(null, $mock->foo());
    }
}
