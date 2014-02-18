<?php

namespace L10nBundle\Business;

use L10nBundle\Entity\L10nResource;
use L10nBundle\Exception\ResourceNotFoundException;
use L10nBundle\Manager\L10nManagerInterface;
use L10nBundle\Business\L10nProvider;

/**
 * @author Cyril Otal
 *
 */
class L10nProviderTest extends \PHPUnit_Framework_TestCase
{



    function testGetL10n()
    {
        $idResource = 'key';
        $idLocalisation = 'France';

        $l10nResource = new L10nResource();


        $l10nManager = $this->getMock('L10nBundle\Manager\L10nManagerInterface', array('getL10nResource', 'setL10nResource'), array(), '', null);
        $l10nManager
            ->expects($this->once())
            ->method('getL10nResource')
            ->with($idResource, $idLocalisation)
            ->will($this->returnValue($l10nResource))
        ;

        $l10nProvider = new L10nProvider($l10nManager);
        $l10nProvider->getL10n($idResource, $idLocalisation);

    }



    /**
     * @expectedException \L10nBundle\Exception\ResourceNotFoundException
     */
    function testGetL10nWithResourceNotFoundException()
    {
        $idResource = 'key';
        $idLocalisation = 'France';

        $l10nManager = $this->getMock('L10nBundle\Manager\L10nManagerInterface', array('getL10nResource', 'setL10nResource'), array(), '', null);
        $l10nManager
            ->expects($this->once())
            ->method('getL10nResource')
            ->with($idResource, $idLocalisation)
            ->will($this->returnValue(null))
        ;

        $l10nProvider = new L10nProvider($l10nManager);
        $l10nProvider->getL10n($idResource, $idLocalisation);

    }

}