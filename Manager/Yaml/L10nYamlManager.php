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
    private function buildCatalogue($filePath)
    {
        $parse = Yaml::parse($filePath);

        $data = array();

        if (!isset($parse[self::ROOT])) {
            throw new \InvalidArgumentException('Missing "' . self::ROOT . '" entry');
        }

        foreach ($parse[self::ROOT] as $idResource => $idLocalizationList) {
            $data[$idResource] = array();
            foreach ($idLocalizationList as $idLocalization => $valueList) {
                $data[$idResource][$idLocalization] = $valueList;
            }
        }

        return $data;
    }

   /**
    * Return a L10nResource
    * @param $idResource
    * @param $idLocalization
    * @return L10nResource $l10nResource
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
                $l10nResource = new L10nResource();
                $l10nResource->setIdLocalization($idLocalization);
                $l10nResource->setIdResource($idResource);
                $l10nResource->setValueList($values);
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

                    $l10nResource = new L10nResource();
                    $l10nResource->setIdLocalization($idLocalization);
                    $l10nResource->setIdResource($idResource);
                    $l10nResource->setValueList($valueList);
                    $l10nResourceList[] = $l10nResource;
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
}
