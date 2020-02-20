<?php declare(strict_types=1);
/*
 * This file is part of PHP Copy/Paste Detector (PHPCPD).
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SebastianBergmann\PHPCPD;

final class CodeCloneMap implements \Countable, \IteratorAggregate
{
    /**
     * @var CodeClone[]
     */
    private $clones = [];

    /**
     * @var CodeClone[]
     */
    private $clonesById = [];

    /**
     * @var int
     */
    private $numberOfDuplicatedLines = 0;

    /**
     * @var int
     */
    private $numLines = 0;

    /**
     * @var int
     */
    private $largestCloneSize = 0;

    /**
     * @var array
     */
    private $filesWithClones = [];

    public function addClone(CodeClone $clone): void
    {
        $id = $clone->getId();

        if (!isset($this->clonesById[$id])) {
            $this->clones[]        = $clone;
            $this->clonesById[$id] = $clone;
        } else {
            $existClone = $this->clonesById[$id];

            foreach ($clone->getFiles() as $file) {
                $existClone->addFile($file);
            }
        }

        $this->numberOfDuplicatedLines += $clone->getSize() * (\count($clone->getFiles()) - 1);

        foreach ($clone->getFiles() as $file) {
            if (!isset($this->filesWithClones[$file->getName()])) {
                $this->filesWithClones[$file->getName()] = true;
            }
        }

        $this->largestCloneSize = \max($this->largestCloneSize, $clone->getSize());
    }

    /**
     * @return CodeClone[]
     */
    public function getClones(): array
    {
        return $this->clones;
    }

    public function getPercentage(): string
    {
        if ($this->numLines > 0) {
            $percent = ($this->numberOfDuplicatedLines / $this->numLines) * 100;
        } else {
            $percent = 100;
        }

        return \sprintf('%01.2F%%', $percent);
    }

    public function getNumLines(): int
    {
        return $this->numLines;
    }

    public function setNumLines(int $numLines): void
    {
        $this->numLines = $numLines;
    }

    public function count(): int
    {
        return \count($this->clones);
    }

    public function getNumberOfFilesWithClones(): int
    {
        return \count($this->filesWithClones);
    }

    public function getNumberOfDuplicatedLines(): int
    {
        return $this->numberOfDuplicatedLines;
    }

    public function getIterator(): CodeCloneMapIterator
    {
        return new CodeCloneMapIterator($this);
    }

    public function isEmpty(): bool
    {
        return empty($this->clones);
    }

    public function getAverageSize(): float
    {
        return $this->getNumberOfDuplicatedLines() / $this->count();
    }

    public function getLargestSize(): int
    {
        return $this->largestCloneSize;
    }
}
