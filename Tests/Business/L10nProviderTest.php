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
    protected $l10nManager;

    public function setUp()
    {
        $this->l10nManager = $this->getMock('L10nBundle\Manager\L10nManagerInterface', array(
            'getL10nResource',
            'setL10nResource',
            'getAllL10nResourceList'
        ), array(), '', false);
    }

    public function testGetL10nWithAllArgs()
    {
        $key = 'cgu';
        $localization = 'fr';
        $locale = 'fr_FR';
        $expected = 'my_value';


        $l10nResource = new L10nResource();
        $l10nResource->setValueList(array(
            $locale => $expected
        ));


        $this->l10nManager
            ->expects($this->once())
            ->method('getL10nResource')
            ->with($key, $localization)
            ->will($this->returnValue($l10nResource))
        ;

        $l10nProvider = new L10nProvider($this->l10nManager, 'xx', 'xx_XX');

        $value = $l10nProvider->getL10n($key, $localization, $locale);

        $this->assertEquals($expected, $value);
    }

    public function testGetL10nWithDefaultLocaleAndDefaultLocalization()
    {
        $key = 'cgu';
        $localization = 'fr';
        $locale = 'fr_FR';
        $expected = 'my_value';


        $l10nResource = new L10nResource();
        $l10nResource->setValueList(array(
            $locale => $expected
        ));


        $this->l10nManager
            ->expects($this->once())
            ->method('getL10nResource')
            ->with($key, $localization)
            ->will($this->returnValue($l10nResource))
        ;

        $l10nProvider = new L10nProvider($this->l10nManager, 'xx', 'xx_XX');
        $l10nProvider->setDefaultLocale($locale);
        $l10nProvider->setDefaultLocalization($localization);

        $value = $l10nProvider->getL10n($key);

        $this->assertEquals($expected, $value);
    }

    public function testGetL10nWithFallbacks()
    {
        $key = 'cgu';
        $localization = 'fr';
        $localizationFallback = 'en';
        $locale = 'fr_FR';
        $localeFallback = 'en_GB';
        $expected = 'my_value';


        $l10nResource = new L10nResource();
        $l10nResource->setValueList(array(
            $localeFallback => $expected
        ));


        $this->l10nManager
            ->expects($this->at(0))
            ->method('getL10nResource')
            ->with($key, $localization)
            ->will($this->returnValue(null))
        ;

        $this->l10nManager
            ->expects($this->at(1))
            ->method('getL10nResource')
            ->with($key, $localizationFallback)
            ->will($this->returnValue($l10nResource))
        ;

        $l10nProvider = new L10nProvider($this->l10nManager, $localizationFallback, $localeFallback);

        $value = $l10nProvider->getL10n($key, $localization, $locale);

        $this->assertEquals($expected, $value);
    }


    public function testGetL10nWithExistingResourceWithoutTranslationFallbackWill()
    {
        $key = 'cgu';
        $localization = 'fr';
        $locale = 'fr_FR';

        $l10nResource = new L10nResource();
        $l10nResource->setValueList(array(
            'not_FOUND' => 'value'
        ));


        $this->l10nManager
            ->expects($this->once())
            ->method('getL10nResource')
            ->with($key, $localization)
            ->will($this->returnValue($l10nResource))
        ;

        $l10nProvider = new L10nProvider($this->l10nManager, 'en', 'en_GB');

        $value = $l10nProvider->getL10n($key, $localization, $locale);

        $this->assertNull($value);
    }

    public function testGetL10nWithKeyNotFoundThrowsExceptionNotFound()
    {
        $key = 'cgu';
        $localization = 'fr';
        $localizationFallback = 'en';
        $locale = 'fr_FR';
        $localeFallback = 'en_GB';

        $this->l10nManager
            ->expects($this->at(0))
            ->method('getL10nResource')
            ->with($key, $localization)
            ->will($this->returnValue(null))
        ;

        $this->l10nManager
            ->expects($this->at(1))
            ->method('getL10nResource')
            ->with($key, $localizationFallback)
            ->will($this->returnValue(null))
        ;

        $l10nProvider = new L10nProvider($this->l10nManager, $localizationFallback, $localeFallback);

        $this->setExpectedException('\L10nBundle\Exception\ResourceNotFoundException');
        $l10nProvider->getL10n($key, $localization, $locale);
    }
}
