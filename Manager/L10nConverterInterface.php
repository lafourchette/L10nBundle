<?php

namespace L10nBundle\Manager;

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
    * @return string
    */
    public function convertL10nResourceList(array $l10nResourceList);
}