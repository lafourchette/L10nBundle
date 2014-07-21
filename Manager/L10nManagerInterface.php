<?php

namespace L10nBundle\Manager;

use L10nBundle\Entity\L10nResource;

/**
 * @@TODO doc
 * @author Cyril Otal
 *
 */
interface L10nManagerInterface
{

   /**
    * Return a L10nResource
    * @param mixed $idResource
    * @param mixed $idLocalization
    * @return L10nResource $l10nResource
    */
    public function getL10nResource($idResource, $idLocalization);

    /**
     * Return all L10nResources
     * @return array<L10nResource> $l10nResource
     */
    public function getAllL10nResourceList();

    /**
     * Save a L10nResource
     * @param L10nResource $l10nResource
     * which valueList is a list of values.
     *      array('value') if not internationnalised,
     *      array('locale_code' => 'value', …) if internationnalised
     * @return void
     */
     public function setL10nResource(L10nResource $l10nResource);
}
