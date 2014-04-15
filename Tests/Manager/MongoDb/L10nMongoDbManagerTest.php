<?php

namespace L10nBundle\Manager\MongoDb;



use L10nBundle\Entity\L10nResource;
use L10nBundle\Manager\L10nManagerInterface;
use L10nBundle\Manager\MongoDb\L10nMongoDbManager;


/**
 * @author Cyril Otal
 *
 */
class L10nMongoDbManagerTest extends \PHPUnit_Framework_TestCase
{


    public function testGetL10nResource()
    {
        $idResource = 'key';
        $idLocalisation = 'idLoc';

        $l10nResult = array(
            'values' => array(
                    array('language' => 'fr-fr', 'value' => 'autre value fr'),
                    array('language' => 'en-gb', 'value' => 'other value en')
                )
            );

        $l10nManager = $this->getMock('L10nBundle\Manager\MongoDb\L10nMongoDbManager', null, array(), 'L10nMongoDbManager', false);
        $l10nCollection = $this->getMock('\MongoCollection', array('findOne'), array(), '', null);
        $l10nCollection
            ->expects($this->once())
            ->method('findOne')
            ->with(array('id_resource' => $idResource, 'id_localisation' => $idLocalisation))
            ->will($this->returnValue($l10nResult));

        $mongoDb = $this->getMock('\MongoDb', array('__get'), array(), '', null);
        $mongoDb
            ->expects($this->once())
            ->method('__get')
            ->with('L10nResource')
            ->will($this->returnValue($l10nCollection));

        $l10nMongoDbManagerReflection = new \ReflectionClass('L10nBundle\Manager\MongoDb\L10nMongoDbManager');
        $prop = $l10nMongoDbManagerReflection->getProperty('db');
        $prop->setAccessible(true);
        $prop->setValue($l10nManager, $mongoDb);


        $values = $l10nManager->getL10nResource($idResource, $idLocalisation);

        $expected = array
            (
                'fr-fr' => 'autre value fr',
                'en-gb' => 'other value en'
            );

        $this->assertEquals($expected, $values);

    }

    public function testSetL10nResource()
    {
        $idResource = 'another_id';
        $idLocalisation = 'idLoc';

        $valueList =  array('fr-fr' => 'autre value fr', 'en-gb' => 'other value en');
        $valueMongoList =  array(
                array('language' => 'fr-fr', 'value' => 'autre value fr'),
                array('language' => 'en-gb', 'value' => 'other value en')
        );

        $l10nManager = $this->getMock('L10nBundle\Manager\MongoDb\L10nMongoDbManager', null, array(), 'L10nMongoDbManager', false);
        $l10nCollection = $this->getMock('\MongoCollection', array('update'), array(), '', null);
        $l10nCollection
            ->expects($this->once())
            ->method('update')
            ->with(
                array('id_resource' => $idResource, 'id_localisation' => $idLocalisation),
                array(
                        'id_resource' => $idResource,
                        'id_localisation' => $idLocalisation,
                        'value_list' => $valueMongoList
                    ),
                array('upsert' => true)
                )
        ;

        $mongoDb = $this->getMock('\MongoDb', array('__get'), array(), '', null);
        $mongoDb
            ->expects($this->once())
            ->method('__get')
            ->with('L10nResource')
            ->will($this->returnValue($l10nCollection))
        ;

        $l10nMongoDbManagerReflection = new \ReflectionClass('L10nBundle\Manager\MongoDb\L10nMongoDbManager');
        $prop = $l10nMongoDbManagerReflection->getProperty('db');
        $prop->setAccessible(true);
        $prop->setValue($l10nManager, $mongoDb);

        $l10nManager->setL10nResource($idResource, $idLocalisation, $valueList);

    }


}