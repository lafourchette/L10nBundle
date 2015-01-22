<?php

namespace L10nBundle\Manager\MongoDb;

use L10nBundle\Entity\L10nResource;

/**
 * @author Cyril Otal
 *
 */
class L10nMySqlManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    private $idResource;

    /**
     * @var string
     */
    private $idLocalization;

    /**
     * @var array
     */
    private $l10nResult;

    /**
     * @var array
     */
    private $valueList;

    /**
     * @var L10nResource
     */
    private $l10nResource;

    /**
     * @var L10nMySqlManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $l10nManager;

    /**
     * @var \Doctrine\DBAL\Driver\Statement|\PHPUnit_Framework_MockObject_MockObject
     */
    private $statement;

    /**
     * @var string
     */
    private $table;

    public function setUp()
    {
        $this->table = 'l10n_table';
        $this->idResource = 'key';
        $this->idLocalization = 'idLoc';

        $this->l10nResult = array(
            array(
                'id_resource' => $this->idResource,
                'id_localization' => $this->idLocalization,
                'locale' =>'fr-FR',
                'value' => 'autre value fr',
            ),
            array(
                'id_resource' => $this->idResource,
                'id_localization' => $this->idLocalization,
                'locale' =>'en-GB',
                'value' => 'other value en',
            )
        )
        ;

        $this->valueList = array(
            'fr-FR' => 'autre value fr',
            'en-GB' => 'other value en'
        );

        $this->l10nResource = new L10nResource();
        $this->l10nResource->setIdLocalization($this->idLocalization);
        $this->l10nResource->setIdResource($this->idResource);
        $this->l10nResource->setValueList($this->valueList);
        $this->l10nManager =
            $this->getMock('L10nBundle\Manager\MySql\L10nMySqlManager', null, array(), 'L10nMySqlManager', false);

        $this->statement =
            $this->getMock('\stdClass', array('bindValue', 'execute', 'fetch'), array(), '', true);
    }

    public function testGetL10nResource()
    {
        $this->statement
            ->expects($this->at(0))
            ->method('bindValue')
            ->with('idResource', $this->idResource)
        ;
        $this->statement
            ->expects($this->at(1))
            ->method('bindValue')
            ->with('idLocalization', $this->idLocalization)
        ;

        $this->statement
            ->expects($this->at(2))
            ->method('execute')
        ;

        $this->statement
            ->expects($this->at(3))
            ->method('fetch')
            ->with(\PDO::FETCH_ASSOC)
            ->will($this->returnValue($this->l10nResult[0]))
        ;
        $this->statement
            ->expects($this->at(4))
            ->method('fetch')
            ->with(\PDO::FETCH_ASSOC)
            ->will($this->returnValue($this->l10nResult[1]))
        ;
        $this->statement
            ->expects($this->at(5))
            ->method('fetch')
            ->with(\PDO::FETCH_ASSOC)
            ->will($this->returnValue(false))
        ;

        $sql = 'SELECT `id_resource`, `id_localization`, `locale`, `value` FROM '
            . $this->table
            . ' WHERE `id_resource` = :idResource AND `id_localization` = :idLocalization';

        $this->configL10nManagerMock($sql, $this->statement);

        $result = $this->l10nManager->getL10nResource($this->idResource, $this->idLocalization);

        $this->assertEquals($this->l10nResource, $result);
    }

    public function testGetAllL10nResourceList()
    {
        $sql = 'SELECT `id_resource`, `id_localization`, `locale`, `value` FROM '
            . $this->table
        ;
        $this->statement
            ->expects($this->at(0))
            ->method('execute')
        ;
        $this->statement
            ->expects($this->at(1))
            ->method('fetch')
            ->with(\PDO::FETCH_ASSOC)
            ->will($this->returnValue($this->l10nResult[0]))
        ;
        $this->statement
            ->expects($this->at(2))
            ->method('fetch')
            ->with(\PDO::FETCH_ASSOC)
            ->will($this->returnValue($this->l10nResult[1]))
        ;
        $this->statement
            ->expects($this->at(3))
            ->method('fetch')
            ->with(\PDO::FETCH_ASSOC)
            ->will($this->returnValue(false))
        ;

        $this->configL10nManagerMock($sql, $this->statement);

        $result = $this->l10nManager->getAllL10nResourceList($this->idResource, $this->idLocalization);

        $this->assertEquals(array($this->l10nResource), $result);
    }

    public function testSetL10nResource()
    {
        $this->statement
            ->expects($this->at(0))
            ->method('bindValue')
            ->with('idResource', $this->idResource)
        ;
        $this->statement
            ->expects($this->at(1))
            ->method('bindValue')
            ->with('idLocalization', $this->idLocalization)
        ;

        $this->statement
            ->expects($this->at(2))
            ->method('execute')
        ;

        $sql = 'DELETE FROM '
            . $this->table
            . ' WHERE `id_resource` = :idResource And `id_localization` = :idLocalization ;INSERT into '
            . $this->table
            . ' (`id_resource`, `id_localization`, `locale`, `value`) VALUES (:idResource, :idLocalization, "fr-FR", "autre value fr"), (:idResource, :idLocalization, "en-GB", "other value en")';
        $this->configL10nManagerMock($sql, $this->statement);
        $this->l10nManager->setL10nResource($this->l10nResource);
    }

    /**
     * configure the L10nManagerMock with the mocked $l10nCollection
     *
     * @param string $sql
     * @param \PHPUnit_Framework_MockObject_MockObject $statement
     */
    private function configL10nManagerMock($sql, $statement)
    {
        $connection = $this->getMock('\Doctrine\DBAL\Connection', array('prepare'), array(), '', false);
        $connection
            ->expects($this->once())
            ->method('prepare')
            ->with($sql)
            ->will($this->returnValue($statement))
        ;

        $l10nMySqlManagerReflection = new \ReflectionClass('L10nBundle\Manager\MySql\L10nMySqlManager');
        $propC = $l10nMySqlManagerReflection->getProperty('connection');
        $propC->setAccessible(true);
        $propC->setValue($this->l10nManager, $connection);
        $propT = $l10nMySqlManagerReflection->getProperty('table');
        $propT->setAccessible(true);
        $propT->setValue($this->l10nManager, $this->table);
    }
}
