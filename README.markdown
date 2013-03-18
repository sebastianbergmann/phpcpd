# PHP Copy/Paste Detector (PHPCPD)

`phpcpd` is a Copy/Paste Detector (CPD) for PHP code.

## Installation

There a two supported ways of installing PHPCPD.

You can use the [PEAR Installer](http://pear.php.net/manual/en/guide.users.commandline.cli.php) to download and install PHPCPD as well as its dependencies. You can also download a [PHP Archive (PHAR)](http://php.net/phar) of PHPCPD that has all required dependencies of PHPCPD bundled in a single file.

### PEAR Installer

The following two commands (which you may have to run as `root`) are all that is required to install PHPCPD using the PEAR Installer:

```sh
pear config-set auto_discover 1
pear install pear.phpunit.de/phpcpd
```

### Composer Installer

Installation can be run via [composer](https://getcomposer.org/):

```sh
php composer.phar require sebastian/phpcpd
```

### PHP Archive (PHAR)

    wget http://pear.phpunit.de/get/phpcpd.phar
    chmod +x phpcpd.phar

## Usage Example

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
