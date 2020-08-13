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

use function count;
use function max;
use function sprintf;
use Countable;
use IteratorAggregate;

final class CodeCloneMap implements Countable, IteratorAggregate
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
    private $numberOfLines = 0;

    /**
     * @var int
     */
    private $largestCloneSize = 0;

    /**
     * @var array
     */
    private $filesWithClones = [];

    public function add(CodeClone $clone): void
    {
        $id = $clone->id();

        if (!isset($this->clonesById[$id])) {
            $this->clones[]        = $clone;
            $this->clonesById[$id] = $clone;
        } else {
            $existClone = $this->clonesById[$id];

            foreach ($clone->files() as $file) {
                $existClone->add($file);
            }
        }

        $this->numberOfDuplicatedLines += $clone->numberOfLines() * (count($clone->files()) - 1);

        foreach ($clone->files() as $file) {
            if (!isset($this->filesWithClones[$file->name()])) {
                $this->filesWithClones[$file->name()] = true;
            }
        }

        $this->largestCloneSize = max($this->largestCloneSize, $clone->numberOfLines());
    }

    /**
     * @return CodeClone[]
     */
    public function clones(): array
    {
        return $this->clones;
    }

    public function percentage(): string
    {
        if ($this->numberOfLines > 0) {
            $percent = ($this->numberOfDuplicatedLines / $this->numberOfLines) * 100;
        } else {
            $percent = 100;
        }

        return sprintf('%01.2F%%', $percent);
    }

    public function numberOfLines(): int
    {
        return $this->numberOfLines;
    }

    public function addToNumberOfLines(int $numberOfLines): void
    {
        $this->numberOfLines += $numberOfLines;
    }

    public function count(): int
    {
        return count($this->clones);
    }

    public function numberOfFilesWithClones(): int
    {
        return count($this->filesWithClones);
    }

    public function numberOfDuplicatedLines(): int
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

    public function averageSize(): float
    {
        return $this->numberOfDuplicatedLines() / $this->count();
    }

    public function largestSize(): int
    {
        return $this->largestCloneSize;
    }
}
