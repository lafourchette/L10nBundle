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
            'l10n' => new \Twig_SimpleFilter('l10n', array($this, 'getL10n'))
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
}
