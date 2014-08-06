<?php
/**
 * @author: Olivier Versane
 */

namespace L10nBundle\Twig\Extension;


use L10nBundle\Business\L10nProvider;

class L10nCurrencyExtension extends \Twig_Extension
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
            'l10nCurrency' => new \Twig_SimpleFilter('l10nCurrency', array($this, 'getL10nCurrency'))
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'l10n.currency';
    }

    /**
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }


    /**
     * @return string
     */
    public function getL10nCurrency($value, $currency = null)
    {
        $fmt = new \NumberFormatter($this->locale, NumberFormatter::CURRENCY);
        return $fmt->formatCurrency($value, $currency);
    }
}
