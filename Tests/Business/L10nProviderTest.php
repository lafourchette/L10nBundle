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



    /**
     *
     * @dataProvider provideData
     */
    public function testGetL10n($idResource, $idLocalisation, $idLocalisationAskedToManager, $locale, $defaultLocalisation, $defaultLocale, $l10nResource, $expected)
    {

        $l10nManager = $this->getMock('L10nBundle\Manager\L10nManagerInterface', array('getL10nResource', 'setL10nResource'), array(), '', false);
        $l10nManager
            ->expects($this->once())
            ->method('getL10nResource')
            ->with($idResource, $idLocalisationAskedToManager)
            ->will($this->returnValue($l10nResource))
        ;

        $l10nProvider = new L10nProvider($l10nManager, $defaultLocalisation, $defaultLocale);
        $value = $l10nProvider->getL10n($idResource, $idLocalisation, $locale);
        $this->assertEquals($expected, $value);

    }

    public function provideData()
    {
        $return = array();

        $idResource = 'key';
        $idLocalisation = 'France';
        $defaultLocalisation = 'Japan';
        $defaultLocale = 'fr-BE';
        $locale = 'sl-SI';

        $l10nResource = new L10nResource();
        $valueList = array();
        $value = 'plop';
        $valueList[$locale] = $value;
        $l10nResource->setValueList($valueList);

        // I18N Value
        $return[] = array($idResource, $idLocalisation, $idLocalisation, $locale, $defaultLocalisation, $defaultLocale, $l10nResource, $value);

        $l10nResource->setValueList(array($value));

        // non I18N value
        $return[] = array($idResource, $idLocalisation, $idLocalisation, $locale, $defaultLocalisation, $defaultLocale, $l10nResource, $value);

        $l10nResource->setValueList(array($defaultLocale => $value));

        // test fallbacks
        $return[] = array($idResource, null, $defaultLocalisation, null, $defaultLocalisation, $defaultLocale, $l10nResource, $value);

        return $return;
    }

    /**
     * @expectedException \L10nBundle\Exception\ResourceNotFoundException
     */
    public function testGetL10nWithResourceNotFoundException()
    {
        $idResource = 'key';
        $idLocalisation = 'France';
        $defaultLocalisation = 'Japan';
        $defaultLocale = 'fr-BE';

        $l10nManager = $this->getMock('L10nBundle\Manager\L10nManagerInterface', array('getL10nResource', 'setL10nResource'), array(), '', false);
        $l10nManager
            ->expects($this->once())
            ->method('getL10nResource')
            ->with($idResource, $idLocalisation)
            ->will($this->returnValue(null))
        ;

        $l10nProvider = new L10nProvider($l10nManager, $defaultLocalisation, $defaultLocale);
        $l10nProvider->getL10n($idResource, $idLocalisation);

    }

}