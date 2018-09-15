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
	die( 'This file is part of the Semantic Breadcrumb Links extension, it is not a valid entry point.' );
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

		if ( is_readable( __DIR__ . '/vendor/autoload.php' ) ) {
			include_once __DIR__ . '/vendor/autoload.php';
		}

		// Load DefaultSettings
		require_once __DIR__ . '/DefaultSettings.php';

		// In case extension.json is being used, the succeeding steps will
		// be handled by the ExtensionRegistry
		self::initExtension();

		$GLOBALS['wgExtensionFunctions'][] = function() {
			self::onExtensionFunction();
		};
	}

	/**
	 * @since 1.3
	 */
	public static function initExtension() {

		define( 'SBL_VERSION', '1.5.0-alpha' );
		define( 'SBL_PROP_PARENTPAGE', 'Has parent page' );

		// Register the extension
		$GLOBALS['wgExtensionCredits']['semantic'][ ] = [
			'path'           => __FILE__,
			'name'           => 'Semantic Breadcrumb Links',
			'author'         => [ 'James Hong Kong' ],
			'url'            => 'https://github.com/SemanticMediaWiki/SemanticBreadcrumbLinks/',
			'descriptionmsg' => 'sbl-desc',
			'version'        => SBL_VERSION,
			'license-name'   => 'GPL-2.0-or-later',
		];

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
	public static function checkRequirements() {

		if ( version_compare( $GLOBALS[ 'wgVersion' ], '1.27', 'lt' ) ) {
			die( '<b>Error:</b> This version of <a href="https://github.com/SemanticMediaWiki/SemanticBreadcrumbLinks/">Semantic Breadcrumb Links</a> is only compatible with MediaWiki 1.27 or above. You need to upgrade MediaWiki first.' );
		}

		if ( !defined( 'SMW_VERSION' ) ) {
			die( '<b>Error:</b> <a href="https://github.com/SemanticMediaWiki/SemanticBreadcrumbLinks/">Semantic Breadcrumb Links</a> requires <a href="https://github.com/SemanticMediaWiki/SemanticMediaWiki/">Semantic MediaWiki</a>. Please enable or install the extension first.' );
		}
	}

	/**
	 * @since 1.3
	 */
	public static function onExtensionFunction() {

		// Check requirements after LocalSetting.php has been processed
		self::checkRequirements();

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

		$configuration = [
			'hideSubpageParent' => $GLOBALS['egSBLPageTitleToHideSubpageParent'],
			'breadcrumbTrailStyleClass' => $GLOBALS['egSBLBreadcrumbTrailStyleClass'],
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
