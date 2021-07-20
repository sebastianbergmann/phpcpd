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

use PHPUnit\Framework\TestCase;
use SebastianBergmann\PHPCPD\ArgumentsBuilder;
use SebastianBergmann\PHPCPD\Detector\Strategy\DefaultStrategy;
use SebastianBergmann\PHPCPD\Detector\Strategy\StrategyConfiguration;
use SebastianBergmann\PHPCPD\Detector\Strategy\SuffixTreeStrategy;

/**
 * @covers \SebastianBergmann\PHPCPD\Arguments
 * @covers \SebastianBergmann\PHPCPD\ArgumentsBuilder
 * @covers \SebastianBergmann\PHPCPD\Detector\Detector
 * @covers \SebastianBergmann\PHPCPD\Detector\Strategy\AbstractStrategy
 * @covers \SebastianBergmann\PHPCPD\Detector\Strategy\DefaultStrategy
 * @covers \SebastianBergmann\PHPCPD\Detector\Strategy\StrategyConfiguration
 * @covers \SebastianBergmann\PHPCPD\Detector\Strategy\SuffixTree\ApproximateCloneDetectingSuffixTree
 * @covers \SebastianBergmann\PHPCPD\Detector\Strategy\SuffixTree\CloneInfo
 * @covers \SebastianBergmann\PHPCPD\Detector\Strategy\SuffixTree\PairList
 * @covers \SebastianBergmann\PHPCPD\Detector\Strategy\SuffixTree\Sentinel
 * @covers \SebastianBergmann\PHPCPD\Detector\Strategy\SuffixTree\SuffixTree
 * @covers \SebastianBergmann\PHPCPD\Detector\Strategy\SuffixTree\SuffixTreeHashTable
 * @covers \SebastianBergmann\PHPCPD\Detector\Strategy\SuffixTree\Token
 * @covers \SebastianBergmann\PHPCPD\Detector\Strategy\SuffixTreeStrategy
 *
 * @uses \SebastianBergmann\PHPCPD\CodeClone
 * @uses \SebastianBergmann\PHPCPD\CodeCloneFile
 * @uses \SebastianBergmann\PHPCPD\CodeCloneMap
 */
final class EditDistanceTest extends TestCase
{
    public function testEditDistanceWithSuffixtree(): void
    {
        $argv      = [1 => '.', '--min-tokens', '60'];
        $arguments = (new ArgumentsBuilder)->build($argv);
        $config    = new StrategyConfiguration($arguments);
        $strategy  = new SuffixTreeStrategy($config);

        $clones = (new Detector($strategy))->copyPasteDetection(
            [
                __DIR__ . '/../fixture/editdistance1.php',
                __DIR__ . '/../fixture/editdistance2.php',
            ],
        );

        $clones = $clones->clones();
        $this->assertCount(1, $clones);
    }

    public function testEditDistanceWithRabinkarp(): void
    {
        $argv      = [1 => '.', '--min-tokens', '60'];
        $arguments = (new ArgumentsBuilder)->build($argv);
        $config    = new StrategyConfiguration($arguments);
        $strategy  = new DefaultStrategy($config);

        $clones = (new Detector($strategy))->copyPasteDetection(
            [
                __DIR__ . '/../fixture/editdistance1.php',
                __DIR__ . '/../fixture/editdistance2.php',
            ],
        );

        $clones = $clones->clones();
        $this->assertCount(0, $clones);
    }
}
