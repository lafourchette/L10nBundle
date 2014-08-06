<?php
/**
 * @author: Olivier Versane
 */

namespace L10nBundle\Twig\Extension;


use L10nBundle\Business\L10nProvider;

class L10nNumberExtension extends \Twig_Extension
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
            'l10nNumber' => new \Twig_SimpleFilter('l10nNumber', array($this, 'getL10nNumber'))
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'l10n.number';
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
    public function getL10nNumber($value, $numberFormat = \NumberFormatter::DECIMAL)
    {
        $fmt = new NumberFormatter($this->locale, $numberFormat);
        return $fmt->format($value);
    }
}
