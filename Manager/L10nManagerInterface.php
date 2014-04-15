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
    * @param mixed $idLocalisation
    * @return L10nResource $l10nResource
    */
    public function getL10nResource($idResource, $idLocalisation);

    /**
     * Save a L10nResource
     * @param mixed $idResource
     * @param mixed $idLocalisation
     * @param array $valueList : list of values. array('value') if not internationnalised, array('locale_code' => 'value', …) if internationnalised

     * @return (boolean ? object ?)
     */
     public function setL10nResource(L10nResource $l10nResource);
}