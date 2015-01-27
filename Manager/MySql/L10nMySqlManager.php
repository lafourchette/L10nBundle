<?php

namespace L10nBundle\Manager\MySql;

use L10nBundle\Entity\L10nResource;
use L10nBundle\Manager\L10nManagerInterface;

/**
 * Manager pluged on a MySQL database
 *
 * @author Cyril Otal
 */
class L10nMySqlManager implements L10nManagerInterface
{
    const ID_RESOURCE_KEY = 'id_resource';
    const ID_LOCALISATION_KEY = 'id_localization';
    const LOCALE_KEY = 'locale';
    const VALUE_KEY = 'value';

    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $connection;

    /**
     * @var string
     */
    protected $table;

    /**
     * @param string $host
     * @param string $port
     * @param string $username
     * @param string $password
     * @param string $database
     */
    public function __construct($host, $port, $username, $password, $database, $table)
    {
        $config = new \Doctrine\DBAL\Configuration();

        $connectionParams = array(
                'dbname' => $database,
                'user' => $username,
                'password' => $password,
                'host' => $host,
                'port' => $port,
                'driver' => 'pdo_mysql',
        );
        $this->connection = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

        $this->table = $table;
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
        $sql = 'SELECT `'
            . self::ID_RESOURCE_KEY
            . '`, `'
            . self::ID_LOCALISATION_KEY
            . '`, `'
            . self::LOCALE_KEY
            . '`, `'
            . self::VALUE_KEY
            . '` FROM '
            . $this->table
             . ' WHERE `'
             . self::ID_RESOURCE_KEY
             . '` = :idResource AND `'
             . self::ID_LOCALISATION_KEY
             . '` = :idLocalization'
            ;
        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue('idResource', $idResource);
        $stmt->bindValue('idLocalization', $idLocalization);
        $stmt->execute();

        $l10nResultList = array();

        while ($result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $l10nResultList[] = $result;
        }

        $l10nResource = null;

        if (!empty($l10nResultList)) {
            $valueList = array();
            foreach ($l10nResultList as $l10nResult) {
                if (isset($l10nResult[self::LOCALE_KEY])) {
                    $valueList[$l10nResult[self::LOCALE_KEY]] = $l10nResult[self::VALUE_KEY];
                } else {
                    $valueList[] = $l10nResult[self::VALUE_KEY];
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
        $sql = 'SELECT `'
            . self::ID_RESOURCE_KEY
            . '`, `'
            . self::ID_LOCALISATION_KEY
            . '`, `'
            . self::LOCALE_KEY
            . '`, `'
            . self::VALUE_KEY
            . '` FROM '
            . $this->table
        ;
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        $l10nResultList = array();
        while ($result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $l10nResultList[] = $result;
        }

        $l10nResourceList = array();

        // tree list to manage multiples SQL entries
        // which represent the same resource
        // with several locales
        $l10nResourceTreeList = array();

        if (!empty($l10nResultList)) {
            foreach ($l10nResultList as $l10nResult) {
                if (!isset($l10nResourceTreeList[$l10nResult[self::ID_RESOURCE_KEY]])) {
                    $l10nResourceTreeList[$l10nResult[self::ID_RESOURCE_KEY]] = array();
                }
                if (!isset($l10nResourceTreeList[$l10nResult[self::ID_RESOURCE_KEY]][$l10nResult[self::ID_LOCALISATION_KEY]])) {
                    $l10nResourceTreeList[$l10nResult[self::ID_RESOURCE_KEY]][$l10nResult[self::ID_LOCALISATION_KEY]] = array();
                }
                $valueList = array();
                if (isset($l10nResult[self::LOCALE_KEY])) {
                    $l10nResourceTreeList
                        [$l10nResult[self::ID_RESOURCE_KEY]]
                        [$l10nResult[self::ID_LOCALISATION_KEY]]
                        [$l10nResult[self::LOCALE_KEY]]
                            = $l10nResult[self::VALUE_KEY];
                } else {
                    $l10nResourceTreeList
                        [$l10nResult[self::ID_RESOURCE_KEY]]
                        [$l10nResult[self::ID_LOCALISATION_KEY]]
                        []
                            = $l10nResult[self::VALUE_KEY];
                }
            }

            // flatten the tree
            foreach ($l10nResourceTreeList as $idResource => $l10nResourceTreeRest) {
                foreach ($l10nResourceTreeRest as $idLocalization => $valueList) {
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

        $sqlValueList = array();
        foreach ($valueList as $locale => $value) {
            $sqlValue = ':idResource, :idLocalization, ';
            if ($locale) {
                $sqlValue .= '"' . $locale . '", "';
            } else {
                $sqlValue .= 'null, "';
            }
            $sqlValueList[] = $sqlValue . $value . '"';
        }
        $sql = 'DELETE FROM '
            . $this->table
            . ' WHERE `'
            . self::ID_RESOURCE_KEY
            . '` = :idResource And `'
            . self::ID_LOCALISATION_KEY
            . '` = :idLocalization ;'
            . 'INSERT into '
            . $this->table
            . ' (`'
            . self::ID_RESOURCE_KEY
            . '`, `'
            . self::ID_LOCALISATION_KEY
            . '`, `'
            . self::LOCALE_KEY
            . '`, `'
            . self::VALUE_KEY
            . '`) VALUES ('
            . implode('), (', $sqlValueList)
            . ')'
            ;

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue('idResource', $idResource);
        $stmt->bindValue('idLocalization', $idLocalization);
        $stmt->execute();
    }
}
