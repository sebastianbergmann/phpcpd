<?php declare(strict_types=1);
/*
 * This file is part of PHP Copy/Paste Detector (PHPCPD).
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\PHPCPD\Detector\Strategy\SuffixTree;

abstract class AbstractToken
{
    /** @var int */
    public $tokenCode;

    /** @var int */
    public $line;

    /** @var string */
    public $file;

    /** @var string */
    public $tokenName;

    /** @var string */
    public $content;

    abstract public function __toString(): string;

    abstract public function hashCode(): int;

    abstract public function equals(self $other): bool;
}
