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

    private $l10nManager;

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

        $this->l10nManager = new L10nYamlManager('fake_path');
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
}