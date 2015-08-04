### 1.2.0 (2015-08-?)

* #9 Added a tooltip to display all closest descendants
* #8 Fixed failure for cases when a subpage contains an extra slash
* #7 Fixed breadcrumb trail for when a subject is a redirect

### 1.1.0 (2015-06-02)

* Fixed unstyled content flashing observed in MW 1.25
* Added `$GLOBALS['egSBLBreadcrumbDividerStyleClass']` to assign styling options such as `sbl-breadcrumb-pipe` or `sbl-breadcrumb-arrow`
* Localisation updates from https://translatewiki.net

### 1.0.0 (2015-02-14)

* Initial release
* `ByPropertyHierarchicalLinksFinder` to match a property hierarchy defined by `egSBLPropertySearchPatternByNamespace`
* `BySubpageLinksFinder` to find a subpage hierarchy if `egSBLUseSubpageFinderFallback` is enabled
