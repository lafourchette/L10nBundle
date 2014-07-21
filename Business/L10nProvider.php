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
     *
     * @var string
     */
    protected $defaultLocalization;

    /**
     *
     * @var string
     */
    protected $defaultLocale;

    /**
     * @param L10nManagerInterface $l10nManager
     * @param string $defaultLocalization
     * @param string $defaultLocale
     */
    public function __construct(L10nManagerInterface $l10nManager, $defaultLocalization, $defaultLocale)
    {
        $this->l10nManager = $l10nManager;
        $this->defaultLocalization = $defaultLocalization;
        $this->defaultLocale = $defaultLocale;
    }

    /**
     *
     * @return string
     */
    public function getDefaultLocalization()
    {
        return $this->defaultLocalization;
    }

    /**
     *
     * @param string $defaultLocalization
     * @return L10nProvider
     */
    public function setDefaultLocalization($defaultLocalization)
    {
        $this->defaultLocalization = $defaultLocalization;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getDefaultLocale()
    {
        return $this->defaultLocale;
    }

    /**
     *
     * @param string $defaultLocale
     * @return L10nProvider
     */
    public function setDefaultLocale($defaultLocale)
    {
        $this->defaultLocale = $defaultLocale;
        return $this;
    }

    /**
     * Return a localised value
     * @param mixed $idResource
     * @param mixed $idLocalization
     * @param string $locale
     * @return string
     * @throws ResourceNotFoundException
     */
    public function getL10n($idResource, $idLocalization = null, $locale = null)
    {
        if (!$idLocalization) {
            $idLocalization = $this->defaultLocalization;
        }
        if (!$locale) {
            $locale = $this->defaultLocale;
        }
        $resource = $this->l10nManager->getL10nResource($idResource, $idLocalization);
        if (!$resource) {
            throw new ResourceNotFoundException(
                sprintf('Resource not found for idResource %s and idLocalization %s', $idResource, $idLocalization)
            );
        }
        $valueList = $resource->getValueList();

        $value = '';
        if (count($valueList) === 1) {
            $value = reset($valueList);
        } elseif (isset($valueList[$locale])) {
                $value = $valueList[$locale];
        } elseif (isset($valueList[$this->defaultLocale])) {
            $value = $valueList[$this->defaultLocale];
        }

        return $value;
    }

}
