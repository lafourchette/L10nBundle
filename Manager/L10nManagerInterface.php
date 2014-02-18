<?php

namespace L10nBundle\Manager;

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
    * @return array $valueList
    */
    function getL10nResource($idResource, $idLocalisation);

    /**
     * Save a L10nResource
     * @param mixed $idResource
     * @param mixed $idLocalisation
     * @param array $valueList : list of values. array('value') if not internationnalised, array('locale_code' => 'value', …) if internationnalised

     * @return (boolean ? object ?)
     */
     function setL10nResource($idResource, $idLocalisation, $valueList);
}