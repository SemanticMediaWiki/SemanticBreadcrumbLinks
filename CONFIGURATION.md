## Configuration

`$GLOBALS['egSBLBreadcrumbTrailStyleClass']` is used to assign a styling class to the breadcrumb trail.

`$GLOBALS['egSBLTryToFindClosestDescendant']` if enabled SBL will try to find the closest descendant
(it will not work for subpages due to missing annotation information).

`$GLOBALS['egSBLUseSubpageDiscoveryForFallback']` if enabled SBL will try to match a hierarchy
based on the `Foo/Bar/Baz` subpage notation for when a property search returns empty results or a
namespace did not describe a property search strategy.

`$GLOBALS['egSBLPropertySearchPatternByNamespace']` supports an individual search pattern on
a per namespace basis. If no search pattern is declared for a namespace then the search is disabled and
unless `egSBLUseSubpageDiscoveryForFallback` is enabled, SBL will not try to use a subpage hierarchy
to build a breadcrumb trail.

It is also possible to use user-defined properties (need to be defined as page-type property) while the amount
of properties specified per namespace will be used as maximum depth for possible antecedents matches
(`parent > grandparent > great-grandparent` etc.) that can be found relative to the current subject.
If multiple parents or children are available for a subject then only one of each can be displayed
due to the nature of the breadcrumb trail.

## Default settings

```php
$GLOBALS['egSBLBreadcrumbTrailStyleClass'] = 'sbl-breadcrumb-trail-light';

$GLOBALS['egSBLTryToFindClosestDescendant'] = true;
$GLOBALS['egSBLUseSubpageDiscoveryForFallback'] = true;

$GLOBALS['egSBLPropertySearchPatternByNamespace'] = array(

	// Search for a three level sub-category hierarchy
	NS_CATEGORY => array( '_SUBC', '_SUBC', '_SUBC' ),

	// Search for a three level sub-property hierarchy
	SMW_NS_PROPERTY => array( '_SUBP', '_SUBP', '_SUBP' ),

	// Search for a three level antecedent hierarchy that contains a
	// `Has parent page` annotation which is to follow `parent > grandparent > great-grandparent`
	NS_MAIN  => array( SBL_PROP_PARENTPAGE, SBL_PROP_PARENTPAGE, SBL_PROP_PARENTPAGE ),
	NS_HELP  => array( SBL_PROP_PARENTPAGE, SBL_PROP_PARENTPAGE, SBL_PROP_PARENTPAGE )
);
```

### Other search strategies

```php

	// Find a relationship trough a `Has parent page` and if successful
	// use the input to find a related category (`_INST`)
	$GLOBALS['egSBLPropertySearchPatternByNamespace'][ NS_MAIN ] = array(
		SBL_PROP_PARENTPAGE,
		'_INST'
	);

	// Match the category of a page and then for all succeeding steps
	// try to match the associated sub-category to that category/sub-category
	$GLOBALS['egSBLPropertySearchPatternByNamespace'][ NS_MAIN ] = array(
		'_INST',
		'_SUBC',
		'_SUBC'
	);
```
