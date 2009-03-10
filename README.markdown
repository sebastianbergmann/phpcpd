phpcpd
======

**phpcpd** is a Copy/Paste Detector (CPD) for PHP code.

The goal of **phpcpd** is not not to replace more sophisticated tools such as [phpcs](http://pear.php.net/PHP_CodeSniffer), [pdepend](http://pdepend.org/), or [phpmd](http://phpmd.org/), but rather to provide an alternative to them when you just need to get a quick overview of duplicated code in a project.

Requirements
------------

* The [tokenizer](http://www.php.net/tokenizer) extension is required.

Installation
------------

phpcpd should be installed using the [PEAR Installer](http://pear.php.net/). This installer is the backbone of PEAR, which provides a distribution system for PHP packages, and is shipped with every release of PHP since version 4.3.0.

The PEAR channel (`pear.phpunit.de`) that is used to distribute phpcpd needs to be registered with the local PEAR environment:

    sb@ubuntu ~ % pear channel-discover pear.phpunit.de
    Adding Channel "pear.phpunit.de" succeeded
    Discovery of channel "pear.phpunit.de" succeeded

This has to be done only once. Now the PEAR Installer can be used to install packages from the PHPUnit channel:

    sb@ubuntu ~ % pear install phpunit/phpcpd
    downloading phpcpd-1.1.0.tgz ...
    Starting to download phpcpd-1.1.0.tgz (8,226 bytes)
    .....done: 8,226 bytes
    install ok: channel://pear.phpunit.de/phpcpd-1.1.0

After the installation you can find the phpcpd source files inside your local PEAR directory; the path is usually `/usr/lib/php/PHPCPD`.

Usage Example
-------------

    sb@ubuntu ~ % phpcpd /usr/local/src/phpunit/trunk 
    phpcpd 1.1.0 by Sebastian Bergmann.

    Found 5 exact clones with 133 duplicated lines in 8 files:

      - PHPUnit/Samples/BankAccountDB/BankAccountDBTestMySQL.php:84-128
        PHPUnit/Samples/BankAccountDB/BankAccountDBTest.php:84-128

      - PHPUnit/Extensions/Database/DataSet/AbstractTable.php:156-190
        PHPUnit/Extensions/Database/DataSet/ReplacementTable.php:172-206

      - PHPUnit/Tests/Extensions/Database/DataSet/CompositeDataSetTest.php:87-103
        PHPUnit/Tests/Extensions/Database/DataSet/CsvDataSetTest.php:80-96

      - PHPUnit/Tests/Extensions/Database/DataSet/CsvDataSetTest.php:70-82
        PHPUnit/Tests/Extensions/Database/DataSet/XmlDataSetsTest.php:71-83

      - PHPUnit/Tests/Extensions/Database/DataSet/CsvDataSetTest.php:70-97
        PHPUnit/Tests/Extensions/Database/DataSet/YamlDataSetTest.php:70-97

    0.22% duplicated lines out of 61556 total lines of code.

