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

/** Stores information on a clone. */
class CloneInfo
{
    /**
     * Length of the clone in tokens.
     *
     * @var int
     */
    public $length;

    /**
     * Position in word list.
     *
     * @var int
     */
    public $position;

    /**
     * @var AbstractToken
     */
    public $token;

    /**
     * Related clones.
     *
     * @var PairList
     */
    public $otherClones;

    /**
     * Number of occurrences of the clone.
     *
     * @var int
     */
    private $occurrences;

    /** Constructor. */
    public function __construct(int $length, int $position, int $occurrences, AbstractToken $token, PairList $otherClones)
    {
        $this->length      = $length;
        $this->position    = $position;
        $this->occurrences = $occurrences;
        $this->token       = $token;
        $this->otherClones = $otherClones;
    }

    /**
     * Returns whether this clone info dominates the given one, i.e. whether
     * both {@link #length} and {@link #occurrences} s not smaller.
     *
     * @param int $later the amount the given clone starts later than the "this" clone
     */
    public function dominates(self $ci, int $later): bool
    {
        return $this->length - $later >= $ci->length && $this->occurrences >= $ci->occurrences;
    }
}
