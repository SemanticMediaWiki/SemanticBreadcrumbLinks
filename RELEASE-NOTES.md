This file contains the RELEASE-NOTES of the Semantic Breadcrumb Links (a.k.a. SBL) extension.

### 1.4.0

This is not a release yet.

### 1.3.1

Released on March 27, 2017

* #21 Fixed warnings caused by the Resource Loader
* #23 Fixed subpage detection
* Internal code changes
* Localization updates from https://translatewiki.net

### 1.3.0

Released on July 9, 2016.

* Requires SMW 2.4+
* Added `onoi/shared-resources` dependency
* Support for `Display title of` provided by SMW 2.4
* Added `$GLOBALS['egSBLEnabledSubpageParentAnnotation']` to allow for subpage
  parents to be annotated automatically if no other `Has parent page` exists
* Recognize `egSBLPageTitleToHideSubpageParent` / `wgNamespacesWithSubpages`
  when building the breadcrumb title
* Apply italic styles for "hasChildren" early to avoid JS async display clutter

### 1.2.0

Released on December 19, 2015.

* #9 Added a tooltip to display all closest descendants
* #8 Fixed failure for cases when a subpage contains an extra slash
* #7 Fixed breadcrumb trail for when a subject is a redirect
* Localization updates from https://translatewiki.net

### 1.1.0

Released on June 2, 2015.

* Fixed unstyled content flashing observed in MW 1.25
* Added `$GLOBALS['egSBLBreadcrumbDividerStyleClass']` to assign styling options such as `sbl-breadcrumb-pipe` or `sbl-breadcrumb-arrow`
* Localization updates from https://translatewiki.net

### 1.0.0

Released on Februray 14, 2015.

* Initial release
* `ByPropertyHierarchicalLinksFinder` to match a property hierarchy defined by `egSBLPropertySearchPatternByNamespace`
* `BySubpageLinksFinder` to find a subpage hierarchy if `egSBLUseSubpageFinderFallback` is enabled
