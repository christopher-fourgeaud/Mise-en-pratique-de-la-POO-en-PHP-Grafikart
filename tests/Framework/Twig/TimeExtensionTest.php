<?php

namespace Tests\Framework\Twig;

use DateTime;
use PHPUnit\Framework\TestCase;
use Framework\Twig\TimeExtension;

class TimeExtensionTest extends TestCase
{
    /**
     * Instance de TimeExtension
     *
     * @var TimeExtension
     */
    private $timeExtension;

    public function setup(): void
    {
        $this->timeExtension = new TimeExtension();
    }

    public function testDateFormat()
    {
        $date = new DateTime();
        $format = 'd/m/Y H:i';

        $result = '<span class="timeago" datetime="' . $date->format(DateTime::ISO8601) . '">' . $date->format($format) . '</span>';

        $this->assertEquals($result, $this->timeExtension->ago($date));
    }
}
