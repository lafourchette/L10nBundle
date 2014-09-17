<?php

namespace L10nBundle\Business;

use L10nBundle\Entity\L10nResource;
use L10nBundle\Manager\L10nConverterInterface;
use L10nBundle\Manager\L10nManagerInterface;

/**
 * @author Cyril Otal
 */
class L10nExporterTest extends \PHPUnit_Framework_TestCase
{
    public function testExportAllL10nResourceList()
    {
        $l10nResource = new L10nResource();

        /** @var L10nManagerInterface|\PHPUnit_Framework_MockObject_MockObject $l10nManager */
        $l10nManager = $this->getMock(
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
        $l10nManager
            ->expects($this->once())
            ->method('getAllL10nResourceList')
            ->will($this->returnValue(array($l10nResource)))
        ;

        /** @var L10nConverterInterface|\PHPUnit_Framework_MockObject_MockObject $l10nConverter */
        $l10nConverter = $this->getMock(
            'L10nBundle\Manager\L10nConverterInterface',
            array('convertL10nResourceList'),
            array(),
            '',
            false
        );
        $l10nConverter->expects($this->once())
            ->method('convertL10nResourceList')
            ->with(array($l10nResource))
        ;

        $l10nExporter = new L10nExporter($l10nManager, $l10nConverter);
        $l10nExporter->exportAllL10nResourceList('/tmp/toto');
    }
}
