<?php

namespace L10nBundle\Manager\MongoDb;

use L10nBundle\Entity\L10nResource;
use L10nBundle\Manager\MongoDb\L10nMongoDbManager;

/**
 * @author Cyril Otal
 *
 */
class L10nMongoDbManagerTest extends \PHPUnit_Framework_TestCase
{

    private $idResource;
    private $idLocalization;
    private $l10nResult;
    private $valueList;
    private $l10nResource;
    private $l10nManager;

    public function setUp()
    {
        if (!extension_loaded('mongo')) {
            $this->markTestSkipped('Mongo extension is not loaded');
        }

        $this->idResource = 'key';
        $this->idLocalization = 'idLoc';

        $this->l10nResult = array(
                'id_resource' => $this->idResource,
                'id_localization' => $this->idLocalization,
                'value_list' => array(
                        array('language' => 'fr-FR', 'value' => 'autre value fr'),
                        array('language' => 'en-GB', 'value' => 'other value en')
                )
        );

        $this->valueList = array
        (
                'fr-FR' => 'autre value fr',
                'en-GB' => 'other value en'
        );

        $this->l10nResource = new L10nResource();
        $this->l10nResource->setIdLocalization($this->idLocalization);
        $this->l10nResource->setIdResource($this->idResource);
        $this->l10nResource->setValueList($this->valueList);
        $this->l10nManager = $this->getMock('L10nBundle\Manager\MongoDb\L10nMongoDbManager', null, array(), 'L10nMongoDbManager', false);

    }

    public function testGetL10nResource()
    {
        $l10nCollection = $this->getMock('\MongoCollection', array('findOne'), array(), '', false);
        $l10nCollection
            ->expects($this->once())
            ->method('findOne')
            ->with(array('id_resource' => $this->idResource, 'id_localization' => $this->idLocalization))
            ->will($this->returnValue($this->l10nResult));

        $this->configL10nManagerMock($l10nCollection);

        $result = $this->l10nManager->getL10nResource($this->idResource, $this->idLocalization);

        $this->assertEquals($this->l10nResource, $result);
    }

    public function testGetAllL10nResourceList()
    {
        $l10nCollection = $this->getMock('\MongoCollection', array('find'), array(), '', false);
        $l10nCollection
        ->expects($this->once())
        ->method('find')
        ->with()
        ->will($this->returnValue(array($this->l10nResult)));

        $this->configL10nManagerMock($l10nCollection);

        $result = $this->l10nManager->getAllL10nResourceList($this->idResource, $this->idLocalization);

        $this->assertEquals(array($this->l10nResource), $result);
    }

    public function testSetL10nResource()
    {
        $valueMongoList =  array(
                array('language' => 'fr-FR', 'value' => 'autre value fr'),
                array('language' => 'en-GB', 'value' => 'other value en')
        );

        $l10nCollection = $this->getMock('\MongoCollection', array('update'), array(), '', false);
        $l10nCollection
            ->expects($this->once())
            ->method('update')
            ->with(
                array('id_resource' => $this->idResource, 'id_localization' => $this->idLocalization),
                array(
                        'id_resource' => $this->idResource,
                        'id_localization' => $this->idLocalization,
                        'value_list' => $valueMongoList
                    ),
                array('upsert' => true)
                )
        ;

        $this->configL10nManagerMock($l10nCollection);

        $this->l10nManager->setL10nResource($this->l10nResource);

    }

    /**
     * configure the L10nManagerMock with the mocked $l10nCollection
     * @param $l10nCollection
     */
    private function configL10nManagerMock($l10nCollection)
    {
        $mongoDb = $this->getMock('\MongoDb', array('__get'), array(), '', false);
        $mongoDb
        ->expects($this->once())
        ->method('__get')
        ->with('L10nResource')
        ->will($this->returnValue($l10nCollection))
        ;

        $l10nMongoDbManagerReflection = new \ReflectionClass('L10nBundle\Manager\MongoDb\L10nMongoDbManager');
        $prop = $l10nMongoDbManagerReflection->getProperty('db');
        $prop->setAccessible(true);
        $prop->setValue($this->l10nManager, $mongoDb);
    }
}