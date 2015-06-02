## Configuration

- `$GLOBALS['egSBLTryToFindClosestDescendant']` if enabled SBL will try to find the closest descendant
(it will not work for subpages due to missing annotation information).

- `$GLOBALS['egSBLPropertySearchPatternByNamespace']` supports an individual search pattern on
a per namespace basis. If no search pattern is declared for a namespace then the search is disabled.
It is also possible to use user-defined properties (need to be defined as page-type property) while
the amountof properties specified per namespace will be used as maximum depth for possible antecedents
matches (`parent > grandparent > great-grandparent` etc.) that can be found relative to the current
subject. If multiple parents or children are available for a subject then only one of each can be
displayed due to the nature of the breadcrumb trail.

### Styling

The output can easily be adjusted using the deployed styles (in `res/sbl.styles.css`) together with
the available configuration settings.

- `$GLOBALS['egSBLBreadcrumbTrailStyleClass']` is used to assign a styling class to the breadcrumb trail.
- `$GLOBALS['egSBLBreadcrumbDividerStyleClass']` is used to assign a divider styling class.

### Subpage

- `$GLOBALS['egSBLUseSubpageFinderFallback']` if enabled SBL will try to match a hierarchy
based on the `Foo/Bar/Baz` subpage notation for when a property search returns empty results or a
namespace did not describe a property search strategy. If `egSBLUseSubpageFinderFallback` is not
enabled then SBL will not try to resolve a possible subpage hierarchy.
- `$GLOBALS['egSBLPageTitleToHideSubpageParent']` if enabled SBL will try to hide the parent part
of a subpage title from display when a corresponding namespace entry is found in the
[`wgNamespacesWithSubpages`][mw-nssubp] setting.

## Default settings

```php
$GLOBALS['egSBLBreadcrumbTrailStyleClass'] = 'sbl-breadcrumb-trail-light';
$GLOBALS['egSBLBreadcrumbDividerStyleClass'] = 'sbl-breadcrumb-arrow';

$GLOBALS['egSBLPageTitleToHideSubpageParent'] = true;

$GLOBALS['egSBLTryToFindClosestDescendant'] = true;
$GLOBALS['egSBLUseSubpageFinderFallback'] = true;

$GLOBALS['egSBLPropertySearchPatternByNamespace'] = array(

	// Search for a three level sub-category hierarchy
	NS_CATEGORY => array(
		'_SUBC',
		'_SUBC',
		'_SUBC' ),

	// Search for a three level sub-property hierarchy
	SMW_NS_PROPERTY => array(
		'_SUBP',
		'_SUBP',
		'_SUBP' ),

	// Search for a three level antecedent hierarchy that contains a `Has parent page`
	// annotation to follow a `parent > grandparent > great-grandparent` schema
	NS_MAIN => array(
		SBL_PROP_PARENTPAGE,
		SBL_PROP_PARENTPAGE,
		SBL_PROP_PARENTPAGE ),
	NS_HELP => array(
		SBL_PROP_PARENTPAGE,
		SBL_PROP_PARENTPAGE,
		SBL_PROP_PARENTPAGE )
);
```

### Other search strategies

```php

	// Find a relationship on the first iteration using a `Has parent page` and
	// if successful use this input to find a related category (`_INST`) in the
	// following iteration
	$GLOBALS['egSBLPropertySearchPatternByNamespace'][ NS_MAIN ] = array(
		SBL_PROP_PARENTPAGE,
		'_INST'
	);

	// Match the category of a page and then for all succeeding steps
	// try to match an associated sub-category to that category/sub-category
	$GLOBALS['egSBLPropertySearchPatternByNamespace'][ NS_MAIN ] = array(
		'_INST',
		'_SUBC',
		'_SUBC'
	);
```

[mw-nssubp]: https://www.mediawiki.org/wiki/Manual:$wgNamespacesWithSubpages
