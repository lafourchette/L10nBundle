<?php

namespace L10nBundle\Business;

use L10nBundle\Entity\L10nResource;
use L10nBundle\Manager\L10nManagerInterface;
use L10nBundle\Utils\Resolver\L10nResolver;

/**
 * @author Cyril Otal
 */
class L10nProviderTest extends \PHPUnit_Framework_TestCase
{
    /** @var L10nManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $l10nManager;

    /** @var L10nResolver|\PHPUnit_Framework_MockObject_MockObject */
    private $l10nResolver;

    public function setUp()
    {
        $this->l10nManager = $this->getMock(
            'L10nBundle\Manager\L10nManagerInterface',
            array(
                'getL10nResource',
                'setL10nResource',
                'getAllL10nResourceList'
            ),
            array(),
            '',
            false
        );

        $this->l10nResolver = $this->getMock(
            'L10nBundle\Utils\Resolver\L10nResolver',
            array('resolve'),
            array(),
            '',
            false
        );
    }

    public function testGetL10nWithAllArgs()
    {
        $key = 'key';
        $localization = 'fr';
        $locale = 'fr-FR';
        $expected = '%my-value%';
        $resolvedExpected = 'my-value';

        $l10nResource = new L10nResource();
        $l10nResource->setValueList(
            array(
                $locale => $expected
            )
        );

        $this->l10nManager
            ->expects($this->once())
            ->method('getL10nResource')
            ->with($key, $localization)
            ->will($this->returnValue($l10nResource))
        ;

        $this->l10nResolver
            ->expects($this->once())
            ->method('resolve')
            ->with($expected)
            ->will($this->returnValue($resolvedExpected))
        ;

        $l10nProvider = new L10nProvider($this->l10nManager, $this->l10nResolver, 'xx', 'xx-XX');

        $value = $l10nProvider->getL10n($key, $localization, $locale);

        $this->assertEquals($resolvedExpected, $value);
    }

    public function testGetL10nWithDefaultLocaleAndDefaultLocalization()
    {
        $key = 'key';
        $localization = 'fr';
        $locale = 'fr-FR';
        $expected = 'my-value';

        $l10nResource = new L10nResource();
        $l10nResource->setValueList(
            array(
                $locale => $expected
            )
        );

        $this->l10nManager
            ->expects($this->once())
            ->method('getL10nResource')
            ->with($key, $localization)
            ->will($this->returnValue($l10nResource))
        ;

        $this->l10nResolver
            ->expects($this->once())
            ->method('resolve')
            ->with($expected)
            ->will($this->returnValue($expected))
        ;

        $l10nProvider = new L10nProvider($this->l10nManager, $this->l10nResolver, 'xx', 'xx-XX');
        $l10nProvider->setDefaultLocale($locale);
        $l10nProvider->setDefaultLocalization($localization);

        $value = $l10nProvider->getL10n($key);

        $this->assertEquals($expected, $value);
    }

    public function testGetL10nWithFallbacks()
    {
        $key = 'key';
        $localization = 'fr';
        $localizationFallback = 'en';
        $locale = 'fr-FR';
        $localeFallback = 'en-GB';
        $expected = 'my-value';

        $l10nResource = new L10nResource();
        $l10nResource->setValueList(
            array(
                $localeFallback => $expected
            )
        );

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

        $this->l10nResolver
            ->expects($this->once())
            ->method('resolve')
            ->with($expected)
            ->will($this->returnValue($expected))
        ;

        $l10nProvider = new L10nProvider($this->l10nManager, $this->l10nResolver, $localizationFallback, $localeFallback);

        $value = $l10nProvider->getL10n($key, $localization, $locale);

        $this->assertEquals($expected, $value);
    }

    public function testGetL10nWithExistingResourceWithoutTranslationFallback()
    {
        $key = 'key';
        $localization = 'fr';
        $locale = 'fr-FR';

        $l10nResource = new L10nResource();
        $l10nResource->setValueList(
            array(
                'not-FOUND' => 'value'
            )
        );
        $l10nResource->setIdResource($key);

        $this->l10nManager
            ->expects($this->once())
            ->method('getL10nResource')
            ->with($key, $localization)
            ->will($this->returnValue($l10nResource))
        ;

        $this->l10nResolver
            ->expects($this->once())
            ->method('resolve')
            ->with($key)
            ->will($this->returnValue($key))
        ;

        $l10nProvider = new L10nProvider($this->l10nManager, $this->l10nResolver, 'en', 'en-GB');

        $value = $l10nProvider->getL10n($key, $localization, $locale);

        $this->assertEquals($value, $key);
    }

    public function testGetL10nWithKeyNotFoundThrowsExceptionNotFound()
    {
        $key = 'key';
        $localization = 'fr';
        $localizationFallback = 'en';
        $locale = 'fr-FR';
        $localeFallback = 'en-GB';

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

        $l10nProvider = new L10nProvider($this->l10nManager, $this->l10nResolver, $localizationFallback, $localeFallback);

        $this->setExpectedException('\L10nBundle\Exception\ResourceNotFoundException');
        $l10nProvider->getL10n($key, $localization, $locale);
    }

    public function testGetL10nWithNoLocale()
    {
        $key = 'key';
        $localization = 'fr';
        $locale = 'fr-FR';
        $expected = 'my-value';

        $l10nResource = new L10nResource();
        $l10nResource->setValueList(array($expected));

        $this->l10nManager
            ->expects($this->once())
            ->method('getL10nResource')
            ->with($key, $localization)
            ->will($this->returnValue($l10nResource))
        ;

        $this->l10nResolver
            ->expects($this->once())
            ->method('resolve')
            ->with($expected)
            ->will($this->returnValue($expected))
        ;

        $l10nProvider = new L10nProvider($this->l10nManager, $this->l10nResolver, 'xx', 'xx-XX');

        $value = $l10nProvider->getL10n($key, $localization, $locale);

        $this->assertEquals($expected, $value);
    }
}
