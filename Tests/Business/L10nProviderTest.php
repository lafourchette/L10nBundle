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
    public function testGetL10n($idResource, $idLocalization, $idLocalizationAskedToManager, $locale, $defaultLocalization, $defaultLocale, $l10nResource, $expected)
    {

        $l10nManager = $this->getMock('L10nBundle\Manager\L10nManagerInterface', array('getL10nResource', 'setL10nResource', 'getAllL10nResourceList'), array(), '', false);
        $l10nManager
            ->expects($this->once())
            ->method('getL10nResource')
            ->with($idResource, $idLocalizationAskedToManager)
            ->will($this->returnValue($l10nResource))
        ;

        $l10nProvider = new L10nProvider($l10nManager, $defaultLocalization, $defaultLocale);
        $value = $l10nProvider->getL10n($idResource, $idLocalization, $locale);
        $this->assertEquals($expected, $value);

    }

    public function provideData()
    {
        $return = array();

        $idResource = 'key';
        $idLocalization = 'France';
        $defaultLocalization = 'Japan';
        $defaultLocale = 'fr-BE';
        $locale = 'sl-SI';

        $l10nResource = new L10nResource();
        $valueList = array();
        $value = 'plop';
        $valueList[$locale] = $value;
        $l10nResource->setValueList($valueList);

        // I18N Value
        $return[] = array($idResource, $idLocalization, $idLocalization, $locale, $defaultLocalization, $defaultLocale, $l10nResource, $value);

        $l10nResource->setValueList(array($value));

        // non I18N value
        $return[] = array($idResource, $idLocalization, $idLocalization, $locale, $defaultLocalization, $defaultLocale, $l10nResource, $value);

        $l10nResource->setValueList(array($defaultLocale => $value));

        // test fallbacks
        $return[] = array($idResource, null, $defaultLocalization, null, $defaultLocalization, $defaultLocale, $l10nResource, $value);

        return $return;
    }

    /**
     * @expectedException \L10nBundle\Exception\ResourceNotFoundException
     */
    public function testGetL10nWithResourceNotFoundException()
    {
        $idResource = 'key';
        $idLocalization = 'France';
        $defaultLocalization = 'Japan';
        $defaultLocale = 'fr-BE';

        $l10nManager = $this->getMock('L10nBundle\Manager\L10nManagerInterface', array('getL10nResource', 'setL10nResource', 'getAllL10nResourceList'), array(), '', false);
        $l10nManager
            ->expects($this->once())
            ->method('getL10nResource')
            ->with($idResource, $idLocalization)
            ->will($this->returnValue(null))
        ;

        $l10nProvider = new L10nProvider($l10nManager, $defaultLocalization, $defaultLocale);
        $l10nProvider->getL10n($idResource, $idLocalization);

    }

}