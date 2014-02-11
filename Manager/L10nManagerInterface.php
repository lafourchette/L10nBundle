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
    * @param $idResource
    * @param $idLocalisation
    * @return array $values
    */
    function getL10nResource($idResource, $idLocalisation);
}