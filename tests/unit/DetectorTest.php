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
use SebastianBergmann\PHPCPD\ArgumentsBuilder;
use SebastianBergmann\PHPCPD\Detector\Strategy\AbstractStrategy;
use SebastianBergmann\PHPCPD\Detector\Strategy\DefaultStrategy;
use SebastianBergmann\PHPCPD\Detector\Strategy\StrategyConfiguration;

/**
 * @covers \SebastianBergmann\PHPCPD\Arguments
 * @covers \SebastianBergmann\PHPCPD\ArgumentsBuilder
 * @covers \SebastianBergmann\PHPCPD\Detector\Detector
 * @covers \SebastianBergmann\PHPCPD\Detector\Strategy\AbstractStrategy
 * @covers \SebastianBergmann\PHPCPD\Detector\Strategy\DefaultStrategy
 * @covers \SebastianBergmann\PHPCPD\Detector\Strategy\StrategyConfiguration
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
     * @psalm-param AbstractStrategy $strategy
     */
    public function testDetectingSimpleClonesWorks(AbstractStrategy $strategy): void
    {
        $clones = (new Detector($strategy))->copyPasteDetection(
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
     */
    public function testDetectingExactDuplicateFilesWorks(AbstractStrategy $strategy): void
    {
        $argv      = [1 => '.', '--min-lines', '20', '--min-tokens', '50'];
        $arguments = (new ArgumentsBuilder)->build($argv);
        $config    = new StrategyConfiguration($arguments);
        $strategy->setConfig($config);

        $clones = (new Detector($strategy))->copyPasteDetection(
            [
                __DIR__ . '/../fixture/a.php',
                __DIR__ . '/../fixture/b.php',
            ]
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
     */
    public function testDetectingClonesInMoreThanTwoFiles(AbstractStrategy $strategy): void
    {
        $argv      = [1 => '.', '--min-lines', '20', '--min-tokens', '60'];
        $arguments = (new ArgumentsBuilder)->build($argv);
        $config    = new StrategyConfiguration($arguments);
        $strategy->setConfig($config);

        $clones = (new Detector($strategy))->copyPasteDetection(
            [
                __DIR__ . '/../fixture/a.php',
                __DIR__ . '/../fixture/b.php',
                __DIR__ . '/../fixture/c.php',
            ]
        );

        $clones = $clones->clones();
        //var_dump($clones);
        $files = $clones[0]->files();
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
     */
    public function testClonesAreIgnoredIfTheySpanLessTokensThanMinTokens(AbstractStrategy $strategy): void
    {
        $argv      = [1 => '.', '--min-lines', '20', '--min-tokens', '61'];
        $arguments = (new ArgumentsBuilder)->build($argv);
        $config    = new StrategyConfiguration($arguments);
        $strategy->setConfig($config);
        $clones = (new Detector($strategy))->copyPasteDetection(
            [
                __DIR__ . '/../fixture/a.php',
                __DIR__ . '/../fixture/b.php',
            ]
        );

        $this->assertCount(0, $clones->clones());
    }

    /**
     * @dataProvider strategyProvider
     */
    public function testClonesAreIgnoredIfTheySpanLessLinesThanMinLines(AbstractStrategy $strategy): void
    {
        $argv      = [1 => '.', '--min-lines', '21', '--min-tokens', '60'];
        $arguments = (new ArgumentsBuilder)->build($argv);
        $config    = new StrategyConfiguration($arguments);
        $strategy->setConfig($config);
        $clones = (new Detector($strategy))->copyPasteDetection(
            [
                __DIR__ . '/../fixture/a.php',
                __DIR__ . '/../fixture/b.php',
            ]
        );

        $this->assertCount(0, $clones->clones());
    }

    /**
     * @dataProvider strategyProvider
     */
    public function testFuzzyClonesAreFound(AbstractStrategy $strategy): void
    {
        $argv      = [1 => '.', '--min-lines', '5', '--min-tokens', '20', '--fuzzy', 'true'];
        $arguments = (new ArgumentsBuilder)->build($argv);
        $config    = new StrategyConfiguration($arguments);
        $strategy->setConfig($config);
        $clones = (new Detector($strategy))->copyPasteDetection(
            [
                __DIR__ . '/../fixture/a.php',
                __DIR__ . '/../fixture/d.php',
            ]
        );

        $this->assertCount(1, $clones->clones());
    }

    /**
     * @dataProvider strategyProvider
     */
    public function testStripComments(AbstractStrategy $strategy): void
    {
        $argv      = [1 => '.', '--min-lines', '8', '--min-tokens', '10', '--fuzzy', 'true'];
        $arguments = (new ArgumentsBuilder)->build($argv);
        $config    = new StrategyConfiguration($arguments);
        $strategy->setConfig($config);

        $detector = new Detector($strategy);

        $clones = $detector->copyPasteDetection(
            [
                __DIR__ . '/../fixture/e.php',
                __DIR__ . '/../fixture/f.php',
            ]
        );

        $this->assertCount(0, $clones->clones());

        $argv      = [1 => '.', '--min-lines', '7', '--min-tokens', '10', '--fuzzy', 'true'];
        $arguments = (new ArgumentsBuilder)->build($argv);
        $config    = new StrategyConfiguration($arguments);
        $strategy->setConfig($config);

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
     * @psalm-return list<AbstractStrategy>
     */
    public function strategyProvider(): array
    {
        // Build default config.
        $argv      = [1 => '.'];
        $arguments = (new ArgumentsBuilder)->build($argv);
        $config    = new StrategyConfiguration($arguments);

        return [
            [new DefaultStrategy($config)],
        ];
    }
}
