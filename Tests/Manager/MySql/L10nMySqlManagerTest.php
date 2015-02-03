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

        $this->l10nManager =
            $this->getMock('L10nBundle\Manager\MySql\L10nMySqlManager', null, array(), 'L10nMySqlManager', false);

        $this->statement =
            $this->getMock('\stdClass', array('bindValue', 'execute', 'fetch'), array(), '', true);
    }

    public function dataGetL10nResource()
    {
        $idResource = 'key';
        $idLocalization = 'idLoc';

        $l10nResult = array(
            array(
                'id_resource' => $idResource,
                'id_localization' => $idLocalization,
                'locale' => 'fr-FR',
                'value' => 'autre value fr',
            ),
            array(
                'id_resource' => $idResource,
                'id_localization' => $idLocalization,
                'locale' => 'en-GB',
                'value' => 'other value en',
            )
        )
        ;

        $valueList = array(
            'fr-FR' => 'autre value fr',
            'en-GB' => 'other value en'
        );

        $l10nResource = new L10nResource();
        $l10nResource->setIdLocalization($idLocalization);
        $l10nResource->setIdResource($idResource);
        $l10nResource->setValueList($valueList);

        $data = array();
        $data[] = array(
            $idResource,
            $idLocalization,
            $l10nResult,
            $l10nResource,
        );

        $value = 'non I18n value';
        $result = array(
            array(
                'id_resource' => $idResource,
                'id_localization' => $idLocalization,
                'locale' => null,
                'value' => $value,
            ),
        )
        ;

        $l10nResource = new L10nResource();
        $l10nResource->setIdLocalization($idLocalization);
        $l10nResource->setIdResource($idResource);
        $l10nResource->setValueList(array($value));

        $data[] = array(
            $idResource,
            $idLocalization,
            $result,
            $l10nResource);

        return $data;
    }

    /**
     * @dataProvider dataGetL10nResource
     */
    public function testGetL10nResource($idResource, $idLocalization, $requestResult, $resource)
    {
        $this->statement
            ->expects($this->at(0))
            ->method('bindValue')
            ->with('idResource', $idResource)
        ;
        $this->statement
            ->expects($this->at(1))
            ->method('bindValue')
            ->with('idLocalization', $idLocalization)
        ;

        $this->statement
            ->expects($this->at(2))
            ->method('execute')
        ;

        $this->statement
            ->expects($this->at(3))
            ->method('fetch')
            ->with(\PDO::FETCH_ASSOC)
            ->will($this->returnValue($requestResult[0]))
        ;

        $at = 4;
        if (isset($requestResult[1])) {
            $this->statement
                ->expects($this->at($at++))
                ->method('fetch')
                ->with(\PDO::FETCH_ASSOC)
                ->will($this->returnValue($requestResult[1]))
            ;
        }
        $this->statement
            ->expects($this->at($at))
            ->method('fetch')
            ->with(\PDO::FETCH_ASSOC)
            ->will($this->returnValue(false))
        ;

        $sql = 'SELECT `id_resource`, `id_localization`, `locale`, `value` FROM '
            . $this->table
            . ' WHERE `id_resource` = :idResource AND `id_localization` = :idLocalization';

        $this->configL10nManagerMock($sql, $this->statement);

        $result = $this->l10nManager->getL10nResource($idResource, $idLocalization);

        $this->assertEquals($resource, $result);
    }

    /**
     * @dataProvider dataGetL10nResource
     */
    public function testGetAllL10nResourceList($idResource, $idLocalization, $requestResult, $resource)
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
            ->will($this->returnValue($requestResult[0]))
        ;

        $at = 2;
        if (isset($requestResult[1])) {
            $this->statement
                ->expects($this->at($at++))
                ->method('fetch')
                ->with(\PDO::FETCH_ASSOC)
                ->will($this->returnValue($requestResult[1]))
            ;
        }
        $this->statement
            ->expects($this->at($at))
            ->method('fetch')
            ->with(\PDO::FETCH_ASSOC)
            ->will($this->returnValue(false))
        ;

        $this->configL10nManagerMock($sql, $this->statement);

        $result = $this->l10nManager->getAllL10nResourceList();

        $this->assertEquals(array($resource), $result);
    }

    /**
     * @dataProvider dataGetL10nResource
     */
    public function testSetL10nResource($idResource, $idLocalization, $requestResult, $resource)
    {
        $this->statement
            ->expects($this->at(0))
            ->method('bindValue')
            ->with('idResource', $idResource)
        ;
        $this->statement
            ->expects($this->at(1))
            ->method('bindValue')
            ->with('idLocalization', $idLocalization)
        ;

        $this->statement
            ->expects($this->at(2))
            ->method('execute')
        ;

        $sql = 'DELETE FROM '
            . $this->table
            . ' WHERE `id_resource` = :idResource And `id_localization` = :idLocalization ;INSERT into '
            . $this->table
            . ' (`id_resource`, `id_localization`, `locale`, `value`) VALUES '
        ;

        $sqlValueList = array();
        foreach ($requestResult as $resourceData) {
            $sqlValueList[] = '(:idResource, :idLocalization, '
                . ($resourceData['locale'] ? '"' . $resourceData['locale'] . '"' : 'null')
                . ', "'
                . $resourceData['value']
                . '")'
            ;
        }
        $sql .= implode(', ', $sqlValueList);

        $this->configL10nManagerMock($sql, $this->statement);
        $this->l10nManager->setL10nResource($resource);
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
