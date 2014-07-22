<?php

namespace L10nBundle\Manager\Yaml;

use L10nBundle\Entity\L10nResource;
use L10nBundle\Manager\L10nManagerInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * @@TODO doc
 * @author Cyril Otal
 *
 */
class L10nYamlManager implements L10nManagerInterface
{
    const ROOT = 'l10n';

    /**
     * Catalogue file
     * @var array
     */
    private $catalogue;

    /**
     * @param string $filePath
     */
    public function __construct($filePath)
    {
        $this->catalogue = $this->buildCatalogue($filePath);
    }

    /**
     * Parse the YAML file and return an array of data
     * @param string $filePath
     * @return array
     * @throws \InvalidArgumentException
     */
    protected function buildCatalogue($filePath)
    {
        $parse = Yaml::parse($filePath);

        if (!is_array($parse)) {
            throw new \InvalidArgumentException('file "' . $filePath . '" doesn\'t not exist');
        }

        if (!isset($parse[self::ROOT])) {
            throw new \InvalidArgumentException('Missing "' . self::ROOT . '" entry');
        }

        return $parse[self::ROOT];
    }

   /**
    * Return a L10nResource
    * @param $idResource
    * @param $idLocalization
    * @return L10nResource $l10nResource | null
    */
    public function getL10nResource($idResource, $idLocalization)
    {
        $values = array();

        $l10nResource = null;

        if (isset($this->catalogue[$idResource][$idLocalization])) {
            $valueList = $this->catalogue[$idResource][$idLocalization];
            if (!is_array($valueList)) {
                $values[] = $valueList;
            } else {
                $values = $valueList;
            }

            if (!empty($values)) {
                $l10nResource = $this->hydrate($idLocalization, $idResource, $values);
            }
        }

        return $l10nResource;
    }

    /**
     * Return all L10nResources
     * @return array<L10nResource> $l10nResource
     */
    public function getAllL10nResourceList()
    {
        $l10nResourceList = array();

        if (!empty($this->catalogue)) {
            foreach ($this->catalogue as $idResource => $idLocalizationList) {
                foreach ($idLocalizationList as $idLocalization => $valueList) {

                    if (!is_array($valueList)) {
                        $valueList = array($valueList);
                    }

                    $l10nResourceList[] = $this->hydrate($idLocalization, $idResource, $valueList);
                }
            }
        }
        return $l10nResourceList;
    }

    /**
     * Dummy method to respect interface
     *
     * @param L10nResource $l10nResource
     * @throws Exception
     */
    public function setL10nResource(L10nResource $l10nResource)
    {
        throw new \Exception('Can\'t save data in a YAML source');
    }

    /**
     * Build a L10nResource
     * @param $idLocalization
     * @param $idResource
     * @param $valueList
     * @return L10nResource
     */
    protected function hydrate($idLocalization, $idResource, $valueList)
    {
        $l10nResource = new L10nResource();
        $l10nResource->setIdLocalization($idLocalization);
        $l10nResource->setIdResource($idResource);
        $l10nResource->setValueList($valueList);
        return $l10nResource;
    }

    /**
     * @return array
     */
    public function getCatalogue()
    {
        return $this->catalogue;
    }
}
