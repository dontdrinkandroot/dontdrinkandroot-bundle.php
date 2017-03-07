<?php

namespace Dontdrinkandroot\UtilsBundle\Twig;

class IntlExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ddr_intl';
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('ddr_date', [$this, 'dateFilter'], ['needs_environment' => true]),
        ];
    }

    function dateFilter(\Twig_Environment $env, $date, $format = null, $locale = null, $timezone = null)
    {
        /** @var \DateTime $date */
        $date = twig_date_converter($env, $date, $timezone);

        $formatter = \IntlDateFormatter::create(
            $locale,
            \IntlDateFormatter::NONE,
            \IntlDateFormatter::NONE,
            $date->getTimezone()->getName(),
            \IntlDateFormatter::GREGORIAN,
            $format
        );

        return $formatter->format($date->getTimestamp());
    }
}
