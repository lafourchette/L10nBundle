<?php

namespace L10nBundle\Manager\MongoDb;

use L10nBundle\Entity\L10nResource;
use L10nBundle\Manager\L10nManagerInterface;

/**
 * @@TODO doc
 * @author Cyril Otal
 *
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
    protected  $db;


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
    * @param $idResource
    * @param $idLocalisation
    * @return L10nResource $l10nResource
    */
    public function getL10nResource($idResource, $idLocalisation)
    {

        $l10nCollection = $this->db->L10nResource;
        $query = array('id_resource' => (string)$idResource, 'id_localisation' => (string)$idLocalisation);
        $l10nResult = $l10nCollection->findOne($query);

        $l10nResource = null;

        if (count($l10nResult)) {
            $valueList = array();
            $valueListResult = $l10nResult['values'];
            foreach ($valueListResult as $value) {
                if (isset($value['language'])) {
                    $valueList[$value['language']] = $value['value'];
                } else {
                    $valueList[] = $value['value'];
                }
            }
            $l10nResource = new L10nResource();
            $l10nResource->setIdLocalisation($idLocalisation);
            $l10nResource->setIdResource($idResource);
            $l10nResource->setValueList($valueList);
        }

        return $l10nResource;
    }

    /**
     * Update a L10nResource
     *
     * @param L10nResource $l10nResource
     *     which valueList is a list of values. array('value') if not internationnalised, array('locale_code' => 'value', …) if internationnalised
     */
    public function setL10nResource(L10nResource $l10nResource)
    {

        $idResource = $l10nResource->getIdResource();
        $idLocalisation = $l10nResource->getIdLocalisation();
        $valueList = $l10nResource->getValueList();
        $valueMongoList = array();
        foreach ($valueList as $locale => $value) {
            if ($locale) {
                $valueMongoList[] = array(
                            'language' => $locale,
                            'value' => $value
                        );
            } else {
                $valueMongoList[] = $value;
            }
        }
        $l10nCollection = $this->db->L10nResource;
        $l10nCollection->update(
                array('id_resource' => (string)$idResource, 'id_localisation' => (string)$idLocalisation),
                array(
                        'id_resource' => (string)$idResource,
                        'id_localisation' => (string)$idLocalisation,
                        'value_list' => $valueMongoList
                    ),
                array('upsert' => true)
            );
    }

}
