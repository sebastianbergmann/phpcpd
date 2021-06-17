<?php

/*-------------------------------------------------------------------------+
|                                                                          |
| Copyright 2005-2011 The ConQAT Project                                   |
|                                                                          |
| Licensed under the Apache License, Version 2.0 (the "License");          |
| you may not use this file except in compliance with the License.         |
| You may obtain a copy of the License at                                  |
|                                                                          |
|    http://www.apache.org/licenses/LICENSE-2.0                            |
|                                                                          |
| Unless required by applicable law or agreed to in writing, software      |
| distributed under the License is distributed on an "AS IS" BASIS,        |
| WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. |
| See the License for the specific language governing permissions and      |
| limitations under the License.                                           |
+-------------------------------------------------------------------------*/

namespace SebastianBergmann\PHPCPD\Detector\Strategy\SuffixTree;

/**
 * A sentinel character which can be used to produce explicit leaves for all
 * suffixes. The sentinel just has to be appended to the list before handing
 * it to the suffix tree. For the sentinel equality and object identity are
 * the same!
 */
class Sentinel implements JavaObjectInterface
{
    /** The hash value used. */
    private $hash;

    public function __construct()
    {
        $this->hash = (int) rand(0, PHP_INT_MAX);
    }

    public function hashCode(): int
    {
        return $this->hash;
    }

    public function equals(object $obj): bool
    {
        // Original code uses physical object equality, not present in PHP.
        return $obj instanceof Sentinel;
    }

    public function toString(): string
    {
        return "$";
    }
}
