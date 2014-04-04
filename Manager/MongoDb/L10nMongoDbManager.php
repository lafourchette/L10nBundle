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


    public function __construct()
    {
        $this->mongoClient = new \MongoClient();
        $this->db = $this->mongoClient->selectDB('doctrine_odm');
    }

   /**
    * Return a L10nResource
    * @param $idResource
    * @param $idLocalisation
    * @return array $values
    */
    public function getL10nResource($idResource, $idLocalisation)
    {

        $l10nCollection = $this->db->L10nResource;
        $query = array('id_resource' => (string)$idResource, 'id_localisation' => (string)$idLocalisation);
        $l10nResult = $l10nCollection->findOne($query);

        $result = null;

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
        }

        return $valueList;
    }

    /**
     * Update a L10nResource
     *
     * @param mixed $idResource
     * @param mixed $idLocalisation
     * @param array $valueList : list of values. array('value') if not internationnalised, array('locale_code' => 'value', …) if internationnalised
     */
    public function setL10nResource($idResource, $idLocalisation, $valueList)
    {

    }

}
