<?php

namespace L10nBundle\Manager\MongoDb;

use L10nBundle\Entity\L10nResource;
use L10nBundle\Manager\JsonLd\L10nJsonLdConverter;

/**
 * @todo doc
 *
 * @author Cyril Otal
 */
class L10nJsonLdConverterTest extends \PHPUnit_Framework_TestCase
{
    public function testConvertL10nResourceList()
    {
        $l10nResourceList = array();
        $l10nResource     = new L10nResource();
        $l10nResource->setIdLocalization('Montpellier');
        $l10nResource->setIdResource('Address');
        $valueList          = array();
        $valueList['fr-FR'] = 'rue';
        $valueList['en-GB'] = 'street';
        $l10nResource->setValueList($valueList);
        $l10nResourceList[] = $l10nResource;

        $l10nResource = new L10nResource();
        $l10nResource->setIdLocalization('Montpellier');
        $l10nResource->setIdResource('tel');
        $l10nResource->setValueList(array('06'));
        $l10nResourceList[] = $l10nResource;

        $expected = '{"@context":{"l10n":"'
            . L10nJsonLdConverter::NS
            . '"},"@graph":'
            . '[{"@id":"_0","l10n:key":[{"@id":"Address"}],'
            . '"l10n:localization":[{"@id":"Montpellier"}],'
            . '"l10n:value":["rue@fr-FR","street@en-GB"]},'
            . '{"@id":"_1","l10n:key":[{"@id":"tel"}],'
            . '"l10n:localization":[{"@id":"Montpellier"}],'
            . '"l10n:value":["06"]}]}';

        // PHP 5.4+
        if (defined('JSON_PRETTY_PRINT')) {
            $expected = json_encode(
                json_decode($expected, true),
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            );
        }

        $l10nJsonLdConverter = new L10nJsonLdConverter();
        $result              = $l10nJsonLdConverter->convertL10nResourceList($l10nResourceList);
        $this->assertEquals($expected, $result);
    }
}

