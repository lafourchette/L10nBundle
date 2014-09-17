<?php

namespace L10nBundle\Manager;

use L10nBundle\Entity\L10nResource;

/**
 * @todo doc
 *
 * @author Cyril Otal
 */
interface L10nConverterInterface
{
    /**
     * Convert a list of L10nResources
     *
     * @param L10nResource[] $l10nResourceList
     *
     * @return string
     */
    public function convertL10nResourceList(array $l10nResourceList);
}
