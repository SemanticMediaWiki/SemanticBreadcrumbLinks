<?php

use SBL\HookRegistry;
use SBL\Options;
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

SemanticBreadcrumbLinks::initExtension();

$GLOBALS['wgExtensionFunctions'][] = function() {
	SemanticBreadcrumbLinks::onExtensionFunction();
};

/**
 * @codeCoverageIgnore
 */
class SemanticBreadcrumbLinks {

	/**
	 * @since 1.3
	 */
	public static function initExtension() {

		// Load DefaultSettings
		require_once __DIR__ . '/DefaultSettings.php';

		define( 'SBL_VERSION', '1.3.0-alpha' );

		define( 'SBL_PROP_PARENTPAGE', 'Has parent page' );

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
		$GLOBALS['wgMessagesDirs']['SemanticBreadcrumbLinks'] = __DIR__ . '/i18n';

		// Register resource files
		$GLOBALS['wgResourceModules']['ext.semanticbreadcrumblinks'] = array(
			'styles'  => 'res/sbl.styles.css',
			'scripts' => 'res/sbl.tooltip.js',
			'localBasePath' => __DIR__ ,
			'remoteExtPath' => 'SemanticBreadcrumbLinks',
			'position' => 'top',
			'group'    => 'ext.smw',
			'dependencies'  => array(
				'onoi.qtip'
			),
			'targets' => array(
				'mobile',
				'desktop'
			)
		);
	}

	/**
	 * @since 1.3
	 */
	public static function onExtensionFunction() {

		// Default values are defined at this point to ensure
		// NS contants are specified prior
		$defaultPropertySearchPatternByNamespace = array(
			NS_CATEGORY => array(
				'_SUBC',
				'_SUBC',
				'_SUBC'
			),
			SMW_NS_PROPERTY => array(
				'_SUBP',
				'_SUBP',
				'_SUBP'
			),
			NS_MAIN => array(
				SBL_PROP_PARENTPAGE,
				SBL_PROP_PARENTPAGE,
				SBL_PROP_PARENTPAGE
			),
			NS_HELP => array(
				SBL_PROP_PARENTPAGE,
				SBL_PROP_PARENTPAGE,
				SBL_PROP_PARENTPAGE
			)
		);

		$configuration = array(
			'hideSubpageParent' => $GLOBALS['egSBLPageTitleToHideSubpageParent'],
			'breadcrumbTrailStyleClass' => $GLOBALS['egSBLBreadcrumbTrailStyleClass'],
			'breadcrumbDividerStyleClass' => $GLOBALS['egSBLBreadcrumbDividerStyleClass'],
			'tryToFindClosestDescendant' => $GLOBALS['egSBLTryToFindClosestDescendant'],
			'useSubpageFinderFallback' => $GLOBALS['egSBLUseSubpageFinderFallback'],
			'enabledSubpageParentAnnotation' => $GLOBALS['egSBLEnabledSubpageParentAnnotation'],
			'wgNamespacesWithSubpages' => $GLOBALS['wgNamespacesWithSubpages'],
			'propertySearchPatternByNamespace' => $GLOBALS['egSBLPropertySearchPatternByNamespace'] + $defaultPropertySearchPatternByNamespace
		);

		$hookRegistry = new HookRegistry(
			ApplicationFactory::getInstance()->getStore(),
			new Options( $configuration )
		);

		$hookRegistry->register();
	}

	/**
	 * @since 1.3
	 *
	 * @return string|null
	 */
	public static function getVersion() {
		return SBL_VERSION;
	}

}
