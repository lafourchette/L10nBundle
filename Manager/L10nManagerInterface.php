<?php

namespace L10nBundle\Manager;

use L10nBundle\Entity\L10nResource;

/**
 * Interface for all methods a Manager must provide
 *
 * @author Cyril Otal
 */
interface L10nManagerInterface
{
    /**
     * Return a L10nResource
     *
     * @param string $idResource
     * @param string $idLocalization
     *
     * @return L10nResource $l10nResource
     */
    public function getL10nResource($idResource, $idLocalization);

    /**
     * Return all L10nResources
     *
     * @return L10nResource[] $l10nResource
     */
    public function getAllL10nResourceList();

    /**
     * Save a L10nResource
     *
     * @param L10nResource $l10nResource which valueList is a list of values.
     *                                   array('value') if not internationalized,
     *                                   array('locale_code' => 'value', …) if internationalized
     *
     * @return void
     */
    public function setL10nResource(L10nResource $l10nResource);
}
