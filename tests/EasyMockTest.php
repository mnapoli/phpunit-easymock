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
    public function should_mock_objects(): void
    {
        /** @var ClassFixture $mock */
        $mock = $this->easyMock(ClassFixture::class);

        $this->assertNull($mock->foo());
    }

    /**
     * @test
     */
    public function should_skip_the_constructor(): void
    {
        /** @var ClassWithConstructor $mock */
        $mock = $this->easyMock(ClassWithConstructor::class);

        $this->assertFalse($mock->constructorCalled);
    }

    /**
     * @test
     */
    public function should_mock_interfaces(): void
    {
        /** @var InterfaceFixture $mock */
        $mock = $this->easyMock(InterfaceFixture::class);

        $this->assertNull($mock->foo());
    }

    /**
     * @test
     */
    public function not_mocked_methods_should_return_null(): void
    {
        /** @var ClassFixture $mock */
        $mock = $this->easyMock(ClassFixture::class);

        $this->assertNull($mock->foo());
    }

    /**
     * @test
     */
    public function should_mock_existing_method_with_a_value(): void
    {
        /** @var ClassFixture $mock */
        $mock = $this->easyMock(ClassFixture::class, array(
            'foo' => 'bar',
        ));

        $this->assertSame('bar', $mock->foo());
    }

    /**
     * @test
     */
    public function should_mock_existing_method_with_a_callback(): void
    {
        /** @var ClassFixture $mock */
        $mock = $this->easyMock(ClassFixture::class, array(
            'foo' => function () {
                return 'bar';
            },
        ));

        $this->assertSame('bar', $mock->foo());
    }

    /**
     * @test
     */
    public function should_mock_existing_method_to_throw_exception(): void
    {
        /** @var ClassFixture $mock */
        $mock = $this->easyMock(ClassFixture::class, array(
            'foo' => new CustomException('My message'),
        ));

        $this->expectException(CustomException::class);
        $this->expectExceptionMessage('My message');

        $mock->foo();
    }

    /**
     * @test
     */
    public function should_mock_new_methods_on_existing_mock(): void
    {
        /** @var ClassFixture $mock */
        $mock = $this->easyMock(ClassFixture::class);
        $mock = $this->easyMock($mock, array(
            'foo' => 'bar',
        ));

        $this->assertSame('bar', $mock->foo());
    }

    /**
     * @test
     */
    public function should_allow_to_spy_method_calls(): void
    {
        /** @var ClassFixture $mock */
        $mock = $this->easySpy(ClassFixture::class, array(
            'foo' => 'bar',
        ));

        // Test PHPUnit's internals to check that the spy was registered
        $property = new \ReflectionProperty(TestCase::class, 'mockObjects');
        $property->setAccessible(true);
        $mockObjects = $property->getValue($this);

        $this->assertCount(1, $mockObjects);
        $this->assertSame($mock, $mockObjects[0]);

        // Cannot use @expectedException because PHPUnit has specific behavior for this
        try {
            $mock->__phpunit_verify();
            $this->fail('Exception not thrown');
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString('Expected invocation at least once but it never occur', $e->getMessage());
        }

        // Invoke the mock: the test should now pass
        $mock->foo();
    }
}
