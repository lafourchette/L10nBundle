<?php

namespace L10nBundle\Business;

use L10nBundle\Entity\L10nResource;

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
     * @var mixed
     */
    protected $defaultLocalisation;

    /**
     *
     * @var string
     */
    protected $defaultLocale;

    /**
     * @param L10nManagerInterface $l10nManager
     * @param mixed $defaultLocalisation
     * @param mixed $defaultLocale
     */
    public function __construct(L10nManagerInterface $l10nManager, $defaultLocalisation, $defaultLocale)
    {
        $this->l10nManager = $l10nManager;
        $this->defaultLocalisation = $defaultLocalisation;
        $this->defaultLocale = $defaultLocale;
    }

    /**
     *
     * @return mixed
     */
    public function getDefaultLocalisation()
    {
        return $this->defaultLocalisation;
    }

    /**
     *
     * @param mixed $defaultLocalisation
     * @return L10nResource
     */
    public function setDefaultLocalisation($defaultLocalisation)
    {
        $this->defaultLocalisation = $defaultLocalisation;
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
     * @return L10nResource
     */
    public function setDefaultLocale($defaultLocale)
    {
        $this->defaultLocale = $defaultLocale;
        return $this;
    }

    /**
     * Return a localised value
     * @param mixed $idResource
     * @param mixed $idLocalisation
     * @param string $locale
     * @return string
     * @throws ResourceNotFoundException
     */
    public function getL10n($idResource, $idLocalisation = null, $locale = null)
    {
        if (!$idLocalisation) {
            $idLocalisation = $this->defaultLocalisation;
        }
        if (!$locale) {
            $locale = $this->defaultLocale;
        }
        $resource = $this->l10nManager->getL10nResource($idResource, $idLocalisation);
        if (!$resource) {
            throw new ResourceNotFoundException(sprintf('Resource not found for idResource %s and idLocalisation %s', $idResource, $idLocalisation));
        }
        $valueList = $resource->getValueList();

        $value ='';
        if (count($valueList) === 1) {
            $value = reset($valueList);
        } else {
            if (isset($valueList[$locale])) {
                $value = $valueList[$locale];
            } elseif (isset($valueList[$this->defaultLocale])) {
                $value = $valueList[$this->defaultLocale];
            }
        }

        return $value;
    }

}