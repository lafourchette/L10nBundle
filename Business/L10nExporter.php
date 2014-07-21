<?php

namespace L10nBundle\Business;

use L10nBundle\Manager\L10nManagerInterface;
use L10nBundle\Manager\L10nConverterInterface;

/**
 * @@TODO doc
 * @author Cyril Otal
 *
 */
class L10nExporter
{
    /**
     * @var L10nManagerInterface $l10nManager
     */
    protected $l10nManager;

    /**
     * @var L10nConverterInterface
     */
    protected $l10nConverter;

    /**
     * @param L10nManagerInterface $l10nManager
     * @param L10nConverterInterface $l10nConverter
     * @parem string $exportDir
     */
    public function __construct(L10nManagerInterface $l10nManager, L10nConverterInterface $l10nConverter)
    {
        $this->l10nManager = $l10nManager;
        $this->l10nConverter = $l10nConverter;
    }

    /**
     * Export L10nResources in the given filename
     * @param string $filePath name of the export file
     */
    public function exportAllL10nResourceList($filePath = '')
    {
        $l10nResourceList = $this->l10nManager->getAllL10nResourceList();
        $output = $this->l10nConverter->convertL10nResourceList($l10nResourceList);
        $f = fopen($filePath, 'w+');
        fwrite($f, $output);
    }
}
