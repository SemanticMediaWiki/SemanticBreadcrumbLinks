<?php

use SBL\HookRegistry;
use SMW\ApplicationFactory;

/**
 * @see https://github.com/SemanticMediaWiki/SemanticBreadcrumbLinks/
 *
 * @defgroup SBL Semantic SemanticBreadcrumb Links
 * @codeCoverageIgnore
 */
call_user_func( function () {

	if ( !defined( 'MEDIAWIKI' ) ) {
		die( 'This file is part of the SemanticBreadcrumbLinks extension, it is not a valid entry point.' );
	}

	if ( version_compare( $GLOBALS[ 'wgVersion' ], '1.23', 'lt' ) ) {
		die( '<b>Error:</b> This version of <a href="https://github.com/SemanticMediaWiki/SemanticBreadcrumbLinks/">SemanticBreadcrumbLinks</a> is only compatible with MediaWiki 1.23 or above. You need to upgrade MediaWiki first.' );
	}

	define( 'SBL_VERSION', '1.0-alpha' );

	// Register the extension
	$GLOBALS[ 'wgExtensionCredits' ][ 'semantic' ][ ] = array(
		'path'           => __FILE__,
		'name'           => 'Semantic Breadcrumb Links',
		'author'         => array( 'James Hong Kong' ),
		'url'            => 'https://github.com/SemanticMediaWiki/SemanticBreadcrumbLinks/',
		'descriptionmsg' => 'sbl-desc',
		'version'        => SBL_VERSION,
		'license-name'   => 'GPL-2.0+',
	);

	// Register message files
	$GLOBALS['wgMessagesDirs'][ 'semanticbreadcrumblinks' ] = __DIR__ . '/i18n';

	// Register resource files
	$GLOBALS['wgResourceModules']['ext.semanticbreadcrumblinks'] = array(
		'styles' => 'res/sbl.styles.css',
		'localBasePath' => __DIR__ ,
		'remoteExtPath' => end( ( explode( DIRECTORY_SEPARATOR . 'extensions' . DIRECTORY_SEPARATOR , __DIR__, 2 ) ) ),
		'position' => 'top'
	);

	// Declare property Id constant
	define( 'SBL_PROP_PARENTPAGE', 'Has parent page' );

	// Register default settings
	$GLOBALS['egSBLBreadcrumbTrailStyleClass'] = 'sbl-breadcrumb-trail-light';
	$GLOBALS['egSBLPropertySearchPatternByNamespace'] = array();

	$GLOBALS['egSBLTryToFindClosestDescendant'] = true;
	$GLOBALS['egSBLUseSubpageDiscoveryForFallback'] = true;
	$GLOBALS['egSBLPageTitleToHideSubpageParent'] = true;

	// Finalize registration process
	$GLOBALS['wgExtensionFunctions'][] = function() {

		// Default values are defined at this point to ensure
		// NS contants are specified
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
			'breadcrumbTrailStyleClass'  => $GLOBALS['egSBLBreadcrumbTrailStyleClass'],
			'tryToFindClosestDescendant' => $GLOBALS['egSBLTryToFindClosestDescendant'],
			'propertySearchPatternByNamespace' => $GLOBALS['egSBLPropertySearchPatternByNamespace'] + $defaultPropertySearchPatternByNamespace,
			'useSubpageDiscoveryForFallback'   => $GLOBALS['egSBLUseSubpageDiscoveryForFallback'],
			'hideSubpageParent' => $GLOBALS['egSBLPageTitleToHideSubpageParent']
		);

		$hookRegistry = new HookRegistry(
			ApplicationFactory::getInstance()->getStore(),
			$configuration
		);

		$hookRegistry->register( $GLOBALS['wgHooks'] );
	};

} );
