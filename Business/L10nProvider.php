<?php

namespace L10nBundle\Business;

use L10nBundle\Exception\ResourceNotFoundException;
use L10nBundle\Manager\L10nManagerInterface;
use L10nBundle\Utils\Resolver\L10nResolver;

/**
 * Class with all methods to retrieve L10nResources,
 * including fallback managment
 *
 * @author Cyril Otal
 */
class L10nProvider
{
    /**
     * @var L10nManagerInterface
     */
    protected $l10nManager;

    /**
     * @var L10nResolver
     */
    protected $l10nResolver;

    /**
     * @var string
     */
    protected $defaultLocalization;

    /**
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
     * @param L10nResolver         $l10nResolver
     * @param string               $fallbackLocalization
     * @param string               $fallbackLocale
     */
    public function __construct(
        L10nManagerInterface $l10nManager,
        L10nResolver $l10nResolver,
        $fallbackLocalization,
        $fallbackLocale
    ) {
        $this->l10nManager = $l10nManager;
        $this->l10nResolver = $l10nResolver;
        $this->fallbackLocalization = $fallbackLocalization;
        $this->fallbackLocale = $fallbackLocale;
    }

    /**
     * @return string
     */
    public function getDefaultLocalization()
    {
        return $this->defaultLocalization;
    }

    /**
     * @param string $defaultLocalization
     *
     * @return L10nProvider
     */
    public function setDefaultLocalization($defaultLocalization)
    {
        $this->defaultLocalization = $defaultLocalization;

        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultLocale()
    {
        return $this->defaultLocale;
    }

    /**
     * @param string $defaultLocale
     *
     * @return L10nProvider
     */
    public function setDefaultLocale($defaultLocale)
    {
        $this->defaultLocale = $defaultLocale;

        return $this;
    }

    /**
     * @param $fallbackLocale
     *
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
     * @param $fallbackLocalization
     *
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
     * Return a localized value
     *
     * @param mixed  $idResource
     * @param mixed  $idLocalization
     * @param string $locale
     *
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
        $value = $resource->getValue($locale, $this->fallbackLocale);

        return $this->l10nResolver->resolve($value);
    }

    /**
     * @param $idResource
     * @param $idLocalization
     *
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
