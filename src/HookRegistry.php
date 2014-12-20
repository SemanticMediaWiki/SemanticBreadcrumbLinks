<?php

namespace SBL;

use SMW\Store;

use DummyLinker;

/**
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class HookRegistry {

	/**
	 * @var Store
	 */
	private $store;

	/**
	 * @var array
	 */
	private $configuration;

	/**
	 * @since 1.0
	 *
	 * @param Store $store
	 * @param array $configuration
	 */
	public function __construct( Store $store, array $configuration ) {
		$this->store = $store;
		$this->configuration = $configuration;
	}

	/**
	 * @since  1.0
	 *
	 * @param array &$wgHooks
	 */
	public function register( &$wgHooks ) {

		// PHP 5.3
		$store = $this->store;
		$configuration = $this->configuration;

		/**
		 * @see https://github.com/SemanticMediaWiki/SemanticMediaWiki/blob/master/docs/technical/hooks.md
		 */
		$wgHooks['smwInitProperties'][] = function () {
			return PropertyRegistry::register();
		};

		/**
		 * @see https://www.mediawiki.org/wiki/Manual:Hooks/SkinTemplateOutputPageBeforeExec
		 */
		$wgHooks['SkinTemplateOutputPageBeforeExec'][] = function ( &$skin, &$template ) use( $store, $configuration ) {

			$subpageLinksFinder = new SubpageLinksFinder();
			$subpageLinksFinder->setSubpageDiscoverySupportState( $configuration['useSubpageDiscoveryForFallback'] );

			$hierarchicalLinksFinderByProperty = new HierarchicalLinksFinderByProperty( $store );
			$hierarchicalLinksFinderByProperty->tryToFindClosestDescendant( $configuration['tryToFindClosestDescendant'] );
			$hierarchicalLinksFinderByProperty->setPropertySearchPatternByNamespace( $configuration['propertySearchPatternByNamespace'] );

			$htmlBreadcrumbLinksBuilder = new HtmlBreadcrumbLinksBuilder(
				$hierarchicalLinksFinderByProperty,
				$subpageLinksFinder
			);

			$htmlBreadcrumbLinksBuilder->setLinker( new DummyLinker() );
			$htmlBreadcrumbLinksBuilder->setBreadcrumbTrailStyleClass( $configuration['breadcrumbTrailStyleClass'] );

			$skinTemplateOutputModifier = new SkinTemplateOutputModifier( $htmlBreadcrumbLinksBuilder );
			$skinTemplateOutputModifier->modifyTemplate( $template );
			$skinTemplateOutputModifier->modifyOutput( $skin->getOutput() );

			return true;
		};

		/**
		 * @see https://www.mediawiki.org/wiki/Manual:Hooks/BeforePageDisplay
		 */
		$wgHooks['BeforePageDisplay'][] = function ( &$output, &$skin ) {
			$output->addModules( 'ext.semanticbreadcrumblinks' );

			return true;
		};
	}

}
