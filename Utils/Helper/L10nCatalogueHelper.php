<?php

namespace L10nBundle\Utils\Helper;

/**
 * Provide array manipulation method
 */
class L10nCatalogueHelper
{
    const LOCALE_KEY = 'locale';
    const VALUE_KEY = 'value';

    /**
     * Transforms the config into a two dimensional array where a
     * final element is a string or an array of translation.
     * This is done by grouping first keys with dots in order to create
     * the idResource. The idLocalization is kept.
     *
     * It drops the elements that do not have the good structure.
     *
     * @param        $config
     * @param string $prefixKey
     *
     * @return array
     */
    public function createCatalogue($config, $prefixKey = null)
    {
        $flatArray = array();

        $configArrayDimension = $this->configLevel($config);

        if (0 === $configArrayDimension) {
            return $flatArray;
        }

        if (1 === $configArrayDimension) {
            return $this->createResource($flatArray, $config, $prefixKey);
        }

        foreach ($config as $key => $subArray) {
            $key = $prefixKey ? $prefixKey . '.' . $key : strval($key);
            $flatArray = array_merge($flatArray, $this->createCatalogue($subArray, $key));
        }

        return $flatArray;
    }

    /**
     * Returns the level of the given config.
     * The level is 0 if the config is a leaf.
     * The level of an array is the max of the level of its elements plus one.
     *
     * @param $config
     *
     * @return integer
     */
    private function configLevel($config)
    {
        if (!$this->isLeaf($config)) {
            return max(array_map(array($this, 'configLevel'), $config)) + 1;
        }

        return 0;
    }

    /**
     * Returns true if the given config is a leaf.
     * A leaf is either a string value or a non associative array.
     *
     * @param mixed $config
     *
     * @return boolean
     */
    private function isLeaf($config)
    {
        if (is_array($config)) {
            $associativeKeysCount = count(array_filter(array_keys($config), 'is_string'));
            return $associativeKeysCount > 0 ? false : true;
        }

        return true;
    }

    /**
     * @param array  $flatArray
     * @param array  $config
     * @param string $prefixKey
     *
     * @return array
     */
    private function createResource(array $flatArray, array $config, $prefixKey)
    {
        if (null !== $prefixKey) {
            $resourceArray = $this->formatResource($config);
            if (count($resourceArray) > 0) {
                $flatArray[$prefixKey] = $resourceArray;
            }
        }

        return $flatArray;
    }

    /**
     * Formats the given array to be in the format of a catalogue resource.
     * A catalogue resource is the value (array) corresponding to a reource id.
     * The format is an associative array of value.
     * A value is either a string or an associative array where each value is a string.
     *
     * The value that cannot be matched in this format are dropped.
     *
     * @param array $rawResourceArray
     *
     * @return array
     */
    private function formatResource(array $rawResourceArray)
    {
        $formattedResourceArray = array();

        foreach ($rawResourceArray as $key => $leaf) {
            if (is_array($leaf)) {
                $formattedLeafArray = $this->formatLeafArray($leaf);
                if (count($formattedLeafArray) > 0) {
                    $formattedResourceArray[$key] = $formattedLeafArray;
                }
            } elseif (is_string($leaf)) {
                $formattedResourceArray[$key] = $leaf;
            }
        }

        return $formattedResourceArray;
    }

    /**
     * Formats the given array to be in the format of a leaf.
     * A leaf that is an array is an associative array with string values.
     * The input format is a non associative array of associative array.
     * Each associative array must contain:
     * a string value for the key LOCALE_KEY which is the locale in the formatted array
     * and a string value for the key VALUE_KEY which is the value in the formatted array.
     *
     * The value that cannot be matched in this format are dropped
     *
     * @param array $leafArray
     *
     * @return array
     */
    private function formatLeafArray(array $leafArray)
    {
        $formattedLeafArray = array();

        foreach ($leafArray as $leafElement) {
            if (!array_key_exists(self::LOCALE_KEY, $leafElement) ||
                !array_key_exists(self::VALUE_KEY, $leafElement)) {
                continue;
            }

            $locale = $leafElement[self::LOCALE_KEY];
            $value = $leafElement[self::VALUE_KEY];

            if (!is_string($locale) || !is_string($value)) {
                continue;
            }

            $formattedLeafArray[$locale] = $value;
        }

        return $formattedLeafArray;
    }
}
