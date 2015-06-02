<?php

use SBL\HookRegistry;
use SMW\ApplicationFactory;

/**
 * @see https://github.com/SemanticMediaWiki/SemanticBreadcrumbLinks/
 *
 * @defgroup SBL Semantic Breadcrumb Links
 */
if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is part of the SemanticBreadcrumbLinks extension, it is not a valid entry point.' );
}

if ( version_compare( $GLOBALS[ 'wgVersion' ], '1.23', 'lt' ) ) {
	die( '<b>Error:</b> This version of <a href="https://github.com/SemanticMediaWiki/SemanticBreadcrumbLinks/">SemanticBreadcrumbLinks</a> is only compatible with MediaWiki 1.23 or above. You need to upgrade MediaWiki first.' );
}

if ( defined( 'SBL_VERSION' ) ) {
	// Do not initialize more than once.
	return 1;
}

define( 'SBL_VERSION', '1.1.0' );

/**
 * @codeCoverageIgnore
 */
call_user_func( function () {

	// Register the extension
	$GLOBALS['wgExtensionCredits']['semantic'][ ] = array(
		'path'           => __FILE__,
		'name'           => 'Semantic Breadcrumb Links',
		'author'         => array( 'James Hong Kong' ),
		'url'            => 'https://github.com/SemanticMediaWiki/SemanticBreadcrumbLinks/',
		'descriptionmsg' => 'sbl-desc',
		'version'        => SBL_VERSION,
		'license-name'   => 'GPL-2.0+',
	);

	// Register message files
	$GLOBALS['wgMessagesDirs']['semantic-breadcrumb-links'] = __DIR__ . '/i18n';

	// Register resource files
	$GLOBALS['wgResourceModules']['ext.semanticbreadcrumblinks'] = array(
		'styles' => 'res/sbl.styles.css',
		'localBasePath' => __DIR__ ,
		'remoteExtPath' => end( ( explode( DIRECTORY_SEPARATOR . 'extensions' . DIRECTORY_SEPARATOR , __DIR__, 2 ) ) ),
		'position' => 'top',
		'group'    => 'ext.smw'
	);

	// Declare property Id constant
	define( 'SBL_PROP_PARENTPAGE', 'Has parent page' );

	// Register default settings
	$GLOBALS['egSBLBreadcrumbTrailStyleClass'] = 'sbl-breadcrumb-trail-light';
	$GLOBALS['egSBLBreadcrumbDividerStyleClass'] = 'sbl-breadcrumb-arrow';
	$GLOBALS['egSBLPropertySearchPatternByNamespace'] = array();

	$GLOBALS['egSBLTryToFindClosestDescendant'] = true;
	$GLOBALS['egSBLUseSubpageFinderFallback'] = true;
	$GLOBALS['egSBLPageTitleToHideSubpageParent'] = true;

	// Finalize registration process
	$GLOBALS['wgExtensionFunctions'][] = function() {

		// Default values are defined at this point to ensure
		// NS contants are specified prior
		$defaultPropertySearchPatternByNamespace = array(
			NS_CATEGORY     => array(
				'_SUBC',
				'_SUBC',
				'_SUBC' ),
			SMW_NS_PROPERTY => array(
				'_SUBP',
				'_SUBP',
				'_SUBP' ),
			NS_MAIN         => array(
				SBL_PROP_PARENTPAGE,
				SBL_PROP_PARENTPAGE,
				SBL_PROP_PARENTPAGE ),
			NS_HELP         => array(
				SBL_PROP_PARENTPAGE,
				SBL_PROP_PARENTPAGE,
				SBL_PROP_PARENTPAGE )
		);

		$configuration = array(
			'hideSubpageParent' => $GLOBALS['egSBLPageTitleToHideSubpageParent'],
			'breadcrumbTrailStyleClass' => $GLOBALS['egSBLBreadcrumbTrailStyleClass'],
			'breadcrumbDividerStyleClass' => $GLOBALS['egSBLBreadcrumbDividerStyleClass'],
			'tryToFindClosestDescendant' => $GLOBALS['egSBLTryToFindClosestDescendant'],
			'useSubpageFinderFallback' => $GLOBALS['egSBLUseSubpageFinderFallback'],
			'wgNamespacesWithSubpages' => $GLOBALS['wgNamespacesWithSubpages'],
			'propertySearchPatternByNamespace' => $GLOBALS['egSBLPropertySearchPatternByNamespace'] + $defaultPropertySearchPatternByNamespace
		);

		$hookRegistry = new HookRegistry(
			ApplicationFactory::getInstance()->getStore(),
			$configuration
		);

		$hookRegistry->register();
	};

} );
