<?php

namespace L10nBundle\Business;

use L10nBundle\Exception\ResourceNotFoundException;
use L10nBundle\Manager\L10nManagerInterface;

/**
 * @todo doc
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
     * @var string
     */
    protected $fallbackLocale;

    /**
     * @var string
     */
    protected $fallbackLocalization;

    /**
     * @param L10nManagerInterface $l10nManager
     * @param $fallbackLocalization
     * @param $fallbackLocale
     */
    public function __construct(L10nManagerInterface $l10nManager, $fallbackLocalization, $fallbackLocale)
    {
        $this->l10nManager = $l10nManager;
        $this->fallbackLocalization = $fallbackLocalization;
        $this->fallbackLocale = $fallbackLocale;
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
     * @param  string       $defaultLocalization
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
     * @param  string       $defaultLocale
     * @return L10nProvider
     */
    public function setDefaultLocale($defaultLocale)
    {
        $this->defaultLocale = $defaultLocale;

        return $this;
    }

    /**
     * @param $fallbackLocale
     * @return $this
     */
    public function setFallbackLocale($fallbackLocale)
    {
        $this->fallbackLocale = $fallbackLocale;

        return $this;
    }

    /**
     * @return string
     */
    public function getFallbackLocale()
    {
        return $this->fallbackLocale;
    }

    /**
     * @param $fallbackLocale
     * @return $this
     */
    public function setFallbackLocalization($fallbackLocalization)
    {
        $this->fallbackLocalization = $fallbackLocalization;

        return $this;
    }

    /**
     * @return string
     */
    public function getFallbackLocalization()
    {
        return $this->fallbackLocalization;
    }

    /**
     * Return a localised value
     * @param  mixed                     $idResource
     * @param  mixed                     $idLocalization
     * @param  string                    $locale
     * @return string
     * @throws ResourceNotFoundException
     */
    public function getL10n($idResource, $idLocalization = null, $locale = null)
    {
        if (is_null($idLocalization)) {
            $idLocalization = $this->defaultLocalization;
        }
        if (is_null($locale)) {
            $locale = $this->defaultLocale;
        }

        $resource = $this->getResourceOrFallbackResource($idResource, $idLocalization);

        return $resource->getValue($locale, $this->fallbackLocale);
    }

    /**
     * @param $idResource
     * @param $idLocalization
     * @return \L10nBundle\Entity\L10nResource
     * @throws \L10nBundle\Exception\ResourceNotFoundException
     */
    protected function getResourceOrFallbackResource($idResource, $idLocalization)
    {
        $resource = $this->l10nManager->getL10nResource($idResource, $idLocalization);

        if (!$resource) {
            $resource = $this->l10nManager->getL10nResource($idResource, $this->fallbackLocalization);
            if (!$resource) {
                throw new ResourceNotFoundException(
                    sprintf('Resource not found for idResource %s and idLocalization %s', $idResource, $idLocalization)
                );
            }
        }

        return $resource;
    }
}
