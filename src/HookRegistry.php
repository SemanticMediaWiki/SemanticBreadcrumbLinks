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
	 * @var PropertyRegistry
	 */
	private $propertyRegistry;

	/**
	 * @since 1.0
	 *
	 * @param Store $store
	 * @param array $configuration
	 * @param PropertyRegistry $propertyRegistry
	 */
	public function __construct( Store $store, array $configuration, PropertyRegistry $propertyRegistry ) {
		$this->store = $store;
		$this->configuration = $configuration;
		$this->propertyRegistry = $propertyRegistry;
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
		$propertyRegistry = $this->propertyRegistry;

		/**
		 * @see https://github.com/SemanticMediaWiki/SemanticMediaWiki/blob/master/docs/technical/hooks.md
		 */
		$wgHooks['smwInitProperties'][] = function () use ( $propertyRegistry ) {
			return $propertyRegistry->registerBreadcrumbProperties();
		};

		/**
		 * @see https://www.mediawiki.org/wiki/Manual:Hooks/SkinTemplateOutputPageBeforeExec
		 */
		$wgHooks['SkinTemplateOutputPageBeforeExec'][] = function ( &$skin, &$template ) use( $store, $configuration ) {

			$subpageLinksFinder = new SubpageLinksFinder();
			$subpageLinksFinder->setSubpageDiscoverySupportState( $configuration['useSubpageDiscoveryForFallback'] );

			$hierarchicalLinksFinderByProperty = new HierarchicalLinksFinderByProperty( $store );
			$hierarchicalLinksFinderByProperty->setFindClosestDescendantState( $configuration['tryToFindClosestDescendant'] );
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
