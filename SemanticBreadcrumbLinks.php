<?php

use SBL\HookRegistry;
use SBL\PropertyRegistry;

use SMW\ApplicationFactory;

/**
 * This documentation group collects source code files belonging to Semantic
 * Breadcrumb Links.
 *
 * @see https://github.com/SemanticMediaWiki/SemanticBreadcrumbLinks/
 *
 * @defgroup SBL Semantic SemanticBreadcrumb Links
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
		'author'         => array( 'mwjames' ),
		'url'            => 'https://github.com/SemanticMediaWiki/SemanticBreadcrumbLinks/',
		'descriptionmsg' => 'sbl-desc',
		'version'        => SBL_VERSION,
		'license-name'   => 'GPL-2.0+',
	);

	// Register message files
	$GLOBALS['wgMessagesDirs'][ 'semanticbreadcrumblinks' ] = __DIR__ . '/i18n';

	// Register resource files
	$GLOBALS['wgResourceModules']['ext.semanticbreadcrumblinks'] = array(
		'styles' => 'resources/sbl.styles.css',
		'localBasePath' => __DIR__ ,
		'remoteExtPath' => end( ( explode( DIRECTORY_SEPARATOR . 'extensions' . DIRECTORY_SEPARATOR , __DIR__, 2 ) ) ),
		'position' => 'top'
	);

	// Register default settings
	$GLOBALS['egSBLBreadcrumbTrailStyleClass'] = 'sbl-breadcrumb-trail-light';
	$GLOBALS['egSBLMaxAntecedentHierarchyMatchDepth'] = 3;

	$GLOBALS['egSBLTryToFindClosestDescendant'] = true;
	$GLOBALS['egSBLUseSubpageDiscoveryForFallback'] = true;

	// Finalize registration process
	$GLOBALS['wgExtensionFunctions'][] = function() {

		$GLOBALS['egSBLPropertySearchPatternByNamespace'] = array(
			NS_CATEGORY     => array( '_SUBC' ),
			SMW_NS_PROPERTY => array( '_SUBP' ),
			NS_MAIN         => array( PropertyRegistry::SBL_PARENTPAGE ),
			NS_HELP         => array( PropertyRegistry::SBL_PARENTPAGE )
		);

		$configuration = array(
			'breadcrumbTrailStyleClass'  => $GLOBALS['egSBLBreadcrumbTrailStyleClass'],
			'tryToFindClosestDescendant' => $GLOBALS['egSBLTryToFindClosestDescendant'],
			'propertySearchPatternByNamespace'    => $GLOBALS['egSBLPropertySearchPatternByNamespace'],
			'maxAntecedentHierarchyMatchDepth' => $GLOBALS['egSBLMaxAntecedentHierarchyMatchDepth'],
			'useSubpageDiscoveryForFallback'      => $GLOBALS['egSBLUseSubpageDiscoveryForFallback']
		);

		$hookRegistry = new HookRegistry(
			ApplicationFactory::getInstance()->getStore(),
			$configuration
		);

		$hookRegistry->register( $GLOBALS['wgHooks'] );
	};

} );
