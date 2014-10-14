<?php

namespace L10nBundle\Tests\Utils\Resolver;

use L10nBundle\Utils\Resolver\L10nResolver;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class L10nResolverTest extends \PHPUnit_Framework_TestCase
{
    /** @var Container|\PHPUnit_Framework_MockObject_MockObject */
    private $container;

    /** @var ParameterBag|\PHPUnit_Framework_MockObject_MockObject */
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
                'resolveString',
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
     */
    public function testResolve($value, $expectedValue, $isResolveStringCalled)
    {
    	if ($isResolveStringCalled) {
        	$this->parameterBag
	            ->expects($this->once())
	            ->method('resolveString')
	            ->with($value)
	            ->will($this->returnValue($expectedValue))
        	;
    	}

        $resolver = new L10nResolver($this->container);

        $resultValue = $resolver->resolve($value);

        $this->assertSame($expectedValue, $resultValue);
    }

    public function getDataForResolve()
    {
    	return array(
    		array('test_string_input', 'test_string_output', true),
    		array(array(), null, false),
    	);
    }
}
