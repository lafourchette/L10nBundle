<?php

namespace L10nBundle\Tests\Utils\Helper;

use L10nBundle\Utils\Helper\L10nCatalogueHelper;

class L10nCatalogueHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var L10nCatalogueHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $catalogueHelper;

    public function setUp()
    {
        $this->catalogueHelper = new L10nCatalogueHelper();
    }

    /**
     * @param mixed $configToFlatten
     * @param array $expectedCatalog
     *
     * @dataProvider createCatalogueProvider
     */
    public function testCreateCatalogue($configToFlatten, array $expectedCatalog)
    {
        $this->assertEquals(
            $expectedCatalog,
            $this->catalogueHelper->createCatalogue($configToFlatten)
        );
    }

    public function createCatalogueProvider()
    {
        return array(
            array(
                'toto',
                array(),
            ),
            array(
                array (1, 2, 3, 42),
                array (),
            ),
            array(
                array ('1' => 1, '2' => 2),
                array (),
            ),
            array(
                array(
                    'resource' => array(
                        'country' => 42
                    ),
                ),
                array(),
            ),
            array(
                array(
                    'resource' => array(
                        'country' => array(
                            array('locale' => 'toto', 'not_value' => 'toto'),
                            array('not_locale' => 'toto', 'value' => 'toto'),
                            array('locale' => 42, 'value' => 'toto'),
                            array('locale' => 42, 'value' => array()),
                        ),
                    ),
                ),
                array(),
            ),
            array(
                array(
                    'titi' => array(
                        'trololo' => array(
                            'sub1' => array(
                                'fr' => array(
                                    array('locale' => 'fr', 'value' => 'trololo sub1 fr fr'),
                                    array('locale' => 'en', 'value' => 'trololo sub1 fr en'),
                                    array('locale' => 'es', 'value' => 42),
                                    array('locale' => 42, 'value' => 'bad locale'),
                                ),
                                'en' => array(
                                    array('not_locale' => 'error', 'value' => 'dropped value'),
                                ),
                                'es' => array(
                                    array('locale' => 'fr', 'value' => 'trololo sub1 es fr'),
                                    array('locale' => 'en', 'not_value' => 'error'),
                                    array('locale' => 'es', 'value' => 'trololo sub1 es es'),
                                ),
                            ),
                            'sub2' => array(
                                'fr' => 'trololo sub2 fr',
                                'en' => 'trololo sub2 en',
                                'es' => 42,
                            ),
                        ),
                        'tralala' => array(
                            'fr' => array(
                                array('locale' => 'fr', 'value' => 'tralala fr fr'),
                            ),
                            'en' => 'tralala en',
                            'es' => 'tralala es',
                        ),
                    ),
                    'tata' => array(
                        'zz' => 'toto',
                        'aa' => '42',
                    ),
                ),
                array(
                    'titi.trololo.sub1' => array(
                        'fr' => array(
                            'fr' => 'trololo sub1 fr fr',
                            'en' => 'trololo sub1 fr en',
                        ),
                        'es' => array(
                            'fr' => 'trololo sub1 es fr',
                            'es' => 'trololo sub1 es es',
                        ),
                    ),
                    'titi.trololo.sub2' => array(
                        'fr' => 'trololo sub2 fr',
                        'en' => 'trololo sub2 en',
                    ),
                    'titi.tralala' => array(
                        'fr' => array('fr' => 'tralala fr fr'),
                        'en' => 'tralala en',
                        'es' => 'tralala es',
                    ),
                    'tata' => array(
                        'zz' => 'toto',
                        'aa' => '42',
                    ),
                ),
            ),
        );
    }
}
