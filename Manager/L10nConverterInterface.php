<?php

namespace L10nBundle\Manager;

use L10nBundle\Entity\L10nResource;

/**
 * @@TODO doc
 * @author Cyril Otal
 *
 */
interface L10nConverterInterface
{

   /**
    * Convert a list of L10nResources
    * @param array(L10nResources) $l10nResourceList
    */
    public function convertL10nResourceList(array $l10nResourceList);
}