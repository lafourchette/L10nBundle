<?php

namespace L10nBundle\Tests\Twig\Extension;

use L10nBundle\Twig\Extension\L10nExtension;

class L10nExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var L10nExtension
     */
    private $l10nExtension;

    /**
     * @var L10nProvider|ObjectProphecy
     */
    private $l10nProvider;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->l10nProvider = $this->getMock(
            'L10nBundle\Business\L10nProvider',
            array(
                'getL10n'
            ),
            array(),
            '',
            false
        );
        $this->l10nExtension = new L10nExtension($this->l10nProvider);
    }

    /**
     * Tests a general case.
     */
    public function testGetL10n()
    {
        $this
            ->l10nProvider
            ->expects($this->once())
            ->method('getL10n')
            ->with('myKey', 'es', 'ca_ES')
            ->will($this->returnValue('Dummy'))
        ;

        $this->assertEquals(
            'Dummy',
            $this->l10nExtension->getL10n('myKey', 'es', 'ca_ES')
        );
    }

    /**
     * Tests minimal case.
     */
    public function testMinimalGetL10n()
    {
        $this
            ->l10nProvider
            ->expects($this->once())
            ->method('getL10n')
            ->with('myKey', null, null)
            ->will($this->returnValue('MinimalDummy'))
        ;

        $this->assertEquals(
            'MinimalDummy',
            $this->l10nExtension->getL10n('myKey')
        );
    }
}
