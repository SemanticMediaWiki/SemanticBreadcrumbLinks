# Semantic Breadcrumb Links

[![CI](https://github.com/SemanticMediaWiki/SemanticBreadcrumbLinks/actions/workflows/ci.yml/badge.svg)](https://github.com/SemanticMediaWiki/SemanticBreadcrumbLinks/actions/workflows/ci.yml)
![Latest Stable Version](https://img.shields.io/packagist/v/mediawiki/semantic-breadcrumb-links.svg)
![Total Download Count](https://img.shields.io/packagist/dt/mediawiki/semantic-breadcrumb-links.svg)
[![codecov](https://codecov.io/gh/SemanticMediaWiki/SemanticBreadcrumbLinks/graph/badge.svg?token=yl1GVLwRwo)](https://codecov.io/gh/SemanticMediaWiki/SemanticBreadcrumbLinks)

Semantic Breadcrumb Links (a.k.a. SBL) is a [Semantic Mediawiki][smw] extension
to aid in-page navigation by building breadcrumb links from an attributive property
filter.

SBL uses a pattern match strategy to filter property usage (e.g. `Has parent page`)
that ascribe the location of a page relative to its parent and provides navigational help by
generating a breadcrumb trail.

This [video](https://vimeo.com/129347298) demonstrates the functionality of the Semantic Breadcrumb Links extension.

## Requirements

- PHP 8.1 or later
- MediaWiki 1.39 or later
- [Semantic MediaWiki][smw] 4.2 or later

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
