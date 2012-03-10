phpcpd
======

**phpcpd** is a Copy/Paste Detector (CPD) for PHP code.

Installation
------------

`phpcpd` should be installed using the PEAR Installer, the backbone of the [PHP Extension and Application Repository](http://pear.php.net/) that provides a distribution system for PHP packages.

Depending on your OS distribution and/or your PHP environment, you may need to install PEAR or update your existing PEAR installation before you can proceed with the following instructions. `sudo pear upgrade PEAR` usually suffices to upgrade an existing PEAR installation. The [PEAR Manual ](http://pear.php.net/manual/en/installation.getting.php) explains how to perform a fresh installation of PEAR.

The following two commands (which you may have to run as `root`) are all that is required to install `phpcpd` using the PEAR Installer:

    pear config-set auto_discover 1
    pear install pear.phpunit.de/phpcpd

After the installation you can find the `phpcpd` source files inside your local PEAR directory; the path is usually `/usr/lib/php/SebastianBergmann/PHPCPD`.

Usage Example
-------------

    ➜ ~ phpcpd /usr/local/src/phpunit/PHPUnit
    phpcpd 1.4.0 by Sebastian Bergmann.

    Found 3 exact clones with 53 duplicated lines in 5 files:

      - /usr/local/src/phpunit/PHPUnit/Framework/Constraint/Or.php:136-157
        /usr/local/src/phpunit/PHPUnit/Framework/Constraint/And.php:143-164

      - /usr/local/src/phpunit/PHPUnit/Framework/Constraint/Or.php:136-157
        /usr/local/src/phpunit/PHPUnit/Framework/Constraint/Xor.php:141-162

      - /usr/local/src/phpunit/PHPUnit/Framework/Comparator/Scalar.php:121-132
        /usr/local/src/phpunit/PHPUnit/Framework/Comparator/Numeric.php:102-113

    0.19% duplicated lines out of 27640 total lines of code.

    Time: 0 seconds, Memory: 18.25Mb
