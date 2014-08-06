<?php
/**
 * @author: Olivier Versane
 */

namespace L10nBundle\Twig\Extension;


use L10nBundle\Business\L10nProvider;
use L10nBundle\Formater\L10nNumberFormater;

class L10nExtension extends \Twig_Extension
{
    /**
     * @var \L10nBundle\Business\L10nProvider
     */
    private $l10nProvider;

    /**
     * @var L10nNumberFormater
     */
    private $l10nNumberFormater;

    /**
     * @param L10nProvider $l10nProvider
     */
    public function __construct(L10nProvider $l10nProvider, L10nNumberFormater $l10nNumberFormater)
    {
        $this->l10nProvider = $l10nProvider;
        $this->l10nNumberFormater = $l10nNumberFormater;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            'l10n' => new \Twig_SimpleFilter('l10n', array($this, 'getL10n')),
            'l10nCurrency' => new \Twig_SimpleFilter('l10nCurrency', array($this, 'getL10nCurrency')),
            'l10nNumber' => new \Twig_SimpleFilter('l10nNumber', array($this, 'getL10nNumber'))
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'l10n';
    }

    /**
     * @return string
     */
    public function getL10n($key)
    {
        return $this->l10nProvider->getL10n($key);
    }

    /**
     * @return string
     */
    public function getL10nCurrency($value, $currency = null)
    {
        return $this->l10nNumberFormater->getL10nCurrency($value, $currency);
    }

    /**
     * @return string
     */
    public function getL10nNumber($value, $numberFormat = \NumberFormatter::DECIMAL)
    {
        return $this->l10nNumberFormater->getL10nNumber($value, $numberFormat);
    }
}
