<?php

/* Static Mocking */
namespace Symfony\Component\Yaml;

use L10nBundle\Tests\Utils\Loader\Yaml\YamlL10nLoaderTest;

class Yaml
{
    /**
     * @param string $input
     * @param bool   $exceptionOnInvalidType
     * @param bool   $objectSupport
     *
     * @return array
     */
    public static function parse($input, $exceptionOnInvalidType = false, $objectSupport = false)
    {
        // return static data, for tests
        switch ($input) {
            case YamlL10nLoaderTest::PATH_EMPTY:
                return YamlL10nLoaderTest::$parseResults[YamlL10nLoaderTest::PATH_EMPTY];
            case YamlL10nLoaderTest::PATH_NO_IMPORT:
                return YamlL10nLoaderTest::$parseResults[YamlL10nLoaderTest::PATH_NO_IMPORT];
            case YamlL10nLoaderTest::FAKE_PATH_1:
                return YamlL10nLoaderTest::$parseResults[YamlL10nLoaderTest::FAKE_PATH_1];
            case YamlL10nLoaderTest::FAKE_PATH_2:
                return YamlL10nLoaderTest::$parseResults[YamlL10nLoaderTest::FAKE_PATH_2];
            case YamlL10nLoaderTest::PATH_CIRCULAR_REFERENCE_1:
                return YamlL10nLoaderTest::$parseResults[YamlL10nLoaderTest::PATH_CIRCULAR_REFERENCE_1];
            case YamlL10nLoaderTest::PATH_CIRCULAR_REFERENCE_2:
                return YamlL10nLoaderTest::$parseResults[YamlL10nLoaderTest::PATH_CIRCULAR_REFERENCE_2];
            default:
                return null;
        }
    }
}

namespace L10nBundle\Tests\Utils\Loader\Yaml;

use L10nBundle\Utils\Loader\Yaml\YamlL10nLoader;
use Symfony\Component\Config\FileLocator;

class YamlL10nLoaderTest extends \PHPUnit_Framework_TestCase
{
    const PATH_INVALID              = '/path/to/invalid.ext';
    const PATH_THAT_DOES_NOT_EXIST  = '/path/to/file/that/does/not/exist.yml';
    const PATH_EMPTY                = '/path/to/empty.yml';
    const PATH_NO_IMPORT            = '/path/to/no/import.yml';
    const FAKE_PATH_1               = '/fake/path/1.yml';
    const FAKE_PATH_2               = '/fake/path/2.yml';
    const PATH_CIRCULAR_REFERENCE_1 = '/path/circular/reference/1.yml';
    const PATH_CIRCULAR_REFERENCE_2 = '/path/circular/reference/2.yml';

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
        ),
        self::PATH_CIRCULAR_REFERENCE_1 => array(
            'imports' => array(
                array(
                    'resource' => self::PATH_CIRCULAR_REFERENCE_2,
                ),
            ),
        ),
        self::PATH_CIRCULAR_REFERENCE_2 => array(
            'imports' => array(
                array(
                    'resource' => self::PATH_CIRCULAR_REFERENCE_1,
                ),
            ),
        ),
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

    public function testLoadFileDoesNotExists()
    {
        $yamlL10nLoaderTest = $this->loadSetUp(array(self::PATH_THAT_DOES_NOT_EXIST));

        $this->assertEquals(array(), $yamlL10nLoaderTest->getConfig());
    }

    public function testLoadFileEmpty()
    {
        $yamlL10nLoaderTest = $this->loadSetUp(array(self::PATH_EMPTY));

        $this->assertEquals(array(), $yamlL10nLoaderTest->getConfig());
    }

    /**
     * @expectedException \Symfony\Component\Config\Exception\FileLoaderLoadException
     */
    public function testLoadInvalid()
    {
        $this->loadSetUp(array(self::PATH_INVALID));
    }

    /**
     * @expectedException \Symfony\Component\Config\Exception\FileLoaderImportCircularReferenceException
     */
    public function testLoadCircularReference()
    {
        $this->loadSetUp(
            array(
                self::PATH_CIRCULAR_REFERENCE_1,
                self::PATH_CIRCULAR_REFERENCE_2,
            )
        );
    }

    public function testLoadNoImports()
    {
        $yamlL10nLoaderTest = $this->loadSetUp(array(self::PATH_NO_IMPORT));

        $this->assertEquals(self::$parseResults[self::PATH_NO_IMPORT]['l10n'], $yamlL10nLoaderTest->getConfig());
    }

    public function  testLoad()
    {
        $yamlL10nLoaderTest = $this->loadSetUp(
            array(
                self::FAKE_PATH_1,
                self::FAKE_PATH_2,
                self::PATH_NO_IMPORT,
            )
        );

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

        $this->assertEquals($expectedConfig, $yamlL10nLoaderTest->getConfig());
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
     * @param array $paths
     *
     * @return YamlL10nLoader
     */
    private function loadSetUp(array $paths)
    {
        $this->locator->expects($this->any())
            ->method('locate')
            ->with(call_user_func_array(array($this, 'logicalOr'), $paths))
            ->will(
                $this->returnCallback(
                    function ($path) {
                        return $path;
                    }
                )
            );

        $yamlL10nLoaderTest = new YamlL10nLoader($this->locator);
        $yamlL10nLoaderTest->load($paths[0]);

        return $yamlL10nLoaderTest;
    }
}
