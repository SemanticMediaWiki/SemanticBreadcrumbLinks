<?php

/**
 * DO NOT EDIT!
 *
 * The following default settings are to be used by the extension itself,
 * please modify settings in the LocalSettings file.
 *
 * @codeCoverageIgnore
 */
if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is part of the SemanticBreadcrumbLinks extension, it is not a valid entry point.' );
}

/**
 * Assigns a styling class to the breadcrumb trail
 */
$GLOBALS['sblgBreadcrumbTrailStyleClass'] = 'sbl-breadcrumb-trail-light';

/**
 * Assigns a divider styling class
 */
$GLOBALS['egSBLBreadcrumbDividerStyleClass'] = 'sbl-breadcrumb-arrow';

/**
 * An array of search patterna on a per namespace basis. If no search pattern is
 * declared for a namespace then the search is disabled.
 */
$GLOBALS['egSBLPropertySearchPatternByNamespace'] = [];

/**
 * SBL will try to find the closest descendant (it will not work for subpages
 * due to missing annotation information)
 */
$GLOBALS['egSBLTryToFindClosestDescendant'] = true;

/**
 * SBL will try to match a hierarchy based on the Foo/Bar/Baz subpage notation
 * for when a property search returns empty results.
 */
$GLOBALS['egSBLUseSubpageFinderFallback'] = true;

/**
 * SBL will try to hide the parent part of a subpage title from display when a
 * corresponding namespace entry is found in the wgNamespacesWithSubpages
 * setting.
 */
$GLOBALS['egSBLPageTitleToHideSubpageParent'] = true;

/**
 * Supports the auto-generation of Has parent page annotations for subpages.
 */
$GLOBALS['egSBLEnabledSubpageParentAnnotation'] = true;

/**
 * Disables the annotation for a subpage when it is identified as translation page.
 */
$GLOBALS['egSBLDisableTranslationSubpageAnnotation'] = true;
