<?php
/*
 * This file is part of PHP Copy/Paste Detector (PHPCPD).
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\PHPCPD;

final class CodeCloneMapIterator implements \Iterator
{
    /**
     * @var CodeClone[]
     */
    private $clones = [];

    /**
     * @var int
     */
    private $position = 0;

    public function __construct(CodeCloneMap $clones)
    {
        $this->clones = $clones->getClones();

        \usort(
            $this->clones,
            function (CodeClone $a, CodeClone $b): int {
                return $a->getSize() <=> $b->getSize();
            }
        );

        $this->clones = \array_reverse($this->clones);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function valid(): bool
    {
        return $this->position < \count($this->clones);
    }

    public function key(): int
    {
        return $this->position;
    }

    public function current(): CodeClone
    {
        return $this->clones[$this->position];
    }

    public function next(): void
    {
        $this->position++;
    }
}
