<?php declare(strict_types=1);
/*
 * This file is part of PHP Copy/Paste Detector (PHPCPD).
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\PHPCPD\Detector;

use function current;
use function next;
use function sort;
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
final class DetectorTest extends TestCase
{
    /**
     * @dataProvider strategyProvider
     *
     * @psalm-param class-string $strategy
     */
    public function testDetectingSimpleClonesWorks(string $strategy): void
    {
        $clones = (new Detector(new $strategy))->copyPasteDetection(
            [__DIR__ . '/../fixture/Math.php']
        );

        $clones = $clones->clones();
        $files  = $clones[0]->files();
        $file   = current($files);

        $this->assertSame(__DIR__ . '/../fixture/Math.php', $file->name());
        $this->assertSame(75, $file->startLine());

        $file = next($files);

        $this->assertSame(__DIR__ . '/../fixture/Math.php', $file->name());
        $this->assertSame(139, $file->startLine());
        $this->assertSame(59, $clones[0]->numberOfLines());
        $this->assertSame(136, $clones[0]->numberOfTokens());

        $this->assertSame(
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
            $clones[0]->lines()
        );
    }

    /**
     * @dataProvider strategyProvider
     *
     * @psalm-param class-string $strategy
     */
    public function testDetectingExactDuplicateFilesWorks(string $strategy): void
    {
        $clones = (new Detector(new $strategy))->copyPasteDetection(
            [
                __DIR__ . '/../fixture/a.php',
                __DIR__ . '/../fixture/b.php',
            ],
            20,
            60
        );

        $clones = $clones->clones();
        $files  = $clones[0]->files();
        $file   = current($files);

        $this->assertCount(1, $clones);
        $this->assertSame(__DIR__ . '/../fixture/a.php', $file->name());
        $this->assertSame(4, $file->startLine());

        $file = next($files);

        $this->assertSame(__DIR__ . '/../fixture/b.php', $file->name());
        $this->assertSame(4, $file->startLine());
        $this->assertSame(20, $clones[0]->numberOfLines());
        $this->assertSame(60, $clones[0]->numberOfTokens());
    }

    /**
     * @dataProvider strategyProvider
     *
     * @psalm-param class-string $strategy
     */
    public function testDetectingClonesInMoreThanTwoFiles(string $strategy): void
    {
        $clones = (new Detector(new $strategy))->copyPasteDetection(
            [
                __DIR__ . '/../fixture/a.php',
                __DIR__ . '/../fixture/b.php',
                __DIR__ . '/../fixture/c.php',
            ],
            20,
            60
        );

        $clones = $clones->clones();
        $files  = $clones[0]->files();
        sort($files);

        $file = current($files);

        $this->assertCount(1, $clones);
        $this->assertSame(__DIR__ . '/../fixture/a.php', $file->name());
        $this->assertSame(4, $file->startLine());

        $file = next($files);

        $this->assertSame(__DIR__ . '/../fixture/b.php', $file->name());
        $this->assertSame(4, $file->startLine());

        $file = next($files);

        $this->assertSame(__DIR__ . '/../fixture/c.php', $file->name());
        $this->assertSame(4, $file->startLine());
    }

    /**
     * @dataProvider strategyProvider
     *
     * @psalm-param class-string $strategy
     */
    public function testClonesAreIgnoredIfTheySpanLessTokensThanMinTokens(string $strategy): void
    {
        $clones = (new Detector(new $strategy))->copyPasteDetection(
            [
                __DIR__ . '/../fixture/a.php',
                __DIR__ . '/../fixture/b.php',
            ],
            20,
            61
        );

        $this->assertCount(0, $clones->clones());
    }

    /**
     * @dataProvider strategyProvider
     *
     * @psalm-param class-string $strategy
     */
    public function testClonesAreIgnoredIfTheySpanLessLinesThanMinLines(string $strategy): void
    {
        $clones = (new Detector(new $strategy))->copyPasteDetection(
            [
                __DIR__ . '/../fixture/a.php',
                __DIR__ . '/../fixture/b.php',
            ],
            21,
            60
        );

        $this->assertCount(0, $clones->clones());
    }

    /**
     * @dataProvider strategyProvider
     *
     * @psalm-param class-string $strategy
     */
    public function testFuzzyClonesAreFound(string $strategy): void
    {
        $clones = (new Detector(new $strategy))->copyPasteDetection(
            [
                __DIR__ . '/../fixture/a.php',
                __DIR__ . '/../fixture/d.php',
            ],
            5,
            20,
            true
        );

        $this->assertCount(1, $clones->clones());
    }

    /**
     * @dataProvider strategyProvider
     *
     * @psalm-param class-string $strategy
     */
    public function testStripComments(string $strategy): void
    {
        $detector = new Detector(new $strategy);

        $clones = $detector->copyPasteDetection(
            [
                __DIR__ . '/../fixture/e.php',
                __DIR__ . '/../fixture/f.php',
            ],
            8,
            10,
            true
        );

        $this->assertCount(0, $clones->clones());

        $clones = $detector->copyPasteDetection(
            [
                __DIR__ . '/../fixture/e.php',
                __DIR__ . '/../fixture/f.php',
            ],
            7,
            10,
            true
        );

        $this->assertCount(1, $clones->clones());
    }

    /**
     * @psalm-return list<class-string>
     */
    public function strategyProvider(): array
    {
        return [
            [DefaultStrategy::class],
        ];
    }
}
