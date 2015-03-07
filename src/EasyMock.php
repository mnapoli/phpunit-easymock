<?php

namespace EasyMock;

use PHPUnit_Framework_MockObject_Generator;
use PHPUnit_Framework_MockObject_Matcher_AnyInvokedCount;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Generates mock objects.
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
 */
class EasyMock
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
    public static function mock($classname, array $methods = array())
    {
        if ($classname instanceof MockObject) {
            $mock = $classname;
        } else {
            $mock = self::createMock($classname);
        }

        foreach ($methods as $method => $return) {
            self::mockMethod($mock, $method, $return);
        }

        return $mock;
    }

    private static function mockMethod(MockObject $mock, $method, $return)
    {
        $methodAssertion = $mock->expects(new PHPUnit_Framework_MockObject_Matcher_AnyInvokedCount)
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
    private static function createMock($classname)
    {
        $mockGenerator = new PHPUnit_Framework_MockObject_Generator();

        return $mockGenerator->getMock(
            $classname,
            array(),
            array(),
            '',
            false
        );
    }
}
