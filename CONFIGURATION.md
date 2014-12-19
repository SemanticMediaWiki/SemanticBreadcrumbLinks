## Configuration

`$GLOBALS['egSBLBreadcrumbTrailStyleClass']` is used to assign a styling class to the breadcrumb trail.

`$GLOBALS['egSBLMaxAntecedentHierarchyMatchDepth']` specifies the max depth for
possible antecedents matches (`parent > grandparent > great-grandparent` etc.) that can be found
relative to the selected page. If multiple parents or children are available for a subject
then only one of each can be displayed due to the nature of the breadcrumb trail.

`$GLOBALS['egSBLTryToFindClosestDescendant']` if enabled SBL will try to find the closest descendant
(it will not work for subpages due to missing annotation information).

`$GLOBALS['egSBLUseSubpageDiscoveryForFallback']` if enabled SBL will try to match a hierarchy
based on the `Foo/Bar/Baz` subpage notation for when a property search returns empty results or a
namespace did not describe a property search strategy (general fallback for those namespaces that to
not define a search property).

`$GLOBALS['egSBLPropertySearchPatternByNamespace']` supports an individual search pattern on
a per namespace basis. If no search pattern is declared for a namespace then the search is disabled.
It is possible to use user-defined properties (need to be defined as page-type property).

## Default settings

```php
$GLOBALS['egSBLBreadcrumbTrailStyleClass'] = 'sbl-breadcrumb-trail-light';
$GLOBALS['egSBLMaxAntecedentHierarchyMatchDepth'] = 3;

$GLOBALS['egSBLTryToFindClosestDescendant'] = true;
$GLOBALS['egSBLUseSubpageDiscoveryForFallback'] = true;

$GLOBALS['egSBLPropertySearchPatternByNamespace'] = array(
	NS_CATEGORY     => array( '_SUBC' ), // search for sub-categories
	SMW_NS_PROPERTY => array( '_SUBP' ), // search for sub-properties

	NS_MAIN         => array( PropertyRegistry::SBL_PARENTPAGE ),
	NS_HELP         => array( PropertyRegistry::SBL_PARENTPAGE )

	// A more elaborated example, SBL first tries to match the category
	// of a page and then for all succeeding steps tries to match a
	// sub-category to the category found earlier
	// NS_MAIN      => array( '_INST', '_SUBC' ),
);
```
