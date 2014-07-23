<?php

/* Static Mocking */
namespace Symfony\Component\Yaml;

class Yaml
{
    public static function parse($input, $exceptionOnInvalidType = false, $objectSupport = false)
    {
        // return static data, for tests
        return array(
            'l10n' => array(
                'key' => array
                (
                    'idLoc'  =>  array
                    (
                     'fr-FR' => 'autre value fr',
                     'en-GB' => 'other value en'
                    )
                )
            )
        );
    }
}


/* Test */

namespace L10nBundle\Manager\Yaml;

use L10nBundle\Entity\L10nResource;
use L10nBundle\Manager\Yaml\L10nYamlManager;

/**
 * @author Cyril Otal
 *
 */
class L10nYamlManagerTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     * @var L10nResource
     */
    private $l10nResource;

    /**
     *
     * @var string
     */
    private $idResource = 'key';

    /**
     * @var string
     */
    private $idLocalization = 'idLoc';

    /**
     *
     * @var array
     */
    private $valueList;

    /**
     *
     * @var array
     */
    private $yamlResourceList;

    private $l10nManager;

    public function setUp()
    {
        $this->l10nResource = new L10nResource();
        $this->valueList = array (
                'fr-FR' => 'autre value fr',
                'en-GB' => 'other value en'
        );
        $this->l10nResource->setIdLocalization($this->idLocalization);
        $this->l10nResource->setIdResource($this->idResource);
        $this->l10nResource->setValueList($this->valueList);

        $this->yamlResourceList =
            array(
                'key' => array
                (
                    'idLoc' => array
                    (
                        'fr-FR' => 'autre value fr',
                        'en-GB' => 'other value en'
                    )
                )
            );

        $l10nManagerReflection = new \ReflectionClass('L10nBundle\Manager\Yaml\L10nYamlManager');

        $this->l10nManager = $this->getMock(
            'L10nBundle\Manager\Yaml\L10nYamlManager',
            array('buildCatalogue'),
            array('fake_path'),
            'L10nYamlManager',
            false
            );

        $privateProperty = $l10nManagerReflection->getProperty('catalogue');
        $privateProperty->setAccessible(true);
        $privateProperty->setValue($this->l10nManager , $this->yamlResourceList);
    }

    public function testGetL10nResource()
    {
        $result = $this->l10nManager->getL10nResource($this->idResource, $this->idLocalization);

        $this->assertEquals($this->l10nResource, $result);
    }

    public function testGetAllL10nResourceList()
    {
        $result = $this->l10nManager->getAllL10nResourceList();
        $this->assertEquals(array($this->l10nResource), $result);
    }

    public function test__construct()
    {
        $path = 'yet/another/fake/path';

        $l10nManagerReflection = new \ReflectionClass('L10nBundle\Manager\Yaml\L10nYamlManager');
        $method = $l10nManagerReflection->getMethod('buildCatalogue');
        $method->setAccessible(true);

        $l10nManager = $this->getMock(
            'L10nBundle\Manager\Yaml\L10nYamlManager',
            array('buildCatalogue'),
            array($path),
            'L10nYamlManager',
            false
            );

        $l10nManager->expects($this->once())
            ->method('buildCatalogue')
            ->with($path)
            ->will($this->returnValue($this->yamlResourceList));

        $l10nManager->__construct($path);

        $this->assertEquals($this->yamlResourceList,
            $l10nManager->getCatalogue());
    }

    /**
     * @expectedException Exception
     */
    public function testSetL10nResource()
    {
        $this->l10nManager->setL10nResource($this->l10nResource);
    }

    /**
     * This test check the buildCatalogue() method
     * It's mocked in all other tests
     */
    public function testBuildCatalogue()
    {
        $l10nManagerReflection = new \ReflectionClass('L10nBundle\Manager\Yaml\L10nYamlManager');

        $oMethod = $l10nManagerReflection->getMethod('buildCatalogue');
        $oMethod->setAccessible(true);
        $result = $oMethod->invoke($this->l10nManager, '/fake/path');
        $this->assertEquals($this->yamlResourceList, $result);
    }

    /**
     * This test seems useless, since hydrate() is already called in other teste methods,
     * But the goal of this test is to point directly on an error in hydrate.
     */
    public function testHydrate()
    {
        $l10nManagerReflection = new \ReflectionClass('L10nBundle\Manager\Yaml\L10nYamlManager');

        $oMethod = $l10nManagerReflection->getMethod('hydrate');
        $oMethod->setAccessible(true);
        $result = $oMethod->invoke($this->l10nManager, $this->idLocalization, $this->idResource, $this->valueList);
        $this->assertEquals($this->l10nResource, $result);
    }

}
