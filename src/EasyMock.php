<?php

namespace EasyMock;

use PHPUnit_Framework_MockObject_Matcher_AnyInvokedCount as AnyInvokedCount;
use PHPUnit_Framework_MockObject_Matcher_Invocation as InvocationMatcher;
use PHPUnit_Framework_MockObject_Matcher_InvokedAtLeastOnce as InvokedAtLeastOnce;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

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
     * @param string $classname The class to mock. Can also be an existing mock to mock new methods.
     * @param array  $methods   Array of values to return, indexed by the method name.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function easyMock($classname, array $methods = array())
    {
        if ($classname instanceof MockObject) {
            $mock = $classname;
        } else {
            $mock = $this->doCreateMock($classname);
        }

        foreach ($methods as $method => $return) {
            $this->mockMethod($mock, $method, new AnyInvokedCount, $return);
        }

        return $mock;
    }

    /**
     * Mock the given class by spying on method calls.
     *
     * This is the same as EasyMock::mock() except this assert that methods are called at
     * least once.
     *
     * @see easyMock()
     *
     * @param string $classname The class to mock. Can also be an existing mock to mock new methods.
     * @param array  $methods   Array of values to return, indexed by the method name.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function easySpy($classname, array $methods = array())
    {
        if ($classname instanceof MockObject) {
            $mock = $classname;
        } else {
            $mock = $this->doCreateMock($classname);
        }

        foreach ($methods as $method => $return) {
            $this->mockMethod($mock, $method, new InvokedAtLeastOnce, $return);
        }

        return $mock;
    }

    private function mockMethod(MockObject $mock, $method, InvocationMatcher $invocation, $return)
    {
        $methodAssertion = $mock->expects($invocation)
            ->method($method);

        if (is_callable($return)) {
            $methodAssertion->willReturnCallback($return);
        } elseif ($return instanceof \Exception) {
            $methodAssertion->willThrowException($return);
        } else {
            $methodAssertion->willReturn($return);
        }
    }

    /**
     * @param string $classname
     * @return MockObject
     */
    private function doCreateMock($classname)
    {
        // PHPUnit 5.4 method
        if (method_exists($this, 'createMock')) {
            return $this->createMock($classname);
        }

        // Fallback on the deprecated method
        return $this->getMock(
            $classname,
            array(),
            array(),
            '',
            false
        );
    }
}
