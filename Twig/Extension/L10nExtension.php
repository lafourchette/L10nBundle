<?php
/**
 * @author: Olivier Versane
 */

namespace L10nBundle\Twig\Extension;


use L10nBundle\Business\L10nProvider;

class L10nExtension extends \Twig_Extension
{
    /**
     * @var \L10nBundle\Business\L10nProvider
     */
    private $l10nProvider;

    /**
     * @var string
     */
    private $locale;

    /**
     * @var string
     */
    private $currency;

    /**
     * @param L10nProvider $l10nProvider
     */
    public function __construct(L10nProvider $l10nProvider)
    {
        $this->l10nProvider = $l10nProvider;
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
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * @param $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
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
        if (!$currency) {
            $currency = $this->currency;
        }

        $fmt = new \NumberFormatter($this->locale, \NumberFormatter::CURRENCY);
        return $fmt->formatCurrency($value, $currency);
    }

    /**
     * @return string
     */
    public function getL10nNumber($value, $numberFormat = \NumberFormatter::DECIMAL)
    {
        $fmt = new \NumberFormatter($this->locale, $numberFormat);
        return $fmt->format($value);
    }
}
