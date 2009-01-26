phpcpd
======

**phpcpd** is a Copy/Paste Detector (CPD) for PHP code.

The goal of **phpcpd** is not not to replace more sophisticated tools such as [phpcs](http://pear.php.net/PHP_CodeSniffer), [pdepend](http://pdepend.org/), or [phpmd](http://phpmd.org/), but rather to provide an alternative to them when you just need to get a quick overview of duplicated code in a project.

Requirements
------------

* The [tokenizer](http://www.php.net/tokenizer) extension is required.

Installation
------------

    sb@ubuntu ~ % pear install phpunit/phpcpd 
    downloading phpcpd-1.0.0.tgz ...
    Starting to download phpcpd-1.0.0.tgz (7,151 bytes)
    .....done: 7,151 bytes
    install ok: channel://pear.phpunit.de/phpcpd-1.0.0

Usage Example
-------------

    sb@ubuntu ~ % phpcpd /usr/local/src/phpunit/trunk 
    phpcpd 1.0.0 by Sebastian Bergmann.

    Found 3 exact clones with 104 duplicated lines in 6 files:

      - PHPUnit/Extensions/Database/DataSet/AbstractTable.php:156-190
        PHPUnit/Extensions/Database/DataSet/ReplacementTable.php:172-206

      - PHPUnit/Samples/BankAccountDB/BankAccountDBTest.php:84-128
        PHPUnit/Samples/BankAccountDB/BankAccountDBTestMySQL.php:84-128

      - PHPUnit/Tests/Extensions/Database/DataSet/XmlDataSetsTest.php:71-97
        PHPUnit/Tests/Extensions/Database/DataSet/CsvDataSetTest.php:70-96

