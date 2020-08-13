<?php declare(strict_types=1);
/*
 * This file is part of PHP Copy/Paste Detector (PHPCPD).
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\PHPCPD\Log;

use const ENT_COMPAT;
use function file_put_contents;
use function function_exists;
use function htmlspecialchars;
use function mb_convert_encoding;
use function ord;
use function preg_replace;
use function strlen;
use function utf8_encode;
use DOMDocument;
use SebastianBergmann\PHPCPD\CodeCloneMap;

abstract class AbstractXmlLogger
{
    /**
     * @var DOMDocument
     */
    protected $document;

    /**
     * @var string
     */
    private $filename;

    public function __construct(string $filename)
    {
        $this->document               = new DOMDocument('1.0', 'UTF-8');
        $this->document->formatOutput = true;

        $this->filename = $filename;
    }

    abstract public function processClones(CodeCloneMap $clones);

    protected function flush(): void
    {
        file_put_contents($this->filename, $this->document->saveXML());
    }

    protected function convertToUtf8(string $string): string
    {
        if (!$this->isUtf8($string)) {
            if (function_exists('mb_convert_encoding')) {
                $string = mb_convert_encoding($string, 'UTF-8');
            } else {
                $string = utf8_encode($string);
            }
        }

        return $string;
    }

    protected function isUtf8(string $string): bool
    {
        $length = strlen($string);

        for ($i = 0; $i < $length; $i++) {
            if (ord($string[$i]) < 0x80) {
                $n = 0;
            } elseif ((ord($string[$i]) & 0xE0) === 0xC0) {
                $n = 1;
            } elseif ((ord($string[$i]) & 0xF0) === 0xE0) {
                $n = 2;
            } elseif ((ord($string[$i]) & 0xF0) === 0xF0) {
                $n = 3;
            } else {
                return false;
            }

            for ($j = 0; $j < $n; $j++) {
                if ((++$i === $length) || ((ord($string[$i]) & 0xC0) !== 0x80)) {
                    return false;
                }
            }
        }

        return true;
    }

    protected function escapeForXml(string $string): string
    {
        $string = $this->convertToUtf8($string);

        $string = preg_replace(
            '/[^\x09\x0A\x0D\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]/u',
            "\xEF\xBF\xBD",
            $string
        );

        return htmlspecialchars($string, ENT_COMPAT, 'UTF-8');
    }
}
