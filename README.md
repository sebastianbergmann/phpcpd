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

    composer global require "sebastian/phpcpd=*"

Make sure you have `~/.composer/vendor/bin/` in your path.

## Usage Example

    ➜ ~ phpcpd /tmp/wordpress-3.8.1/wp-includes
    phpcpd 2.0.1 by Sebastian Bergmann.

    Found 34 exact clones with 1273 duplicated lines in 11 files:

      - /tmp/wordpress-3.8.1/wp-includes/class-snoopy.php:165-195
        /tmp/wordpress-3.8.1/wp-includes/class-snoopy.php:225-255

      .
      .
      .

      - /tmp/wordpress-3.8.1/wp-includes/SimplePie/Misc.php:1769-1830
        /tmp/wordpress-3.8.1/wp-includes/SimplePie/Parse/Date.php:710-771

    0.86% duplicated lines out of 147877 total lines of code.

    Time: 24.67 seconds, Memory: 159.00Mb

