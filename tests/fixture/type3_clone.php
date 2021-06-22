<?php

/**
 * Purpose is to demonstrate detection of Type 3 clone, which contains small changes.
 *
 * ./phpcpd --min-tokens 1 --min-lines 5 tests/fixture/type3_clone.php
 * ./phpcpd --algorithm suffixtree --min-tokens 5 --min-lines 5 --head-equality 2 tests/fixture/type3_clone.php
 */

function foo()
{
    $a = 10;
    $b = 20;
    if ($a > $b) {
        return 'foo';
    } else {
        return 'bar';
    }
}

function bar()
{
    $a = 10;
    $b = 20;
    if ($a > $b) {
    } else {
        return 'bar';
    }
}

function bar()
{
    $a = 10;
    $b = '20';
    if ($a) {
        return 'foo';
    } else {
        return 'bar';
    }
}
