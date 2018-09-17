# Changes in PHPCPD

All notable changes in PHPCPD are documented in this file using the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [4.1.0] - 2018-09-17

### Added

* Implemented [#117](https://github.com/sebastianbergmann/phpcpd/issues/117): Report average and maximum length of code clone

### Changed

* The text logger now prints code clones sorted by size (in descending order)

## [4.0.0] - 2018-01-02

### Removed

* PHPCPD is no longer supported on PHP 5.6 and PHP 7.0

## [3.0.1] - 2017-11-16

### Fixed

* Fixed [#147](https://github.com/sebastianbergmann/phpcpd/issues/147): Wrong exit code when no files were found to be scanned
* Fixed [#152](https://github.com/sebastianbergmann/phpcpd/issues/152): Version requirement for `sebastian/version` is too strict

## [3.0.0] - 2017-02-05

### Added

* Merged [#90](https://github.com/sebastianbergmann/phpcpd/pull/90): The PMD logger now replaces all characters that are invalid XML with `U+FFFD`
* Merged [#100](https://github.com/sebastianbergmann/phpcpd/pull/100): Added the `--regexps-exclude` option

### Changed

* When the Xdebug extension is loaded, PHPCPD disables as much of Xdebug's functionality as possible to minimize the performance impact

### Removed

* PHPCPD is no longer supported on PHP 5.3, PHP 5.4, and PHP 5.5

[4.1.0]: https://github.com/sebastianbergmann/phpcpd/compare/4.0.0...4.1.0
[4.0.0]: https://github.com/sebastianbergmann/phpcpd/compare/3.0.1...4.0.0
[3.0.1]: https://github.com/sebastianbergmann/phpcpd/compare/3.0.0...3.0.1
[3.0.0]: https://github.com/sebastianbergmann/phpcpd/compare/2.0...3.0.0

