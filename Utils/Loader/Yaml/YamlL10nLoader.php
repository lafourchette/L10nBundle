<?php

namespace L10nBundle\Utils\Loader\Yaml;

use Symfony\Component\Config\Exception\FileLoaderLoadException;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Yaml\Yaml;

/**
 * Loads the L10n configuration in yaml format.
 *
 * @package L10nBundle\Loader\Yaml
 */
class YamlL10nLoader extends FileLoader
{
    const ROOT = 'l10n';

    /** @var array */
    private $config;

    public function __construct(FileLocatorInterface $locator)
    {
        $this->config = array();
        parent::__construct($locator);
    }

    /**
     * Loads a Yaml file.
     *
     * @param mixed  $file The resource
     * @param string $type The resource type
     *
     * @throws FileLoaderLoadException
     */
    public function load($file, $type = null)
    {
        $path = $this->locator->locate($file);
        if (is_array($path)) {
            return;
        }

        $content = $this->loadFile($path);

        if (!is_array($content)) {
            return;
        }

        $this->parseImports($content, $file);

        $this->loadConfig($content);
    }

    /**
     * @inheritdoc
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'yml' === pathinfo($resource, PATHINFO_EXTENSION);
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Loads a YAML file.
     *
     * @param string $file
     *
     * @throws FileLoaderLoadException
     *
     * @return array The file content
     */
    private function loadFile($file)
    {
        if (!$this->supports($file)) {
            throw new FileLoaderLoadException($file);
        }

        return Yaml::parse($file);
    }

    /**
     * Parses all imports
     *
     * @param array $content
     * @param string $file
     *
     * @return void
     */
    private function parseImports($content, $file)
    {
        if (!isset($content['imports'])) {
            return;
        }

        foreach ($content['imports'] as $import) {
            $this->setCurrentDir(dirname($file));
            $this->import($import['resource'], null, false, $file);
        }
    }

    /**
     * Recursively merges the config of content into current config.
     *
     * @param $content
     */
    private function loadConfig($content)
    {
        if (isset($content[self::ROOT])) {
            $this->config = array_replace_recursive($this->config, $content[self::ROOT]);
        }
    }
}
