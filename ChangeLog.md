# Changes in PHPCPD

All notable changes in PHPCPD are documented in this file using the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [6.0.3] - 2020-12-07

### Changed

* Changed PHP version constraint in `composer.json` from `^7.3` to `>=7.3`

## [6.0.2] - 2020-08-18

### Fixed

* [#187](https://github.com/sebastianbergmann/phpcpd/issues/187): Exclude arguments are being handled as prefixes

## [6.0.1] - 2020-08-13

### Fixed

* The `--verbose` CLI option had no effect

## [6.0.0] - 2020-08-13

### Removed

* The `--names` CLI option has been removed; use the `--suffix` CLI option instead
* The `--names-exclude` CLI option has been removed; use the `--exclude` CLI option instead
* The `--regexps-exclude` CLI option has been removed
* The `--progress` CLI option has been removed

## [5.0.2] - 2020-02-22

### Changed

* Require `sebastian/version` version 3 and `phpunit/php-timer` version 3 to allow Composer-based installation alongside `phploc/phploc` version 6 and `phpunit/phpunit` version 9 

## [5.0.1] - 2020-02-20

### Fixed

* [#181](https://github.com/sebastianbergmann/phpcpd/issues/181): `--min-lines`, `--min-tokens`, and `--fuzzy` commandline options do not work

## [5.0.0] - 2020-02-20

### Removed

* Removed support for PHP versions older than PHP 7.3

## [4.1.0] - 2018-09-17

### Added

* Implemented [#117](https://github.com/sebastianbergmann/phpcpd/issues/117): Report average and maximum length of code clone

### Changed

* The text logger now prints code clones sorted by size (in descending order)

## [4.0.0] - 2018-01-02

### Removed

* Removed support for PHP versions older than PHP 7.1

## [3.0.1] - 2017-11-16

### Fixed

* [#147](https://github.com/sebastianbergmann/phpcpd/issues/147): Wrong exit code when no files were found to be scanned
* [#152](https://github.com/sebastianbergmann/phpcpd/issues/152): Version requirement for `sebastian/version` is too strict

## [3.0.0] - 2017-02-05

### Added

* [#90](https://github.com/sebastianbergmann/phpcpd/pull/90): The PMD logger now replaces all characters that are invalid XML with `U+FFFD`
* [#100](https://github.com/sebastianbergmann/phpcpd/pull/100): Added the `--regexps-exclude` option

### Changed

* When the Xdebug extension is loaded, PHPCPD disables as much of Xdebug's functionality as possible to minimize the performance impact

### Removed

* Removed support for PHP versions older than PHP 5.6

[6.0.3]: https://github.com/sebastianbergmann/phpcpd/compare/6.0.2...6.0.3
[6.0.2]: https://github.com/sebastianbergmann/phpcpd/compare/6.0.1...6.0.2
[6.0.1]: https://github.com/sebastianbergmann/phpcpd/compare/6.0.0...6.0.1
[6.0.0]: https://github.com/sebastianbergmann/phpcpd/compare/5.0.2...6.0.0
[5.0.2]: https://github.com/sebastianbergmann/phpcpd/compare/5.0.1...5.0.2
[5.0.1]: https://github.com/sebastianbergmann/phpcpd/compare/5.0.0...5.0.1
[5.0.0]: https://github.com/sebastianbergmann/phpcpd/compare/4.1.0...5.0.0
[4.1.0]: https://github.com/sebastianbergmann/phpcpd/compare/4.0.0...4.1.0
[4.0.0]: https://github.com/sebastianbergmann/phpcpd/compare/3.0.1...4.0.0
[3.0.1]: https://github.com/sebastianbergmann/phpcpd/compare/3.0.0...3.0.1
[3.0.0]: https://github.com/sebastianbergmann/phpcpd/compare/2.0...3.0.0

