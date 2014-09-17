<?php

namespace L10nBundle\Manager\JsonLd;

use L10nBundle\Entity\L10nResource;
use L10nBundle\Manager\L10nConverterInterface;
use ML\JsonLD\JsonLD;
use ML\IRI\IRI;
use ML\JsonLD\TypedValue;
use ML\JsonLD\Quad;
use ML\JsonLD\RdfConstants;

/**
 * @todo doc
 *
 * @author Cyril Otal
 */
class L10nJsonLdConverter implements L10nConverterInterface
{
    const NS = 'http://plop.org/l10n/ns/';

    /**
     * Convert a list of L10nResources in a JSON-LD file
     *
     * @param L10nResource[] $l10nResourceList
     *
     * @return string JSON-LD document
     */
    public function convertL10nResourceList(array $l10nResourceList)
    {
        $quadList = array();

        $c = 0;
        if (count($l10nResourceList)) {
            foreach ($l10nResourceList as $l10nResource) {
                $bNode = new IRI('_' . $c);
                $quadList[] = new Quad($bNode, new IRI(self::NS . 'key'), new IRI($l10nResource->getIdResource()));
                $quadList[] =
                    new Quad($bNode, new IRI(self::NS . 'localization'), new IRI($l10nResource->getIdLocalization()));
                $valueList = $l10nResource->getValueList();
                foreach ($valueList as $locale => $value) {
                    if (!is_int($locale)) {
                        $value .= '@' . $locale;
                    }
                    $quadList[] =
                        new Quad($bNode, new IRI(self::NS . 'value'), new TypedValue($value, RdfConstants::XSD_STRING));
                }
                $c++;
            }
        }

        $jsonLd = JsonLD::fromRdf($quadList);
        $compacted =
            JsonLD::compact($jsonLd, '{"@context": {"l10n" : "' . self::NS . '"}}', array('compactArrays' => false));

        return JsonLD::toString($compacted, true);
    }
}
