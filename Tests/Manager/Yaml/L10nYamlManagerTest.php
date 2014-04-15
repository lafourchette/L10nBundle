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
                                'l10n:localisation' => array
                                (
                                        '@id' => '#idLoc'
                                ),
                                'l10n:value' => array
                                (
                                        array
                                        (
                                                '@language' => 'fr-fr',
                                                '@value' => 'autre value fr'
                                        ),
                                        array
                                        (
                                                '@language' => 'en-gb',
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
use L10nBundle\Exception\ResourceNotFoundException;
use L10nBundle\Manager\L10nManagerInterface;
use L10nBundle\Manager\Yaml\L10nYamlManager;

/**
 * @author Cyril Otal
 *
 */
class L10nYamlManagerTest extends \PHPUnit_Framework_TestCase
{

    public function testGetL10nResource()
    {
        $idResource = 'key';
        $idLocalisation = 'idLoc';

        $valueList = array
        (
                'fr-fr' => 'autre value fr',
                'en-gb' => 'other value en'
        );

        $l10nResource = new L10nResource();
        $l10nResource->setIdLocalisation($idLocalisation);
        $l10nResource->setIdResource($idResource);
        $l10nResource->setValueList($valueList);

        $l10nManager = new L10nYamlManager('someDataFile');
        $result = $l10nManager->getL10nResource($idResource, $idLocalisation);


        $this->assertEquals($l10nResource, $result);

    }



}