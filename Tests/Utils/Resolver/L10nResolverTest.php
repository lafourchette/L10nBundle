<?php

namespace L10nBundle\Tests\Utils\Resolver;

use L10nBundle\Utils\Resolver\L10nResolver;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class L10nResolverTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Container|\PHPUnit_Framework_MockObject_MockObject
     */
    private $container;

    /**
     * @var ParameterBag|\PHPUnit_Framework_MockObject_MockObject
     */
    private $parameterBag;

    public function setUp()
    {
        $this->container = $this->getMock(
            'Symfony\Component\DependencyInjection\Container',
            array('getParameterBag')
        );

        $this->parameterBag = $this->getMock(
            'Symfony\Component\DependencyInjection\ParameterBag\ParameterBag',
            array(
                'resolve',
                'resolveValue',
            )
        );

        $this->parameterBag
            ->expects($this->once())
            ->method('resolve')
        ;

        $this->container
            ->expects($this->once())
            ->method('getParameterBag')
            ->with()
            ->will($this->returnValue($this->parameterBag))
        ;
    }

    /**
     * @dataProvider getDataForResolve
     *
     * @param mixed $value
     * @param mixed $expectedValue
     * @param bool  $isResolveValueCalled
     */
    public function testResolve($value, $expectedValue, $isResolveValueCalled)
    {
        if ($isResolveValueCalled) {
            $this->parameterBag
                ->expects($this->once())
                ->method('resolveValue')
                ->with($value)
                ->will($this->returnValue($expectedValue))
            ;
        }

        $resolver = new L10nResolver($this->container);

        $resultValue = $resolver->resolve($value);

        $this->assertSame($expectedValue, $resultValue);
    }

    /**
     * @return array
     */
    public function getDataForResolve()
    {
        return array(
            array('test_string_input', 'test_string_output', true),
            array(true, true, true),
            array(42, 42, true),
            array(array(), null, false),
        );
    }
}
