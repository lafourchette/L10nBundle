<?php

namespace L10nBundle\Formater;


class L10nNumberFormater
{
    /**
     * @var string
     */
    private $locale;

    /**
     * @var string currency
     */
    private $currency;

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