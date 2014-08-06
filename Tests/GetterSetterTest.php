<?php

namespace L10nBundle;

/**
 * @author Cyril Otal
 * Generic test class to test usual getter and setter
 */
class GetterSetterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Some couples of data, indexed by type
     * If a setter need a type not provided here, the test is skipped
     * @var array
     */
    public $valueList = array (
            'string' => array('first_value', 'other_value'),
            'array' => array(
                    array(1, 2),
                    array('a', 'b'),
            )
    );

    /**
     * List of classes to test, with there constructors' mocked arguments
     * In PHP >= 5.4, constructors' arguments could be avoided thanks to newInstanceWithoutConstructor method.
     * @return array
     */
    public function getClass()
    {
        return array(
            array(
                'L10nBundle\Entity\L10nResource',
                array()
                ),
            array(
                'L10nBundle\Business\L10nProvider',
                array(
                    $this->getMock('L10nBundle\Manager\L10nManagerInterface'),
                    null,
                    null
            ))
        );
    }

    /**
     * @dataProvider getClass
     */
    public function testGetterAndSetter($class, $constructorParameterList)
    {

        $reflexionClass = new \ReflectionClass($class);

        if ($reflexionClass->getConstructor())
        {
            $instance = $reflexionClass->newInstanceArgs($constructorParameterList);
        } else
        {
            $instance = $reflexionClass->newInstance();
        }

        $propertyList = $reflexionClass->getProperties();
        foreach ($propertyList as $property) {
            $name = ucfirst($property->getName());

            $property->setAccessible(true);
            $getter = 'get' . $name;
            $setter = 'set' . $name;

            if ($reflexionClass->hasMethod($setter)) {
                $type = $this->guessParamType($reflexionClass->getMethod($setter));
                if (isset($this->valueList[$type])) {
                    $value = $this->valueList[$type];

                    $instance->$setter($value[0]);
                    // test setter
                    $this->assertEquals($property->getValue($instance), $value[0]);

                    if ($reflexionClass->hasMethod($getter)) {
                        $property->setValue($instance, $value[1]);
                        // test getter
                        $this->assertEquals($instance->$getter(), $value[1]);
                    }
                }
            }
        }
    }

    public function guessParamType(\ReflectionMethod $method)
    {
        $parameterList = $method->getParameters();
        $var = $parameterList[0];
        $class = $var->getClass();
        if ($class) {
            return $class->getName();
        }
        if($var->isArray()) {
            return 'array';
        }
        return 'string';
    }
}