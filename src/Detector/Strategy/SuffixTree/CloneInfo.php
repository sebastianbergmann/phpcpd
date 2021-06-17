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

/** Stores information on a clone. */
class CloneInfo
{
    /**
     * Length of the clone in tokens.
     * @var int
     */
    public $length;

    /**
     * Position in word list
     * @var int
     */
    public $position;

    /**
     * Number of occurrences of the clone.
     * @var int
     */
    private $occurrences;

    /**
     * @var PhpToken
     */
    public $token;

    /**
     * Related clones
     * @var PairList
     */
    public $otherClones;

    /** Constructor. */
    public function __construct(int $length, int $position, int $occurrences, PhpToken $token, PairList $otherClones)
    {
        $this->length = $length;
        $this->position = $position;
        $this->occurrences = $occurrences;
        $this->token = $token;
        $this->otherClones = $otherClones;
    }

    /**
     * Returns whether this clone info dominates the given one, i.e. whether
     * both {@link #length} and {@link #occurrences} s not smaller.
     * 
     * @param CloneInfo $ci
     * @param later The amount the given clone starts later than the "this" clone.
     * @return boolean
     */
    public function dominates(CloneInfo $ci, int $later): bool
    {
        return $this->length - $later >= $ci->length && $this->occurrences >= $ci->occurrences;
    }
}
