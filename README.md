[![Latest Stable Version](https://poser.pugx.org/sebastian/phpcpd/v/stable.png)](https://packagist.org/packages/sebastian/phpcpd)
[![Build Status](https://travis-ci.org/sebastianbergmann/phpcpd.png?branch=master)](https://travis-ci.org/sebastianbergmann/phpcpd)

# PHP Copy/Paste Detector (PHPCPD)

`phpcpd` is a Copy/Paste Detector (CPD) for PHP code.

## Installation

### PHP Archive (PHAR)

The easiest way to obtain PHPCPD is to download a [PHP Archive (PHAR)](http://php.net/phar) that has all required dependencies of PHPCPD bundled in a single file:

    wget https://phar.phpunit.de/phpcpd.phar
    chmod +x phpcpd.phar
    mv phpcpd.phar /usr/local/bin/phpcpd

You can also immediately use the PHAR after you have downloaded it, of course:

    wget https://phar.phpunit.de/phpcpd.phar
    php phpcpd.phar

### Composer

Simply add a dependency on `sebastian/phpcpd` to your project's `composer.json` file if you use [Composer](http://getcomposer.org/) to manage the dependencies of your project. Here is a minimal example of a `composer.json` file that just defines a development-time dependency on PHPCPD:

    {
        "require-dev": {
            "sebastian/phpcpd": "*"
        }
    }

For a system-wide installation via Composer, you can run:

    composer global require 'sebastian/phpcpd=*'

Make sure you have `~/.composer/vendor/bin/` in your path.

### PEAR Installer

The following two commands (which you may have to run as `root`) are all that is required to install PHPCPD using the PEAR Installer:

    pear config-set auto_discover 1
    pear install pear.phpunit.de/phpcpd

## Usage Example

    ➜ ~ phpcpd /usr/local/src/phpunit/PHPUnit
    phpcpd 1.4.1 by Sebastian Bergmann.

    Found 3 exact clones with 53 duplicated lines in 5 files:

      - /usr/local/src/phpunit/PHPUnit/Framework/Constraint/Or.php:136-157
        /usr/local/src/phpunit/PHPUnit/Framework/Constraint/And.php:143-164

      - /usr/local/src/phpunit/PHPUnit/Framework/Constraint/Or.php:136-157
        /usr/local/src/phpunit/PHPUnit/Framework/Constraint/Xor.php:141-162

      - /usr/local/src/phpunit/PHPUnit/Framework/Comparator/Scalar.php:121-132
        /usr/local/src/phpunit/PHPUnit/Framework/Comparator/Numeric.php:102-113

    0.19% duplicated lines out of 27640 total lines of code.

    Time: 0 seconds, Memory: 18.25Mb
