<?php

/*
 * This file is part of the CoreSphereConsoleBundle.
 *
 * (c) Laszlo Korte <me@laszlokorte.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoreSphere\ConsoleBundle\Formatter;

use SensioLabs\AnsiConverter\AnsiToHtmlConverter;
use SensioLabs\AnsiConverter\Theme\SolarizedTheme;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyleInterface;

class HtmlOutputFormatterDecorator implements OutputFormatterInterface
{
    private $formatter;

    public function __construct(OutputFormatterInterface $formatter)
    {
        $this->formatter = $formatter;
    }


    function setDecorated($decorated)
    {
        return $this->formatter->setDecorated($decorated);
    }


    function isDecorated(){
        return $this->formatter->isDecorated();
    }


    function setStyle($name, OutputFormatterStyleInterface $style)
    {
        return $this->formatter->setStyle($name, $style);
    }


    function hasStyle($name)
    {
        return $this->formatter->hasStyle($name);
    }


    function getStyle($name)
    {
        return $this->formatter->getStyle($name);
    }


    function format($message)
    {
        $converter = new AnsiToHtmlConverter();
        $formatted = $this->formatter->format($message);
        $converted = $converter->convert($formatted);

        return $converted;
    }
}
