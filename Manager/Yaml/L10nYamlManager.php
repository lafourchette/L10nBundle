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
    const URI_PREFIX = '#';
    const NS = 'l10n:';
    const ATTR_ID = '@id';
    const ATTR_LANGUAGE = '@language';
    const ATTR_VALUE = '@value';
    const ROOT = '@graph';


    /**
     * Path to YAML file
     * @@TODO : config
     * @var string
     */
    protected $dataFile = '/../../Resources/data/data.yml';

    /**
     * @param string $dataFile
     */
    public function  __construct($dataFile)
    {
        $this->dataFile = $dataFile;
    }

   /**
    * Return a L10nResource
    * @param $idResource
    * @param $idLocalisation
    * @return L10nResource $l10nResource
    */
    public function getL10nResource($idResource, $idLocalisation)
    {
        $data = Yaml::parse(__DIR__ . $this->dataFile);

        if (!isset($data[self::ROOT])) {
            throw new \InvalidArgumentException('Missing "' . self::ROOT . '" entry');
        }

        $values = array();
        $resourceList = $data[self::ROOT];

        foreach($resourceList as $resource) {
            if ($resource[self::NS . 'key'][self::ATTR_ID] == self::URI_PREFIX . $idResource
                    && $resource[self::NS . 'localisation'][self::ATTR_ID] == self::URI_PREFIX . $idLocalisation
                ) {
                $valueList = $resource[self::NS . 'value'];
                if (!is_array($valueList)) {
                    break;
                }
                foreach ($valueList as $value) {
                    if (isset($value[self::ATTR_LANGUAGE])) {
                        $values[$value[self::ATTR_LANGUAGE]] = $value[self::ATTR_VALUE];
                    } else {
                        $values[] = $value[self::ATTR_VALUE];
                    }
                }
                break;
            }
        }

        $l10nResource = null;

        if (count($values)) {
            $l10nResource = new L10nResource();
            $l10nResource->setIdLocalisation($idLocalisation);
            $l10nResource->setIdResource($idResource);
            $l10nResource->setValueList($values);
        }

        return $l10nResource;
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