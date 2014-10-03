<?php

namespace L10nBundle\Manager\Yaml;

use L10nBundle\Entity\L10nResource;
use L10nBundle\Utils\Helper\L10nCatalogueHelper;

/**
 * @author Cyril Otal
 */
class L10nYamlManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var L10nCatalogueHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $catalogueHelper;

    /**
     * @var L10nResource
     */
    private $l10nResource;

    /**
     * @var string
     */
    private $idResource = 'key';

    /**
     * @var string
     */
    private $idLocalization = 'idLoc';

    /**
     * @var array
     */
    private $valueList;

    /**
     * @var array
     */
    private $yamlResourceList;

    /**
     * @var L10nYamlManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $l10nManager;

    public function setUp()
    {
        $this->l10nResource = new L10nResource();
        $this->valueList = array(
            'fr-FR' => 'other value fr',
            'en-GB' => 'other value en',
        );
        $this->l10nResource->setIdLocalization($this->idLocalization);
        $this->l10nResource->setIdResource($this->idResource);
        $this->l10nResource->setValueList($this->valueList);

        $this->yamlResourceList = array(
            'key' => array(
                'idLoc' => $this->valueList,
            ),
        );

        $l10nManagerReflection = new \ReflectionClass('L10nBundle\Manager\Yaml\L10nYamlManager');

        $this->l10nManager = $this->getMock(
            'L10nBundle\Manager\Yaml\L10nYamlManager',
            null,
            array(),
            'L10nYamlManager',
            false
        );

        $this->catalogueHelper = $this->getMock(
            'L10nBundle\Utils\Helper\L10nCatalogueHelper',
            array()
        );

        $privateProperty = $l10nManagerReflection->getProperty('catalogue');
        $privateProperty->setAccessible(true);
        $privateProperty->setValue($this->l10nManager, $this->yamlResourceList);
    }

    public function test__construct()
    {
        $config = array('toto');
        $catalogue = array(42);

        $this->catalogueHelper
            ->expects($this->once())
            ->method('createCatalogue')
            ->with($config)
            ->will($this->returnValue($catalogue))
        ;

        $l10nManager = new L10nYamlManager($this->catalogueHelper, $config);

        $this->assertEquals($catalogue, $l10nManager->getCatalogue());
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

    /**
     * @expectedException \Exception
     */
    public function testSetL10nResource()
    {
        $this->l10nManager->setL10nResource($this->l10nResource);
    }

    /**
     * This test seems useless, since hydrate() is already called in other test methods,
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
