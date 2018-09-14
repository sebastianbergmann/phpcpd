<?php
/*
 * This file is part of PHP Copy/Paste Detector (PHPCPD).
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\PHPCPD\Detector;

if (!\defined('TEST_FILES_PATH')) {
    \define(
      'TEST_FILES_PATH',
      __DIR__ . \DIRECTORY_SEPARATOR . '_files' . \DIRECTORY_SEPARATOR
    );
}

use PHPUnit\Framework\TestCase;
use SebastianBergmann\PHPCPD\Detector\Strategy\DefaultStrategy;

/**
 * @covers \SebastianBergmann\PHPCPD\Detector\Detector
 * @covers \SebastianBergmann\PHPCPD\Detector\Strategy\DefaultStrategy
 *
 * @uses \SebastianBergmann\PHPCPD\CodeClone
 * @uses \SebastianBergmann\PHPCPD\CodeCloneFile
 * @uses \SebastianBergmann\PHPCPD\CodeCloneMap
 */
class DetectorTest extends TestCase
{
    /**
     * @dataProvider strategyProvider
     */
    public function testDetectingSimpleClonesWorks($strategy): void
    {
        $detector = new Detector(new $strategy);

        $clones = $detector->copyPasteDetection(
          [TEST_FILES_PATH . 'Math.php']
        );

        $clones = $clones->getClones();
        $files  = $clones[0]->getFiles();
        $file   = \current($files);

        $this->assertEquals(TEST_FILES_PATH . 'Math.php', $file->getName());
        $this->assertEquals(75, $file->getStartLine());

        $file = \next($files);

        $this->assertEquals(TEST_FILES_PATH . 'Math.php', $file->getName());
        $this->assertEquals(139, $file->getStartLine());
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
     * @dataProvider strategyProvider
     */
    public function testDetectingExactDuplicateFilesWorks($strategy): void
    {
        $detector = new Detector(new $strategy);

        $clones = $detector->copyPasteDetection([
            TEST_FILES_PATH . 'a.php',
            TEST_FILES_PATH . 'b.php',
        ], 20, 60);

        $clones = $clones->getClones();
        $files  = $clones[0]->getFiles();
        $file   = \current($files);

        $this->assertCount(1, $clones);
        $this->assertEquals(TEST_FILES_PATH . 'a.php', $file->getName());
        $this->assertEquals(4, $file->getStartLine());

        $file = \next($files);

        $this->assertEquals(TEST_FILES_PATH . 'b.php', $file->getName());
        $this->assertEquals(4, $file->getStartLine());
        $this->assertEquals(20, $clones[0]->getSize());
        $this->assertEquals(60, $clones[0]->getTokens());
    }

    /**
     * @dataProvider strategyProvider
     */
    public function testDetectingClonesInMoreThanTwoFiles($strategy): void
    {
        $detector = new Detector(new $strategy);

        $clones = $detector->copyPasteDetection(
          [
              TEST_FILES_PATH . 'a.php',
              TEST_FILES_PATH . 'b.php',
              TEST_FILES_PATH . 'c.php',
          ],
          20,
          60
        );

        $clones = $clones->getClones();
        $files  = $clones[0]->getFiles();
        \sort($files);

        $file = \current($files);

        $this->assertCount(1, $clones);
        $this->assertEquals(TEST_FILES_PATH . 'a.php', $file->getName());
        $this->assertEquals(4, $file->getStartLine());

        $file = \next($files);

        $this->assertEquals(TEST_FILES_PATH . 'b.php', $file->getName());
        $this->assertEquals(4, $file->getStartLine());

        $file = \next($files);

        $this->assertEquals(TEST_FILES_PATH . 'c.php', $file->getName());
        $this->assertEquals(4, $file->getStartLine());
    }

    /**
     * @dataProvider strategyProvider
     */
    public function testClonesAreIgnoredIfTheySpanLessTokensThanMinTokens($strategy): void
    {
        $detector = new Detector(new $strategy);

        $clones = $detector->copyPasteDetection(
          [
              TEST_FILES_PATH . 'a.php',
              TEST_FILES_PATH . 'b.php',
          ],
          20,
          61
        );

        $this->assertCount(0, $clones->getClones());
    }

    /**
     * @dataProvider strategyProvider
     */
    public function testClonesAreIgnoredIfTheySpanLessLinesThanMinLines($strategy): void
    {
        $detector = new Detector(new $strategy);

        $clones = $detector->copyPasteDetection(
          [
              TEST_FILES_PATH . 'a.php',
              TEST_FILES_PATH . 'b.php',
          ],
          21,
          60
        );

        $this->assertCount(0, $clones->getClones());
    }

    /**
     * @dataProvider strategyProvider
     */
    public function testFuzzyClonesAreFound($strategy): void
    {
        $detector = new Detector(new $strategy);

        $clones = $detector->copyPasteDetection(
          [
              TEST_FILES_PATH . 'a.php',
              TEST_FILES_PATH . 'd.php',
          ],
          5,
          20,
          true
        );

        $clones = $clones->getClones();

        $this->assertCount(1, $clones);
    }

    /**
     * @dataProvider strategyProvider
     */
    public function testStripComments($strategy): void
    {
        $detector = new Detector(new $strategy);
        $clones   = $detector->copyPasteDetection(
            [
                TEST_FILES_PATH . 'e.php',
                TEST_FILES_PATH . 'f.php',
            ],
            8,
            10,
            true
        );
        $clones = $clones->getClones();
        $this->assertCount(0, $clones);

        $clones = $detector->copyPasteDetection(
            [
                TEST_FILES_PATH . 'e.php',
                TEST_FILES_PATH . 'f.php',
            ],
            7,
            10,
            true
        );
        $clones = $clones->getClones();
        $this->assertCount(1, $clones);
    }

    public function strategyProvider()
    {
        return [
            [DefaultStrategy::class],
        ];
    }
}
