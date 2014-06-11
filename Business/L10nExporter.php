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
    const DEFAULT_EXPORT_FILE_NAME_PREFIX = 'export.l10n.';

    /**
     * @var L10nManagerInterface $l10nManager
     */
    protected $l10nManager;

    /**
     *
     * @var L10nConverterInterface
     */
    protected $l10nConverter;

    /**
     *
     * @var string
     */
    protected $exportDir;

    /**
     * @param L10nManagerInterface $l10nManager
     * @param L10nConverterInterface $l10nConverter
     * @parem string $exportDir
     */
    public function __construct(L10nManagerInterface $l10nManager, L10nConverterInterface $l10nConverter, $exportDir)
    {
        $this->l10nManager = $l10nManager;
        $this->l10nConverter = $l10nConverter;
        $this->exportDir = $exportDir;
    }

    /**
     * Export L10nResources in the given filename
     * @param string $fileName name of the export file
     */
    public function exportAllL10nResourceList($fileName = '')
    {
        if ($fileName == '') {
            $fileName = basename(tempnam($this->exportDir, self::DEFAULT_EXPORT_FILE_NAME_PREFIX));
        }
        $l10nResourceList = $this->l10nManager->getAllL10nResourceList();
        $output = $this->l10nConverter->convertL10nResourceList($l10nResourceList);
        $f = fopen($this->exportDir . '/' . $fileName, 'w+');
        fwrite($f, $output);
    }
}