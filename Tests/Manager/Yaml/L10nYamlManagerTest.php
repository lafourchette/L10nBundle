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



    function testGetL10nResource()
    {
        $idResource = 'key';
        $idLocalisation = 'idLoc';

        $l10nResource = new L10nResource();

        $l10nManager = new L10nYamlManager();
        $values = $l10nManager->getL10nResource($idResource, $idLocalisation);

        $expected = array
            (
                'fr-fr' => 'autre value fr',
                'en-gb' => 'other value en'
            );

        $this->assertEquals($expected, $values);

    }



}