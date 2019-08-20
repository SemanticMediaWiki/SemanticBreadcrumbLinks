# Semantic Breadcrumb Links

[![Build Status](https://secure.travis-ci.org/SemanticMediaWiki/SemanticBreadcrumbLinks.svg?branch=master)](http://travis-ci.org/SemanticMediaWiki/SemanticBreadcrumbLinks)
[![Code Coverage](https://scrutinizer-ci.com/g/SemanticMediaWiki/SemanticBreadcrumbLinks/badges/coverage.png?s=f3501ede0bcc98824aa51501eb3647ecf71218c0)](https://scrutinizer-ci.com/g/SemanticMediaWiki/SemanticBreadcrumbLinks/)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/SemanticMediaWiki/SemanticBreadcrumbLinks/badges/quality-score.png?s=d9aac7e68e6554f95b0a89608cbc36985429d819)](https://scrutinizer-ci.com/g/SemanticMediaWiki/SemanticBreadcrumbLinks/)
[![Latest Stable Version](https://poser.pugx.org/mediawiki/semantic-breadcrumb-links/version.png)](https://packagist.org/packages/mediawiki/semantic-breadcrumb-links)
[![Packagist download count](https://poser.pugx.org/mediawiki/semantic-breadcrumb-links/d/total.png)](https://packagist.org/packages/mediawiki/semantic-breadcrumb-links)

Semantic Breadcrumb Links (a.k.a. SBL) is a [Semantic Mediawiki][smw] extension
to aid in-page navigation by building breadcrumb links from an attributive property
filter.

SBL uses a pattern match strategy to filter property usage (e.g. `Has parent page`)
that ascribe the location of a page relative to its parent and provides navigational help by
generating a breadcrumb trail.

This [video](https://vimeo.com/129347298) demonstrates the functionality of the Semantic Breadcrumb Links extension.

## Requirements

- PHP 5.6 or later
- MediaWiki 1.27 or later
- [Semantic MediaWiki][smw] 2.5 or later

## Installation

The recommended way to install Semantic Breadcrumb Links is using [Composer](http://getcomposer.org) with
[MediaWiki's built-in support for Composer](https://www.mediawiki.org/wiki/Composer).

Note that the required extension Semantic MediaWiki must be installed first according to the installation
instructions provided.

### Step 1

Change to the base directory of your MediaWiki installation. If you do not have a "composer.local.json" file yet,
create one and add the following content to it:

```
{
	"require": {
		"mediawiki/semantic-breadcrumb-links": "~2.0"
	}
}
```

If you already have a "composer.local.json" file add the following line to the end of the "require"
section in your file:

    "mediawiki/semantic-breadcrumb-links": "~2.0"

Remember to add a comma to the end of the preceding line in this section.

### Step 2

Run the following command in your shell:

    php composer.phar update --no-dev

Note if you have Git installed on your system add the `--prefer-source` flag to the above command.

### Step 3

Add the following line to the end of your "LocalSettings.php" file:

    wfLoadExtension( 'SemanticBreadcrumbLinks' );

## Usage

![image](https://cloud.githubusercontent.com/assets/1245473/16253761/85daa7b2-3839-11e6-833e-6ec2bc15756b.png)

This [document](docs/README.md) decribes available settings and features of this extension.

## Contribution and support

If you want to contribute work to the project please subscribe to the developers mailing list and have a look
at the contribution guideline.

* [File an issue](https://github.com/SemanticMediaWiki/SemanticBreadcrumbLinks/issues)
* [Submit a pull request](https://github.com/SemanticMediaWiki/SemanticBreadcrumbLinks/pulls)
* Ask a question on [the mailing list](https://www.semantic-mediawiki.org/wiki/Mailing_list)

## Tests

This extension provides unit and integration tests that are run by a [continues integration platform][travis]
but can also be executed using `composer phpunit` from the extension base directory.

## License

[GNU General Public License, version 2 or later][gpl-licence].

[smw]: https://github.com/SemanticMediaWiki/SemanticMediaWiki
[contributors]: https://github.com/SemanticMediaWiki/SemanticBreadcrumbLinks/graphs/contributors
[travis]: https://travis-ci.org/SemanticMediaWiki/SemanticBreadcrumbLinks
[gpl-licence]: https://www.gnu.org/copyleft/gpl.html
[composer]: https://getcomposer.org/
