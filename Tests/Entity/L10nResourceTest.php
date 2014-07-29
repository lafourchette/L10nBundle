<?php

namespace L10nBundle\Entity;

use L10nBundle\Entity\L10nResource;
use L10nBundle\Exception\ResourceNotFoundException;
use L10nBundle\Manager\L10nManagerInterface;
use L10nBundle\Business\L10nProvider;

/**
 * @author Cyril Otal
 *
 */
class L10nResourceTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     * @var string
     */
    protected $key = 'some_key';

    /**
     *
     * @dataProvider getData
     */
    public function testGetValue($valueList, $locale, $fallbackLocale, $return)
    {
        $l10nResource = new L10nResource();
        $l10nResource->setValueList($valueList);
        $l10nResource->setIdResource($this->key);
        $this->assertEquals($return, $l10nResource->getValue($locale, $fallbackLocale));
    }

   public function getData()
   {
       return array(
           array(
               array('+330102'),
               'fr',
               'es',
               '+330102'
               ),
           array(
               array('fr' => 'valeur', 'en' => 'value'),
               'fr',
               'es',
               'valeur'
               ),
           array(
               array('fr' => 'valeur', 'en' => 'value'),
               'ja',
               'en',
               'value'
               ),
           array(
               array('fr' => 'valeur', 'en' => 'value'),
               'ja',
               'es',
               $this->key
               ),
           );
   }
}
