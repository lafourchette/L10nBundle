<?php

namespace L10nBundle\Manager\MongoDb;

use L10nBundle\Entity\L10nResource;

/**
 * @author Cyril Otal
 */
class L10nMongoDbManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var L10nMongoDbManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $l10nManager;

    public function setUp()
    {
        if (!extension_loaded('mongo')) {
            $this->markTestSkipped('Mongo extension is not loaded');
        }

        $this->l10nManager =
            $this->getMock('L10nBundle\Manager\MongoDb\L10nMongoDbManager', null, array(), 'L10nMongoDbManager', false);
    }

    public function dataGetL10nResource()
    {
        $idResource = 'key';
        $idLocalization = 'idLoc';

        $l10nResult = array(
            'id_resource'     => $idResource,
            'id_localization' => $idLocalization,
            'value_list'      => array(
                array('language' => 'fr-FR', 'value' => 'autre value fr'),
                array('language' => 'en-GB', 'value' => 'other value en')
            )
        );

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
            'id_resource'     => $idResource,
            'id_localization' => $idLocalization,
            'value_list'      => array($value),
        );

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
        $l10nCollection = $this->getMock('\MongoCollection', array('findOne'), array(), '', false);
        $l10nCollection
            ->expects($this->once())
            ->method('findOne')
            ->with(array('id_resource' => $idResource, 'id_localization' => $idLocalization))
            ->will($this->returnValue($requestResult))
        ;

        $this->configL10nManagerMock($l10nCollection);

        $result = $this->l10nManager->getL10nResource($idResource, $idLocalization);

        $this->assertEquals($resource, $result);
    }

    /**
     * @dataProvider dataGetL10nResource
     */
    public function testGetAllL10nResourceList($idResource, $idLocalization, $requestResult, $resource)
    {
        $l10nCollection = $this->getMock('\MongoCollection', array('find'), array(), '', false);
        $l10nCollection
            ->expects($this->once())
            ->method('find')
            ->with()
            ->will($this->returnValue(array($requestResult)))
        ;

        $this->configL10nManagerMock($l10nCollection);

        $result = $this->l10nManager->getAllL10nResourceList();

        $this->assertEquals(array($resource), $result);
    }

    /**
     * @dataProvider dataGetL10nResource
     */
    public function testSetL10nResource($idResource, $idLocalization, $requestResult, $resource)
    {
        $l10nCollection = $this->getMock('\MongoCollection', array('update'), array(), '', false);
        $l10nCollection
            ->expects($this->once())
            ->method('update')
            ->with(
                array('id_resource' => $idResource, 'id_localization' => $idLocalization),
                array(
                    'id_resource'     => $idResource,
                    'id_localization' => $idLocalization,
                    'value_list'      => $requestResult['value_list'],
                ),
                array('upsert' => true)
            )
        ;

        $this->configL10nManagerMock($l10nCollection);

        $this->l10nManager->setL10nResource($resource);
    }

    /**
     * configure the L10nManagerMock with the mocked $l10nCollection
     *
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
