<?php declare(strict_types=1);
/*
 * This file is part of PHP Copy/Paste Detector (PHPCPD).
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\PHPCPD\Detector\Strategy;

use const T_CLOSE_TAG;
use const T_COMMENT;
use const T_DOC_COMMENT;
use const T_INLINE_HTML;
use const T_NS_SEPARATOR;
use const T_OPEN_TAG;
use const T_OPEN_TAG_WITH_ECHO;
use const T_USE;
use const T_WHITESPACE;
use SebastianBergmann\PHPCPD\CodeCloneMap;

abstract class AbstractStrategy
{
    /**
     * @psalm-var array<int,true>
     */
    protected $tokensIgnoreList = [
        T_INLINE_HTML        => true,
        T_COMMENT            => true,
        T_DOC_COMMENT        => true,
        T_OPEN_TAG           => true,
        T_OPEN_TAG_WITH_ECHO => true,
        T_CLOSE_TAG          => true,
        T_WHITESPACE         => true,
        T_USE                => true,
        T_NS_SEPARATOR       => true,
    ];

    /**
     * @psalm-var array<string,array{0: string, 1: int}>
     */
    protected $hashes = [];

    abstract public function processFile(string $file, int $minLines, int $minTokens, CodeCloneMap $result, bool $fuzzy = false): void;
}
