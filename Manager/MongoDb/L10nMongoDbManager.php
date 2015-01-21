<?php

namespace L10nBundle\Manager\MongoDb;

use L10nBundle\Entity\L10nResource;
use L10nBundle\Manager\L10nManagerInterface;

/**
 * @todo doc
 *
 * @author Cyril Otal
 */
class L10nMongoDbManager implements L10nManagerInterface
{
    /**
     * @var \MongoClient
     */
    protected $mongoClient;

    /**
     * @var \MongoDB
     */
    protected $db;

    /**
     * @param string $host
     * @param string $port
     * @param string $username
     * @param string $password
     * @param string $database
     */
    public function __construct($host, $port, $username, $password, $database)
    {
        $this->mongoClient = new \MongoClient('mongodb://' . $username . ':' . $password . '@' . $host . ':' . $port);
        $this->db = $this->mongoClient->selectDB($database);
    }

    /**
     * Return a L10nResource
     *
     * @param $idResource
     * @param $idLocalization
     *
     * @return L10nResource $l10nResource
     */
    public function getL10nResource($idResource, $idLocalization)
    {
        $l10nCollection = $this->db->L10nResource;
        $query = array('id_resource' => (string) $idResource, 'id_localization' => (string) $idLocalization);
        $l10nResult = $l10nCollection->findOne($query);

        $l10nResource = null;

        if (count($l10nResult)) {
            $valueList = array();
            $valueListResult = $l10nResult['value_list'];
            foreach ($valueListResult as $value) {
                if (isset($value['language'])) {
                    $valueList[$value['language']] = $value['value'];
                } else {
                    $valueList[] = $value['value'];
                }
            }
            $l10nResource = new L10nResource();
            $l10nResource->setIdLocalization($idLocalization);
            $l10nResource->setIdResource($idResource);
            $l10nResource->setValueList($valueList);
        }

        return $l10nResource;
    }

    /**
     * Return all L10nResources
     *
     * @return L10nResource[] $l10nResource
     */
    public function getAllL10nResourceList()
    {
        $l10nCollection = $this->db->L10nResource;
        /** @var array $l10nResultList */
        $l10nResultList = $l10nCollection->find();

        $l10nResourceList = array();
        if (count($l10nResultList)) {
            foreach ($l10nResultList as $l10nResult) {
                $valueList = array();
                $valueListResult = $l10nResult['value_list'];
                foreach ($valueListResult as $value) {
                    if (isset($value['language'])) {
                        $valueList[$value['language']] = $value['value'];
                    } else {
                        $valueList[] = $value['value'];
                    }
                }
                $l10nResource = new L10nResource();
                $l10nResource->setIdLocalization($l10nResult['id_localization']);
                $l10nResource->setIdResource($l10nResult['id_resource']);
                $l10nResource->setValueList($valueList);
                $l10nResourceList[] = $l10nResource;
            }
        }

        return $l10nResourceList;
    }

    /**
     * Update a L10nResource
     *
     * @param L10nResource $l10nResource which valueList is a list of values.
     *                                   array('value') if not internationalized,
     *                                   array('locale_code' => 'value', …) if internationalized
     */
    public function setL10nResource(L10nResource $l10nResource)
    {
        $idResource = $l10nResource->getIdResource();
        $idLocalization = $l10nResource->getIdLocalization();
        $valueList = $l10nResource->getValueList();
        $valueMongoList = array();
        foreach ($valueList as $locale => $value) {
            if ($locale) {
                $valueMongoList[] = array(
                    'language' => $locale,
                    'value'    => $value
                );
            } else {
                $valueMongoList[] = array($value);
            }
        }
        $l10nCollection = $this->db->L10nResource;
        $l10nCollection->update(
            array('id_resource' => (string) $idResource, 'id_localization' => (string) $idLocalization),
            array(
                'id_resource'     => (string) $idResource,
                'id_localization' => (string) $idLocalization,
                'value_list'      => $valueMongoList
            ),
            array('upsert' => true)
        );
    }
}
