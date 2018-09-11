<?php
/*
 * This file is part of PHP Copy/Paste Detector (PHPCPD).
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\PHPCPD\Log;

use SebastianBergmann\PHPCPD\CodeCloneMap;

abstract class AbstractXmlLogger
{
    /**
     * @var \DOMDocument
     */
    protected $document;

    /**
     * Constructor.
     */
    public function __construct(string $filename)
    {
        $this->document               = new \DOMDocument('1.0', 'UTF-8');
        $this->document->formatOutput = true;

        $this->filename = $filename;
    }

    /**
     * Processes a list of clones.
     */
    abstract public function processClones(CodeCloneMap $clones);

    /**
     * Writes the XML document to the file.
     */
    protected function flush(): void
    {
        \file_put_contents($this->filename, $this->document->saveXML());
    }

    /**
     * Converts a string to UTF-8 encoding.
     */
    protected function convertToUtf8(string $string): string
    {
        if (!$this->isUtf8($string)) {
            if (\function_exists('mb_convert_encoding')) {
                $string = \mb_convert_encoding($string, 'UTF-8');
            } else {
                $string = \utf8_encode($string);
            }
        }

        return $string;
    }

    /**
     * Checks a string for UTF-8 encoding.
     */
    protected function isUtf8(string $string): string
    {
        $length = \strlen($string);

        for ($i = 0; $i < $length; $i++) {
            if (\ord($string[$i]) < 0x80) {
                $n = 0;
            } elseif ((\ord($string[$i]) & 0xE0) == 0xC0) {
                $n = 1;
            } elseif ((\ord($string[$i]) & 0xF0) == 0xE0) {
                $n = 2;
            } elseif ((\ord($string[$i]) & 0xF0) == 0xF0) {
                $n = 3;
            } else {
                return false;
            }

            for ($j = 0; $j < $n; $j++) {
                if ((++$i == $length) || ((\ord($string[$i]) & 0xC0) != 0x80)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Escapes a string for inclusion inside an XML tag.
     *
     * Converts the string to UTF-8, substitutes the unicode replacement
     * character for every character disallowed in XML, and escapes
     * special characters.
     */
    protected function escapeForXml(string $string): string
    {
        $string = $this->convertToUtf8($string);

        // Substitute the unicode replacement character for disallowed chars
        $string = \preg_replace(
            '/[^\x09\x0A\x0D\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]/u',
            "\xEF\xBF\xBD",
            $string
        );

        return \htmlspecialchars($string, \ENT_COMPAT, 'UTF-8');
    }
}
