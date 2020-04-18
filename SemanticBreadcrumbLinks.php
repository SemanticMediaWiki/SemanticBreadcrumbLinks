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
	die( 'This file is part of the Semantic Breadcrumb Links extension. It is not a valid entry point.' );
}

if ( defined( 'SBL_VERSION' ) ) {
	// Do not initialize more than once.
	return 1;
}

SemanticBreadcrumbLinks::load();

/**
 * @codeCoverageIgnore
 */
class SemanticBreadcrumbLinks {

	/**
	 * @since 1.3
	 */
	public static function load() {

		if ( !defined( 'MEDIAWIKI' ) ) {
			return;
		}

		if ( is_readable( __DIR__ . '/vendor/autoload.php' ) ) {
			include_once __DIR__ . '/vendor/autoload.php';
		}

		// #56 Ensure the constant is defined before `LocalSettings.php` is
		// loaded in order to make it available for use in `LocalSettings.php`
		define( 'SBL_PROP_PARENTPAGE', 'Has parent page' );

		// Load DefaultSettings
		require_once __DIR__ . '/DefaultSettings.php';
	}

	/**
	 * @since 1.3
	 */
	public static function initExtension( $credits = [] ) {

		// See https://phabricator.wikimedia.org/T151136
		define( 'SBL_VERSION', isset( $credits['version'] ) ? $credits['version'] : 'UNKNOWN' );

		// Register message files
		$GLOBALS['wgMessagesDirs']['SemanticBreadcrumbLinks'] = __DIR__ . '/i18n';
		$GLOBALS['wgExtensionMessagesFiles']['SemanticBreadcrumbLinksMagic'] = __DIR__ . '/i18n/SemanticBreadcrumbLinks.magic.php';

		// Register resource files
		$GLOBALS['wgResourceModules']['ext.semanticbreadcrumblinks.styles'] = [
			'styles'  => 'res/sbl.styles.css',
			'localBasePath' => __DIR__ ,
			'remoteExtPath' => 'SemanticBreadcrumbLinks',
			'position' => 'top',
			'group'    => 'ext.smw',
			'targets' => [
				'mobile',
				'desktop'
			]
		];

		$GLOBALS['wgResourceModules']['ext.semanticbreadcrumblinks'] = [
			'scripts' => 'res/sbl.tooltip.js',
			'localBasePath' => __DIR__ ,
			'remoteExtPath' => 'SemanticBreadcrumbLinks',
			'position' => 'top',
			'group'    => 'ext.smw',
			'dependencies'  => [
				'ext.semanticbreadcrumblinks.styles',
				'onoi.qtip'
			],
			'targets' => [
				'mobile',
				'desktop'
			]
		];
	}

	/**
	 * @since 1.3
	 */
	public static function onExtensionFunction() {

		if ( !defined( 'SMW_VERSION' ) ) {
			if ( PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg' ) {
				die( "\nThe 'Semantic Breadcrumb Links' extension requires 'Semantic MediaWiki' to be installed and enabled.\n" );
			} else {
				die( '<b>Error:</b> The <a href="https://github.com/SemanticMediaWiki/SemanticBreadcrumbLinks/">Semantic Breadcrumb Links</a> extension requires <a href="https://www.semantic-mediawiki.org/wiki/Semantic_MediaWiki">Semantic MediaWiki</a> to be installed and enabled.<br />' );
			}
		}

		// Default values are defined at this point to ensure
		// NS contants are specified prior
		$defaultPropertySearchPatternByNamespace = [
			NS_CATEGORY => [
				'_SUBC',
				'_SUBC',
				'_SUBC'
			],
			SMW_NS_PROPERTY => [
				'_SUBP',
				'_SUBP',
				'_SUBP'
			],
			NS_MAIN => [
				SBL_PROP_PARENTPAGE,
				SBL_PROP_PARENTPAGE,
				SBL_PROP_PARENTPAGE
			],
			NS_HELP => [
				SBL_PROP_PARENTPAGE,
				SBL_PROP_PARENTPAGE,
				SBL_PROP_PARENTPAGE
			]
		];

		// Cover legacy settings
		$deprecationNotices = [];

		if ( isset( $GLOBALS['egSBLBreadcrumbTrailStyleClass'] ) ) {
			$GLOBALS['sblgBreadcrumbTrailStyleClass'] = $GLOBALS['egSBLBreadcrumbTrailStyleClass'];
			$deprecationNotices['replacement']['egSBLBreadcrumbTrailStyleClass'] = 'sblgBreadcrumbTrailStyleClass';
		}

		if ( $deprecationNotices !== [] && !isset( $GLOBALS['smwgDeprecationNotices']['sbl'] ) ) {
			$GLOBALS['smwgDeprecationNotices']['sbl'] = [ 'replacement' => $deprecationNotices['replacement'] ];
		}

		$configuration = [
			'hideSubpageParent' => $GLOBALS['egSBLPageTitleToHideSubpageParent'],
			'breadcrumbTrailStyleClass' => $GLOBALS['sblgBreadcrumbTrailStyleClass'],
			'breadcrumbDividerStyleClass' => $GLOBALS['egSBLBreadcrumbDividerStyleClass'],
			'tryToFindClosestDescendant' => $GLOBALS['egSBLTryToFindClosestDescendant'],
			'useSubpageFinderFallback' => $GLOBALS['egSBLUseSubpageFinderFallback'],
			'enabledSubpageParentAnnotation' => $GLOBALS['egSBLEnabledSubpageParentAnnotation'],
			'disableTranslationSubpageAnnotation' => $GLOBALS['egSBLDisableTranslationSubpageAnnotation'],
			'wgNamespacesWithSubpages' => $GLOBALS['wgNamespacesWithSubpages'],
			'propertySearchPatternByNamespace' => $GLOBALS['egSBLPropertySearchPatternByNamespace'] + $defaultPropertySearchPatternByNamespace
		];

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
