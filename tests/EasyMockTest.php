<?php

namespace EasyMock\Test;

use EasyMock\EasyMock;
use EasyMock\Test\Fixture\ClassFixture;
use EasyMock\Test\Fixture\ClassWithConstructor;
use EasyMock\Test\Fixture\CustomException;
use EasyMock\Test\Fixture\InterfaceFixture;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\ExpectationFailedException;

/**
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class EasyMockTest extends TestCase
{
    use EasyMock;

    /**
     * @test
     */
    public function should_mock_objects()
    {
        /** @var ClassFixture $mock */
        $mock = $this->easyMock('EasyMock\Test\Fixture\ClassFixture');

        $this->assertInstanceOf('PHPUnit\Framework\MockObject\MockObject', $mock);
        $this->assertNull($mock->foo());
    }

    /**
     * @test
     */
    public function should_skip_the_constructor()
    {
        /** @var ClassWithConstructor $mock */
        $mock = $this->easyMock('\EasyMock\Test\Fixture\ClassWithConstructor');
        $this->assertFalse($mock->constructorCalled);
    }

    /**
     * @test
     */
    public function should_mock_interfaces()
    {
        /** @var InterfaceFixture $mock */
        $mock = $this->easyMock('EasyMock\Test\Fixture\InterfaceFixture');

        $this->assertInstanceOf('PHPUnit\Framework\MockObject\MockObject', $mock);
        $this->assertNull($mock->foo());
    }

    /**
     * @test
     */
    public function not_mocked_methods_should_return_null()
    {
        /** @var ClassFixture $mock */
        $mock = $this->easyMock('EasyMock\Test\Fixture\ClassFixture');

        $this->assertNull($mock->foo());
    }

    /**
     * @test
     */
    public function should_mock_existing_method_with_a_value()
    {
        /** @var ClassFixture $mock */
        $mock = $this->easyMock('EasyMock\Test\Fixture\ClassFixture', array(
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
        $mock = $this->easyMock('EasyMock\Test\Fixture\ClassFixture', array(
            'foo' => function () {
                return 'bar';
            },
        ));

        $this->assertSame('bar', $mock->foo());
    }

    /**
     * @test
     * @expectedException \EasyMock\Test\Fixture\CustomException
     * @expectedExceptionMessage My message
     */
    public function should_mock_existing_method_to_throw_exception()
    {
        /** @var ClassFixture $mock */
        $mock = $this->easyMock('EasyMock\Test\Fixture\ClassFixture', array(
            'foo' => new CustomException('My message'),
        ));
        $mock->foo();
    }

    /**
     * @test
     */
    public function should_mock_new_methods_on_existing_mock()
    {
        /** @var ClassFixture $mock */
        $mock = $this->easyMock('EasyMock\Test\Fixture\ClassFixture');
        $mock = $this->easyMock($mock, array(
            'foo' => 'bar',
        ));

        $this->assertSame('bar', $mock->foo());
    }

    /**
     * @test
     */
    public function should_allow_to_spy_method_calls()
    {
        $mock = $this->easySpy('EasyMock\Test\Fixture\ClassFixture', array(
            'foo' => 'bar',
        ));

        // Test PHPUnit's internals to check that the spy was registered
        $property = new \ReflectionProperty('\PHPUnit\Framework\TestCase', 'mockObjects');
        $property->setAccessible(true);
        $mockObjects = $property->getValue($this);

        $this->assertCount(1, $mockObjects);
        $this->assertSame($mock, $mockObjects[0]);

        // Cannot use @expectedException because PHPUnit has specific behavior for this
        try {
            $mock->__phpunit_verify();
            $this->fail('Exception not thrown');
        } catch (ExpectationFailedException $e) {
            $this->assertContains('Expected invocation at least once but it never occur', $e->getMessage());
        }

        // Invoke the mock: the test should now pass
        $mock->foo();
    }
}
