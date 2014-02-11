<?php

namespace L10nBundle\Business;

use L10nBundle\Exception\ResourceNotFoundException;

use L10nBundle\Manager\L10nManagerInterface;

/**
 * @@TODO doc
 * @author Cyril Otal
 *
 */
class L10nProvider
{
    /**
     * @var L10nManagerInterface $l10nManager
     */
    protected $l10nManager;

    /**
     * @param L10nManagerInterface $l10nManager
     */
    function __construct(L10nManagerInterface $l10nManager)
    {
        $this->l10nManager = $l10nManager;
    }

    /**
     *
     * @param mixed $idResource
     * @param mixed $idLocalisation
     * @param string $locale
     * @throws ResourceNotFoundException
     */
    function getL10n($idResource, $idLocalisation, $locale = null)
    {
        $resource = $this->l10nManager->getL10nResource($idResource, $idLocalisation);
        if (!$resource) {
            throw new ResourceNotFoundException(sprintf('Resource not found for idResource %s and idLocalisation %s', $idResource, $idLocalisation));
        }
        $valueList = $resource->getValueList();

        // stuff with $locale

        // ⇒ defaultLocale ?

    }
}