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

use SebastianBergmann\PHPCPD\OutOfBoundsException;

/**
 * A list for storing pairs in a specific order.
 *
 * @author $Author: hummelb $
 *
 * @version $Rev: 51770 $
 *
 * @ConQAT.Rating GREEN Hash: 7459D6D0F59028B37DD23DD091BDCEEA
 *
 * @template T
 * @template S
 */
class PairList
{
    /**
     * Version used for serialization.
     *
     * @var int
     */
    private $serialVersionUID = 1;

    /**
     * The current size.
     *
     * @var int
     */
    private $size = 0;

    /**
     * The array used for storing the S.
     *
     * @var S[]
     */
    private $firstElements;

    /**
     * The array used for storing the T.
     *
     * @var T[]
     */
    private $secondElements;

    /**
     * @param S $firstType
     * @param T $secondType
     */
    public function __construct(int $initialCapacity, $firstType, $secondType)
    {
        if ($initialCapacity < 1) {
            $initialCapacity = 1;
        }
        $this->firstElements  = array_fill(0, $initialCapacity, null);
        $this->secondElements = array_fill(0, $initialCapacity, null);
    }

    /** Returns whether the list is empty. */
    public function isEmpty(): bool
    {
        return $this->size == 0;
    }

    /** Returns the size of the list. */
    public function size(): int
    {
        return $this->size;
    }

    /**
     * Add the given pair to the list.
     *
     * @param S $first
     * @param T $second
     */
    public function add($first, $second): void
    {
        $this->firstElements[$this->size]  = $first;
        $this->secondElements[$this->size] = $second;
        $this->size++;
    }

    /** Adds all pairs from another list. */
    public function addAll(self $other): void
    {
        // we have to store this in a local var, as other.$this->size may change if
        // other == this
        $otherSize = $other->size;

        for ($i = 0; $i < $otherSize; $i++) {
            $this->firstElements[$this->size]  = $other->firstElements[$i];
            $this->secondElements[$this->size] = $other->secondElements[$i];
            $this->size++;
        }
    }

    /**
     * Returns the first element at given index.
     *
     * @return S
     */
    public function getFirst(int $i)
    {
        $this->checkWithinBounds($i);

        return $this->firstElements[$i];
    }

    /**
     * Sets the first element at given index.
     *
     * @param S $value
     */
    public function setFirst(int $i, $value): void
    {
        $this->checkWithinBounds($i);
        $this->firstElements[$i] = $value;
    }

    /**
     * Returns the second element at given index.
     *
     * @return T
     */
    public function getSecond(int $i)
    {
        $this->checkWithinBounds($i);

        return $this->secondElements[$i];
    }

    /**
     * Sets the first element at given index.
     *
     * @param T $value
     */
    public function setSecond(int $i, $value): void
    {
        $this->checkWithinBounds($i);
        $this->secondElements[$i] = $value;
    }

    /**
     * Creates a new list containing all first elements.
     *
     * @return S[]
     */
    public function extractFirstList(): array
    {
        $result = [];

        for ($i = 0; $i < $this->size; $i++) {
            $result[] = $this->firstElements[$i];
        }

        return $result;
    }

    /**
     * Creates a new list containing all second elements.
     *
     * @return T[]
     */
    public function extractSecondList(): array
    {
        $result = [];

        for ($i = 0; $i < $this->size; $i++) {
            $result[] = $this->secondElements[$i];
        }

        return $result;
    }

    /** Swaps the entries located at indexes $i and $j. */
    public function swapEntries(int $i, int $j): void
    {
        $tmp1 = $this->getFirst($i);
        $tmp2 = $this->getSecond($i);
        $this->setFirst($i, $this->getFirst($j));
        $this->setSecond($i, $this->getSecond($j));
        $this->setFirst($j, $tmp1);
        $this->setSecond($j, $tmp2);
    }

    /** Clears this list. */
    public function clear(): void
    {
        $this->size = 0;
    }

    /** Removes the last element of the list. */
    public function removeLast(): void
    {
        $this->size--;
    }

    public function hashCode(): int
    {
        $prime = 31;
        $hash  = $this->size;
        $hash  = $prime * $hash + crc32(serialize($this->firstElements));

        return $prime * $hash + crc32(serialize($this->secondElements));
    }

    /**
     * Checks whether the given <code>$i</code> is within the bounds. Throws an
     * exception otherwise.
     */
    private function checkWithinBounds(int $i): void
    {
        if ($i < 0 || $i >= $this->size) {
            throw new OutOfBoundsException('Out of bounds: ' . $i);
        }
    }
}
