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
     * @var string
     */
    protected $dataFile;

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
    * @param $idLocalization
    * @return L10nResource $l10nResource
    */
    public function getL10nResource($idResource, $idLocalization)
    {
        $data = Yaml::parse(__DIR__ . $this->dataFile);

        if (!isset($data[self::ROOT])) {
            throw new \InvalidArgumentException('Missing "' . self::ROOT . '" entry');
        }

        $values = array();
        $resourceList = $data[self::ROOT];
        $l10nResource = null;

        if (count($resourceList)) {
            foreach($resourceList as $resource) {
                if ($resource[self::NS . 'key'][self::ATTR_ID] == self::URI_PREFIX . $idResource
                        && $resource[self::NS . 'localization'][self::ATTR_ID] == self::URI_PREFIX . $idLocalization
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
        }


        if (count($values)) {
            $l10nResource = new L10nResource();
            $l10nResource->setIdLocalization($idLocalization);
            $l10nResource->setIdResource($idResource);
            $l10nResource->setValueList($values);
        }

        return $l10nResource;
    }

    /**
     * Return all L10nResources
     * @return array<L10nResource> $l10nResource
     */
    public function getAllL10nResourceList()
    {
        $data = Yaml::parse(__DIR__ . $this->dataFile);

        if (!isset($data[self::ROOT])) {
            throw new \InvalidArgumentException('Missing "' . self::ROOT . '" entry');
        }

        $resourceList = $data[self::ROOT];
        $l10nResourceList = array();

        if (count($resourceList)) {
            foreach($resourceList as $resource) {
                $values = array();
                $idResource = preg_replace('/^' . self::URI_PREFIX . '/' , '', $resource[self::NS . 'key'][self::ATTR_ID]);
                $idLocalization = preg_replace('/^' . self::URI_PREFIX . '/' , '', $resource[self::NS . 'localization'][self::ATTR_ID]);

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
                $l10nResource = new L10nResource();
                $l10nResource->setIdLocalization($idLocalization);
                $l10nResource->setIdResource($idResource);
                $l10nResource->setValueList($values);
                $l10nResourceList[] = $l10nResource;
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