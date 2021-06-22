<?php
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
    /**
     * Minimum lines to consider
     * @var int
     */
    private $minLines = 5;

    /**
     * Minimum tokens to consider in a clone.
     * @var int
     */
    private $minTokens = 70;

    /**
     * Edit distance to consider when comparing two clones
     * Only available for the suffix-tree algorithm
     * @var int
     */
    private $editDistance = 5;

    /**
     * Tokens that must be equal to consider a clone
     * Only available for the suffix-tree algorithm
     * @var int
     */
    private $headEquality = 10;

    /**
     * Fuzz variable names
     * suffixtree always makes variables and functions fuzzy
     * @var bool
     */
    private $fuzzy = false;

    /**
     * @param Arguments $arguments
     */
    public function __construct(Arguments $arguments)
    {
        $this->minLines     = $arguments->linesThreshold;
        $this->minTokens    = $arguments->tokensThreshold;
        $this->fuzzy        = $arguments->fuzzy;
        $this->editDistance = $arguments->editDistance;
        $this->headEquality = $arguments->headEquality;
    }

    public function getMinLines(): int
    {
        return $this->minLines;
    }

    public function getMinTokens(): int
    {
        return $this->minTokens;
    }

    public function getHeadEquality(): int
    {
        return $this->headEquality;
    }

    public function getEditDistance(): int
    {
        return $this->editDistance;
    }

    public function getFuzzy(): bool
    {
        return $this->fuzzy;
    }

    public function getMinLines()
    {
        return $this->minLines;
    }

}
