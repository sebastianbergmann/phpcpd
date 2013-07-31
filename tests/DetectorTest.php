<?php
/**
 * phpcpd
 *
 * Copyright (c) 2009-2013, Sebastian Bergmann <sebastian@phpunit.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Sebastian Bergmann nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package   phpcpd
 * @author    Sebastian Bergmann <sebastian@phpunit.de>
 * @copyright 2009-2013 Sebastian Bergmann <sebastian@phpunit.de>
 * @license   http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @since     File available since Release 1.0.0
 */

if (!defined('TEST_FILES_PATH')) {
    define(
      'TEST_FILES_PATH',
      dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR
    );
}

/**
 * Tests for the PHPCPD code analyser.
 *
 * @author    Sebastian Bergmann <sebastian@phpunit.de>
 * @copyright 2009-2013 Sebastian Bergmann <sebastian@phpunit.de>
 * @license   http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @link      http://github.com/sebastianbergmann/phpcpd/tree
 * @since     Class available since Release 1.0.0
 */
class PHPCPD_DetectorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers       SebastianBergmann\PHPCPD\Detector\Detector::copyPasteDetection
     * @covers       SebastianBergmann\PHPCPD\CodeClone::getLines
     * @dataProvider strategyProvider
     */
    public function testDetectingSimpleClonesWorks($strategy)
    {
        $detector = new SebastianBergmann\PHPCPD\Detector\Detector(new $strategy);

        $clones = $detector->copyPasteDetection(
          array(TEST_FILES_PATH . 'Math.php')
        );

        $clones = $clones->getClones();
        $files  = $clones[0]->getFiles();
        $file   = current($files);

        $this->assertEquals(TEST_FILES_PATH . 'Math.php', $file->getName());
        $this->assertEquals(85, $file->getStartLine());

        $file = next($files);

        $this->assertEquals(TEST_FILES_PATH . 'Math.php', $file->getName());
        $this->assertEquals(149, $file->getStartLine());
        $this->assertEquals(59, $clones[0]->getSize());
        $this->assertEquals(136, $clones[0]->getTokens());

        $this->assertEquals(
          '    public function div($v1, $v2)
    {
        $v3 = $v1 / ($v2 + $v1);
        if ($v3 > 14)
        {
            $v4 = 0;
            for ($i = 0; $i < $v3; $i++)
            {
                $v4 += ($v2 * $i);
            }
        }
        $v5 = ($v4 < $v3 ? ($v3 - $v4) : ($v4 - $v3));

        $v6 = ($v1 * $v2 * $v3 * $v4 * $v5);

        $d = array($v1, $v2, $v3, $v4, $v5, $v6);

        $v7 = 1;
        for ($i = 0; $i < $v6; $i++)
        {
            shuffle( $d );
            $v7 = $v7 + $i * end($d);
        }

        $v8 = $v7;
        foreach ( $d as $x )
        {
            $v8 *= $x;
        }

        $v3 = $v1 / ($v2 + $v1);
        if ($v3 > 14)
        {
            $v4 = 0;
            for ($i = 0; $i < $v3; $i++)
            {
                $v4 += ($v2 * $i);
            }
        }
        $v5 = ($v4 < $v3 ? ($v3 - $v4) : ($v4 - $v3));

        $v6 = ($v1 * $v2 * $v3 * $v4 * $v5);

        $d = array($v1, $v2, $v3, $v4, $v5, $v6);

        $v7 = 1;
        for ($i = 0; $i < $v6; $i++)
        {
            shuffle( $d );
            $v7 = $v7 + $i * end($d);
        }

        $v8 = $v7;
        foreach ( $d as $x )
        {
            $v8 *= $x;
        }

        return $v8;
',
          $clones[0]->getLines()
        );
    }

    /**
     * @covers       SebastianBergmann\PHPCPD\Detector\Detector::copyPasteDetection
     * @dataProvider strategyProvider
     */
    public function testDetectingExactDuplicateFilesWorks($strategy)
    {
        $detector = new SebastianBergmann\PHPCPD\Detector\Detector(new $strategy);

        $clones = $detector->copyPasteDetection(array(
          TEST_FILES_PATH . 'a.php',
          TEST_FILES_PATH . 'b.php'
        ), 20, 60);

        $clones = $clones->getClones();
        $files  = $clones[0]->getFiles();
        $file   = current($files);

        $this->assertCount(1, $clones);
        $this->assertEquals(TEST_FILES_PATH . 'a.php', $file->getName());
        $this->assertEquals(4, $file->getStartLine());

        $file = next($files);

        $this->assertEquals(TEST_FILES_PATH . 'b.php', $file->getName());
        $this->assertEquals(4, $file->getStartLine());
        $this->assertEquals(20, $clones[0]->getSize());
        $this->assertEquals(60, $clones[0]->getTokens());
    }

    /**
     * @covers       SebastianBergmann\PHPCPD\Detector\Detector::copyPasteDetection
     * @dataProvider strategyProvider
     */
    public function testDetectingClonesInMoreThanTwoFiles($strategy)
    {
        $detector = new SebastianBergmann\PHPCPD\Detector\Detector(new $strategy);

        $clones = $detector->copyPasteDetection(
          array(
            TEST_FILES_PATH . 'a.php',
            TEST_FILES_PATH . 'b.php',
            TEST_FILES_PATH . 'c.php',
          ),
          20,
          60
        );

        $clones = $clones->getClones();
        $files  = $clones[0]->getFiles();
        $file   = current($files);

        $this->assertCount(1, $clones);
        $this->assertEquals(TEST_FILES_PATH . 'a.php', $file->getName());
        $this->assertEquals(4, $file->getStartLine());

        $file = next($files);

        $this->assertEquals(TEST_FILES_PATH . 'b.php', $file->getName());
        $this->assertEquals(4, $file->getStartLine());

        $file = next($files);

        $this->assertEquals(TEST_FILES_PATH . 'c.php', $file->getName());
        $this->assertEquals(4, $file->getStartLine());
    }

    /**
     * @covers       SebastianBergmann\PHPCPD\Detector\Detector::copyPasteDetection
     * @dataProvider strategyProvider
     */
    public function testClonesAreIgnoredIfTheySpanLessTokensThanMinTokens($strategy)
    {
        $detector = new SebastianBergmann\PHPCPD\Detector\Detector(new $strategy);

        $clones = $detector->copyPasteDetection(
          array(
            TEST_FILES_PATH . 'a.php',
            TEST_FILES_PATH . 'b.php'
          ),
          20,
          61
        );

        $this->assertCount(0, $clones->getClones());
    }

    /**
     * @covers       SebastianBergmann\PHPCPD\Detector\Detector::copyPasteDetection
     * @dataProvider strategyProvider
     */
    public function testClonesAreIgnoredIfTheySpanLessLinesThanMinLines($strategy)
    {
        $detector = new SebastianBergmann\PHPCPD\Detector\Detector(new $strategy);

        $clones = $detector->copyPasteDetection(
          array(
            TEST_FILES_PATH . 'a.php',
            TEST_FILES_PATH . 'b.php'
          ),
          21,
          60
        );

        $this->assertCount(0, $clones->getClones());
    }

    /**
     * @covers       SebastianBergmann\PHPCPD\Detector\Detector::copyPasteDetection
     * @dataProvider strategyProvider
     */
    public function testFuzzyClonesAreFound($strategy)
    {
        $detector = new SebastianBergmann\PHPCPD\Detector\Detector(new $strategy);

        $clones = $detector->copyPasteDetection(
          array(
            TEST_FILES_PATH . 'a.php',
            TEST_FILES_PATH . 'd.php'
          ),
          5,
          20,
          TRUE
        );

        $clones = $clones->getClones();

        $this->assertCount(1, $clones);
    }

    /**
     * @covers       SebastianBergmann\PHPCPD\Detector\Detector::copyPasteDetection
     * @dataProvider strategyProvider
     */
    public function testStripComments($strategy)
    {
        $detector = new SebastianBergmann\PHPCPD\Detector\Detector(new $strategy);
        $clones = $detector->copyPasteDetection(
            array(
                TEST_FILES_PATH . 'e.php',
                TEST_FILES_PATH . 'f.php'
            ),
            8,
            10,
            TRUE
        );
        $clones = $clones->getClones();
        $this->assertCount(0, $clones);

        $clones = $detector->copyPasteDetection(
            array(
                TEST_FILES_PATH . 'e.php',
                TEST_FILES_PATH . 'f.php'
            ),
            7,
            10,
            TRUE
        );
        $clones = $clones->getClones();
        $this->assertCount(1, $clones);
    }

    public function strategyProvider()
    {
        return array(
          array('SebastianBergmann\\PHPCPD\\Detector\\Strategy\\DefaultStrategy')
        );
    }
}
