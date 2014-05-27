<?php

/* Static Mocking */
namespace Symfony\Component\Yaml;

class Yaml
{
    public static function parse($input, $exceptionOnInvalidType = false, $objectSupport = false)
    {
        // return static data, for tests
        return array(
                '@graph' => array
                    (
                        array
                        (
                                'l10n:key' => array
                                (
                                        '@id' => '#key'
                                ),
                                'l10n:localization' => array
                                (
                                        '@id' => '#idLoc'
                                ),
                                'l10n:value' => array
                                (
                                        array
                                        (
                                                '@language' => 'fr-FR',
                                                '@value' => 'autre value fr'
                                        ),
                                        array
                                        (
                                                '@language' => 'en-GB',
                                                '@value' => 'other value en'
                                        )
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

    private $l10nManager;
    private $yamlResourceList;

    public function setUp()
    {
        $this->l10nResource = new L10nResource();
        $this->valueList = array
        (
                'fr-FR' => 'autre value fr',
                'en-GB' => 'other value en'
        );
        $this->l10nResource->setIdLocalization($this->idLocalization);
        $this->l10nResource->setIdResource($this->idResource);
        $this->l10nResource->setValueList($this->valueList);

        $l10nManagerReflection = new \ReflectionClass('L10nBundle\Manager\Yaml\L10nYamlManager');
        $method = $l10nManagerReflection->getMethod('getYamlResourceList');
        $method->setAccessible(true);

        $this->l10nManager = $this->getMock('L10nBundle\Manager\Yaml\L10nYamlManager', array('getYamlResourceList'), array('someDataFile'), 'L10nYamlManager', false);


        $this->yamlResourceList = array(
            array (
                'l10n:key' => array('@id' => '#key'),
                'l10n:localization' => array('@id' => '#idLoc'),
                'l10n:value' => array(
                    array(
                        '@language' => 'fr-FR',
                        '@value' => 'autre value fr'
                    ),
                    array(
                            '@language' => 'en-GB',
                            '@value' => 'other value en'
                    ),
                )
            )
        );

        $this->l10nManager->expects($this->any())
            ->method('getYamlResourceList')
            ->with()
            ->will($this->returnValue($this->yamlResourceList));
    }

    public function testGetL10nResource()
    {
        $l10nManager = new L10nYamlManager('someDataFile');
        //var_dump($this->l10nManager);
        $result = $this->l10nManager->getL10nResource($this->idResource, $this->idLocalization);

        $this->assertEquals($this->l10nResource, $result);
    }

    public function testGetAllL10nResourceList()
    {
        $l10nManager = new L10nYamlManager('someDataFile');
        $result = $this->l10nManager->getAllL10nResourceList();
        $this->assertEquals(array($this->l10nResource), $result);
    }

    public function testGetYamlResourceList()
    {
        $l10nManager = new L10nYamlManager('someDataFile');

        $l10nManagerReflection = new \ReflectionClass('L10nBundle\Manager\Yaml\L10nYamlManager');

        $oMethod = $l10nManagerReflection->getMethod('getYamlResourceList');
        $oMethod->setAccessible(true);
        $result = $oMethod->invoke($l10nManager);
        $this->assertEquals($this->yamlResourceList, $result);
    }
}