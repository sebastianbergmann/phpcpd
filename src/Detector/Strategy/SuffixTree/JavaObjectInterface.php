<?php

namespace SebastianBergmann\PHPCPD\Detector\Strategy\SuffixTree;

interface JavaObjectInterface
{
    public function hashCode(): int;
    public function equals(JavaObjectInterface $obj): bool;
}
