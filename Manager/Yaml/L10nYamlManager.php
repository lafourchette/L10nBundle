<?php

namespace L10nBundle\Manager\Yaml;

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
    /**
     * Path to YAML file
     * @@TODO : config
     * @var string
     */
    protected $dataFile = '/../../Resources/data/data.yml';

   /**
    * Return a L10nResource
    * @param $idResource
    * @param $idLocalisation
    * @return array $values
    */
    function getL10nResource($idResource, $idLocalisation)
    {
        $data = Yaml::parse(__DIR__ . $this->dataFile);

        if (!isset($data['@graph'])) {
            throw new \InvalidArgumentException('Missing "@graph" entry');
        }

        $values = array();
        $resourceList = $data['@graph'];

        foreach($resourceList as $resource) {
            if ($resource['l10n:key']['@id'] == self::URI_PREFIX . $idResource && $resource['l10n:localisation']['@id'] == self::URI_PREFIX . $idLocalisation) {
                $valueList = $resource['l10n:value'];
                if (!is_array($valueList)) {
                    break;
                }
                foreach ($valueList as $value) {
                    if (isset($value['@language'])) {
                        $values[$value['@language']] = $value['@value'];
                    } else {
                        $values[] = $value['@value'];
                    }
                }
                break;
            }
        }

        return $values;
    }
}