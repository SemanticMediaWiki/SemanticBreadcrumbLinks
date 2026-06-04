<?php

namespace SBL;

use SMW\Services\ServicesFactory as ApplicationFactory;

/**
 * @license GPL-2.0-or-later
 * @since 3.0.0
 *
 * @author mwjames
 */
class Setup {

	/**
	 * @since 3.0.0
	 */
	public static function onExtensionFunction() {
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

}
