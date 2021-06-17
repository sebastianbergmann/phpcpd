<?php

// Main entry for testing PHP version of ApproximateCloneDetectingSuffixTree

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

require_once "JavaObjectInterface.php";
require_once "PairList.php";
require_once "PhpToken.php";
require_once "Sentinel.php";
require_once "SuffixTreeHashTable.php";
require_once "SuffixTree.php";
require_once "CloneInfo.php";
require_once "ApproximateCloneDetectingSuffixTree.php";

$word = [];
$file = $argv[1];

if (is_file($file)) {
    $content = file_get_contents($file);
    $tokens = token_get_all($content);

    // Copied from phpcpd
    $tokensIgnoreList = [
        T_INLINE_HTML        => true,
        T_COMMENT            => true,
        T_DOC_COMMENT        => true,
        T_OPEN_TAG           => true,
        T_OPEN_TAG_WITH_ECHO => true,
        T_CLOSE_TAG          => true,
        T_WHITESPACE         => true,
        T_USE                => true,
        T_NS_SEPARATOR       => true,
    ];
    foreach($tokens as $token) {
        if (is_array($token)) {
            if (isset($tokensIgnoreList[$token[0]])) {
                continue;
            }
            $word[] = new PhpToken(
                $token[0],
                token_name($token[0]),
                $token[2],
                $file,
                $token[1]
            );
        } 
    }
} else {
    die('Only supports one file');
}
$word[] = new Sentinel();
$tree = new ApproximateCloneDetectingSuffixTree($word);
$tree->findClones(10, 5, 10);
