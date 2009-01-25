phpcpd
======

**phpcpd** is a Copy/Paste Detector (CPD) for PHP code.

The goal of **phpcpd** is not not to replace more sophisticated tools such as [phpcs](http://pear.php.net/PHP_CodeSniffer), [pdepend](http://pdepend.org/), or [phpmd](http://phpmd.org/), but rather to provide an alternative to them when you just need to get a quick overview of duplicated code in a project.

Requirements
------------

* The [tokenizer](http://www.php.net/tokenizer) extension is required.

Usage Example
-------------

    sb@ubuntu phpcpd % ./phpcpd.php /usr/local/src/phpunit/trunk
    phpcpd 1.0.0 by Sebastian Bergmann.

      PHPUnit/Extensions/Database/DataSet/AbstractTable.php:156-190
      PHPUnit/Extensions/Database/DataSet/ReplacementTable.php:172-206

      PHPUnit/Samples/BankAccountDB/BankAccountDBTest.php:84-128
      PHPUnit/Samples/BankAccountDB/BankAccountDBTestMySQL.php:84-128

      PHPUnit/Tests/Extensions/Database/DataSet/XmlDataSetsTest.php:71-97
      PHPUnit/Tests/Extensions/Database/DataSet/CsvDataSetTest.php:70-96

    Found 104 duplicate lines of code in 6 files.

