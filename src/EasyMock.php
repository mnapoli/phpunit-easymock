<?php

namespace EasyMock;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Rule\AnyInvokedCount;
use PHPUnit\Framework\MockObject\Rule\InvocationOrder;
use PHPUnit\Framework\MockObject\Rule\InvokedAtLeastOnce;

/**
 * Generates mock objects.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
trait EasyMock
{
    /**
     * Mock the given class.
     *
     * Methods not specified in $methods will be mocked to return null (default PHPUnit behavior).
     * The class constructor will *not* be called.
     *
     * @param string|MockObject $classname The class to mock. Can also be an existing mock to mock new methods.
     * @param array             $methods   Array of values to return, indexed by the method name.
     *
     * @return MockObject
     */
    protected function easyMock($classname, array $methods = array()): MockObject
    {
        $mock = $classname instanceof MockObject ? $classname : $this->createMock($classname);

        foreach ($methods as $method => $return) {
            $this->mockMethod($mock, $method, new AnyInvokedCount(), $return);
        }

        return $mock;
    }

    /**
     * Mock the given class by spying on method calls.
     *
     * This is the same as EasyMock::mock() except this assert that methods are called at least once.
     *
     * @see easyMock()
     *
     * @param string|MockObject $classname The class to mock. Can also be an existing mock to mock new methods.
     * @param array             $methods   Array of values to return, indexed by the method name.
     *
     * @return MockObject
     */
    protected function easySpy($classname, array $methods = array()): MockObject
    {
        $mock = $classname instanceof MockObject ? $classname : $this->createMock($classname);

        foreach ($methods as $method => $return) {
            $this->mockMethod($mock, $method, new InvokedAtLeastOnce(), $return);
        }

        return $mock;
    }

    abstract protected function createMock($originalClassName): MockObject;

    private function mockMethod(MockObject $mock, $method, InvocationOrder $invocation, $return): void
    {
        $methodAssertion = $mock->expects($invocation)->method($method);

        if (is_callable($return)) {
            $methodAssertion->willReturnCallback($return);
        } elseif ($return instanceof \Exception) {
            $methodAssertion->willThrowException($return);
        } else {
            $methodAssertion->willReturn($return);
        }
    }
}
