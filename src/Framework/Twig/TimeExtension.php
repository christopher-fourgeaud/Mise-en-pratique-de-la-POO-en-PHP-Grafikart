<?php

namespace Framework\Twig;

use DateTime;
use Twig\TwigFilter;
use Twig\Extension\AbstractExtension;

class TimeExtension extends AbstractExtension
{

    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('ago', [$this, 'ago'], ['is_safe' => ['html']])
        ];
    }

    public function ago(\DateTime $date, string $format = 'd/m/Y H:i')
    {
        return '<span class="timeago" datetime="' . $date->format(DateTime::ISO8601) . '">' .
            $date->format($format) .
            '</span>';
    }
}
