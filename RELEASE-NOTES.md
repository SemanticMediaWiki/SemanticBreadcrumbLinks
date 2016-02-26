This file contains the RELEASE-NOTES of the Semantic Breadcrumb Links (a.k.a. SBL) extension.

### 1.3.0 (2015-03-10)

* Added `$GLOBALS['egSBLEnabledSubpageParentAnnotation']` to allow for subpage
  parents to be annotated automatically if no other `Has parent page` exists
* Recognize `egSBLPageTitleToHideSubpageParent` / `wgNamespacesWithSubpages`
  when building the breadcrumb title
* Apply italic styles for "hasChildren" early to avoid JS async display clutter

### 1.2.0 (2015-12-19)

* #9 Added a tooltip to display all closest descendants
* #8 Fixed failure for cases when a subpage contains an extra slash
* #7 Fixed breadcrumb trail for when a subject is a redirect
* Localization updates from https://translatewiki.net

### 1.1.0 (2015-06-02)

* Fixed unstyled content flashing observed in MW 1.25
* Added `$GLOBALS['egSBLBreadcrumbDividerStyleClass']` to assign styling options such as `sbl-breadcrumb-pipe` or `sbl-breadcrumb-arrow`
* Localization updates from https://translatewiki.net

### 1.0.0 (2015-02-14)

* Initial release
* `ByPropertyHierarchicalLinksFinder` to match a property hierarchy defined by `egSBLPropertySearchPatternByNamespace`
* `BySubpageLinksFinder` to find a subpage hierarchy if `egSBLUseSubpageFinderFallback` is enabled
