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

class Token extends AbstractToken
{
    public function __construct(
        int $tokenCode,
        string $tokenName,
        int $line,
        string $file,
        string $content
    ) {
        $this->tokenCode = $tokenCode;
        $this->tokenName = $tokenName;
        $this->line      = $line;
        $this->content   = $content;
        $this->file      = $file;
    }

    public function __toString(): string
    {
        return $this->tokenName;
    }

    public function hashCode(): int
    {
        return crc32($this->content);
    }

    public function equals(AbstractToken $other): bool
    {
        return $other->hashCode() === $this->hashCode();
    }
}
