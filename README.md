# Semantic Breadcrumb Links

[![Build Status](https://secure.travis-ci.org/SemanticMediaWiki/SemanticBreadcrumbLinks.svg?branch=master)](http://travis-ci.org/SemanticMediaWiki/SemanticBreadcrumbLinks)
[![Code Coverage](https://scrutinizer-ci.com/g/SemanticMediaWiki/SemanticBreadcrumbLinks/badges/coverage.png?s=f3501ede0bcc98824aa51501eb3647ecf71218c0)](https://scrutinizer-ci.com/g/SemanticMediaWiki/SemanticBreadcrumbLinks/)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/SemanticMediaWiki/SemanticBreadcrumbLinks/badges/quality-score.png?s=d9aac7e68e6554f95b0a89608cbc36985429d819)](https://scrutinizer-ci.com/g/SemanticMediaWiki/SemanticBreadcrumbLinks/)
[![Latest Stable Version](https://poser.pugx.org/mediawiki/semantic-breadcrumb-links/version.png)](https://packagist.org/packages/mediawiki/semantic-breadcrumb-links)
[![Packagist download count](https://poser.pugx.org/mediawiki/semantic-breadcrumb-links/d/total.png)](https://packagist.org/packages/mediawiki/semantic-breadcrumb-links)
[![Dependency Status](https://www.versioneye.com/php/mediawiki:semantic-breadcrumb-links/badge.png)](https://www.versioneye.com/php/mediawiki:semantic-breadcrumb-links)

Semantic Breadcrumb Links (a.k.a. SBL) is a [Semantic Mediawiki][smw] extension
to aid in-page navigation by building breadcrumb links from an attributive property
filter.

SBL uses a pattern match strategy to filter property usage (e.g. `Has parent page`)
that ascribe the location of a page relative to its parent and provides navigational help by
generating a breadcrumb trail.

A short [video](https://vimeo.com/129347298) demonstrates "How SBL works" or can be used
to ease context navigation.

## Requirements

- PHP 5.3.2 or later
- MediaWiki 1.23 or later
- [Semantic MediaWiki][smw] 2.1 or later

## Installation

The recommended way to install Semantic Breadcrumb Links is by using [Composer][composer] with an entry in MediaWiki's `composer.json`.

```json
{
	"require": {
		"mediawiki/semantic-breadcrumb-links": "~1.1"
	}
}
```
1. From your MediaWiki installation directory, execute
   `composer require mediawiki/semantic-breadcrumb-links:~1.1`
2. Navigate to _Special:Version_ on your wiki and verify that the package
   have been successfully installed.

## Usage

If a property relation can be matched, SBL will generate a breadcrumb trail including its
relational directionality where `>` identifies a `Has parent` (or `Is child of`) relationship
while `<` indicates the closest descendant for a `Is parent of` affinity.

It is required to specify a property search pattern (by default `Has parent page` is assigned
as special property to `NS_MAIN`) in order to find relationships between a subject and its
antecedents.

### Example

`Foo` `>` `Bar` -- `[[Has parent page::Foo]]` `>` `Baz` -- `[[Has parent page::Bar]]`

If a subject `Baz` declares a relationship with `Bar` and `Bar` itself specifies
a parental relationship with `Foo` then the breadcrumb trail for `Baz` will be resolved as
`Foo > Bar`. On the other hand, the subject `Bar` will display a `Foo > Bar < Baz` trail
indicating that `Foo` is a `parent`( `>` ) and `Baz` is a `child` ( `<` ) of `Bar`.

### Configuration

Additional customizing and setting details can be found in the [configuration](https://github.com/SemanticMediaWiki/SemanticBreadcrumbLinks/blob/master/CONFIGURATION.md) section.

## Contribution and support

If you want to contribute work to the project please subscribe to the developers mailing list and
have a look at the contribution guideline.

* [File an issue](https://github.com/SemanticMediaWiki/SemanticBreadcrumbLinks/issues)
* [Submit a pull request](https://github.com/SemanticMediaWiki/SemanticBreadcrumbLinks/pulls)
* Ask a question on [the mailing list](https://semantic-mediawiki.org/wiki/Mailing_list)
* Ask a question on the #semantic-mediawiki IRC channel on Freenode.

### Tests

This extension provides unit and integration tests that are run by a [continues integration platform][travis]
but can also be executed using `composer phpunit` from the extension base directory.

## License

[GNU General Public License, version 2 or later][gpl-licence].

[smw]: https://github.com/SemanticMediaWiki/SemanticMediaWiki
[contributors]: https://github.com/SemanticMediaWiki/SemanticBreadcrumbLinks/graphs/contributors
[travis]: https://travis-ci.org/SemanticMediaWiki/SemanticBreadcrumbLinks
[gpl-licence]: https://www.gnu.org/copyleft/gpl.html
[composer]: https://getcomposer.org/
