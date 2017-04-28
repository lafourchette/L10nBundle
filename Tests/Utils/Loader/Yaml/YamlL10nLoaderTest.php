<?php
namespace L10nBundle\Tests\Utils\Loader\Yaml;

use L10nBundle\Utils\Loader\Yaml\YamlL10nLoader;
use Symfony\Component\Config\FileLocator;

class YamlL10nLoaderTest extends \PHPUnit_Framework_TestCase
{
    const PATH_EMPTY                = '/path/to/empty.yml';
    const PATH_NO_IMPORT            = '/path/to/no/import.yml';
    const FAKE_PATH_1               = '/fake/path/1.yml';
    const FAKE_PATH_2               = '/fake/path/2.yml';

    public static $parseResults = array(
        self::PATH_EMPTY => array(),
        self::PATH_NO_IMPORT => array(
            'l10n' => array(
                'idLoc1' => array
                (
                    'fr-FR' => 'no import fr',
                    'en-GB' => 'no import en',
                ),
                'key' => array
                (
                    'subKey1' => array
                    (
                        'en-GB' => 'no import en',
                        'sp-SP' => 'no import sp',
                    ),
                    'subKey2' => array
                    (
                        'fr-FR' => 'no import fr',
                        'en-GB' => 'no import en',
                        'sp-SP' => 'no import sp',
                        'ch-CH' => 'no import ch',
                    ),
                ),
            ),
        ),
        self::FAKE_PATH_1 => array(
            'imports' => array(
                array(
                    'resource' => self::FAKE_PATH_2,
                ),
            ),
            'l10n' => array(
                'key' => array
                (
                    'subKey2' => array
                    (
                        'fr-FR' => 'fake path 1 fr',
                        'en-GB' => 'fake path 1 en',
                    ),
                ),
            ),
        ),
        self::FAKE_PATH_2 => array(
            'imports' => array(
                array(
                    'resource' => self::PATH_NO_IMPORT,
                ),
            ),
            'l10n' => array(
                'idLoc2' => array
                (
                    'fr-FR' => 'fake path 2 fr',
                    'en-GB' => 'fake path 2 en',
                ),
                'key' => array
                (
                    'subKey1' => array
                    (
                        'sp-SP' => 'fake path 2 sp',
                    ),
                    'subKey2' => array
                    (
                        'ch-CH' => 'fake path 2 ch',
                    ),
                ),
            ),
        )
    );

    /** @var FileLocator|\PHPUnit_Framework_MockObject_MockObject */
    private $locator;

    public function setUp()
    {
        $this->locator = $this->getMock('Symfony\Component\Config\FileLocator');
    }

    public function test__construct()
    {
        $yamlL10nLoaderTest = new YamlL10nLoader($this->locator);

        $this->assertSame($this->locator, $yamlL10nLoaderTest->getLocator());
        $this->assertEquals(array(), $yamlL10nLoaderTest->getConfig());
    }

    public function testLoadFileEmpty()
    {
        $yamlL10nLoaderTest = $this->loadSetUp(self::PATH_EMPTY);

        $this->assertEquals(array(), $yamlL10nLoaderTest->getConfig());
    }

    public function testLoadNoImports()
    {
        $yamlL10nLoaderTest = $this->loadSetUp(self::PATH_NO_IMPORT);

        $this->assertEquals(self::$parseResults[self::PATH_NO_IMPORT]['l10n'], $yamlL10nLoaderTest->getConfig());
    }

    public function  testLoad()
    {
        $yamlL10nLoaderTest = $this->loadSetUp(self::PATH_NO_IMPORT);

        $expectedConfig = array(
            'idLoc1' => array
            (
                'fr-FR' => 'no import fr',
                'en-GB' => 'no import en',
            ),
            'key' => array
            (
                'subKey1' => array
                (
                    'en-GB' => 'no import en',
                    'sp-SP' => 'fake path 2 sp',
                ),
                'subKey2' => array
                (
                    'sp-SP' => 'no import sp',
                    'ch-CH' => 'fake path 2 ch',
                    'fr-FR' => 'fake path 1 fr',
                    'en-GB' => 'fake path 1 en',
                ),
            ),
            'idLoc2' => array
            (
                'fr-FR' => 'fake path 2 fr',
                'en-GB' => 'fake path 2 en',
            ),
        );

        $loaderConfig = $yamlL10nLoaderTest->getConfig();
        $loaderConfig = krsort($loaderConfig);

        $this->assertEquals(krsort($expectedConfig), $loaderConfig);
    }

    public function testSupports()
    {
        $loader = new YamlL10nLoader(new FileLocator());

        $this->assertTrue($loader->supports('foo.yml'));
        $this->assertFalse($loader->supports('foo.foo'));
        $this->assertFalse($loader->supports('foo'));
        $this->assertFalse($loader->supports(42));
    }

    /**
     * Set up the locator mock locate method to identity for all given paths.
     *
     * @param string $path
     *
     * @return YamlL10nLoader
     */
    private function loadSetUp($path)
    {
        $this->locator->expects($this->any())
            ->method('locate')
            ->with($path)
            ->will(
                $this->returnValue($path)
            );

        $yamlL10nLoaderTest = $this->getMock(
            'L10nBundle\\Utils\\Loader\\Yaml\\YamlL10nLoader',
            array('loadFile'),
            array($this->locator)
        );
        $yamlL10nLoaderTest
            ->expects($this->any())
            ->method('loadFile')
            ->with($path)
            ->will($this->returnValue(self::$parseResults[$path]))
        ;

        $yamlL10nLoaderTest->load($path);

        return $yamlL10nLoaderTest;
    }
}
