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

use SebastianBergmann\PHPCPD\Arguments;

final class StrategyConfiguration
{
    private int $minLines;

    private int $minTokens;

    private int $editDistance;

    private int $headEquality;

    private bool $fuzzy;

    public function __construct(Arguments $arguments)
    {
        $this->minLines     = $arguments->linesThreshold();
        $this->minTokens    = $arguments->tokensThreshold();
        $this->fuzzy        = $arguments->fuzzy();
        $this->editDistance = $arguments->editDistance();
        $this->headEquality = $arguments->headEquality();
    }

    public function minLines(): int
    {
        return $this->minLines;
    }

    public function minTokens(): int
    {
        return $this->minTokens;
    }

    public function fuzzy(): bool
    {
        return $this->fuzzy;
    }

    public function headEquality(): int
    {
        return $this->headEquality;
    }

    public function editDistance(): int
    {
        return $this->editDistance;
    }
}
