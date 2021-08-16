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

/**
 * Small DTO to carry configuration for a strategy.
 * Different algorithms have different configs available.
 */
final class StrategyConfiguration
{
    private int $minLines = 5;

    private int $minTokens = 70;

    private int $editDistance = 5;

    private int $headEquality = 10;

    private bool $fuzzy;

    public function __construct(Arguments $arguments)
    {
        $this->minLines     = $arguments->linesThreshold();
        $this->minTokens    = $arguments->tokensThreshold();
        $this->fuzzy        = $arguments->fuzzy();
        $this->editDistance = $arguments->editDistance();
        $this->headEquality = $arguments->headEquality();
    }

    public function getMinLines(): int
    {
        return $this->minLines;
    }

    public function getMinTokens(): int
    {
        return $this->minTokens;
    }

    public function getFuzzy(): bool
    {
        return $this->fuzzy;
    }

    public function getHeadEquality(): int
    {
        return $this->headEquality;
    }

    public function getEditDistance(): int
    {
        return $this->editDistance;
    }
}
