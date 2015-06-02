<?php

namespace SBL;

use SMW\Store;
use DummyLinker;
use Hooks;

/**
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class HookRegistry {

	/**
	 * @var array
	 */
	private $handlers = array();

	/**
	 * @since 1.0
	 *
	 * @param Store $store
	 * @param array $configuration
	 */
	public function __construct( Store $store, array $configuration ) {
		$this->addCallbackHandlers( $store, $configuration );
	}

	/**
	 * @since  1.1
	 *
	 * @param string $name
	 *
	 * @return boolean
	 */
	public function isRegistered( $name ) {
		return Hooks::isRegistered( $name );
	}

	/**
	 * @since  1.1
	 *
	 * @param string $name
	 *
	 * @return Callable|false
	 */
	public function getHandlersFor( $name ) {
		return isset( $this->handlers[$name] ) ? $this->handlers[$name] : false;
	}

	/**
	 * @since  1.0
	 */
	public function register() {
		foreach ( $this->handlers as $name => $callback ) {
			Hooks::register( $name, $callback );
		}
	}

	private function addCallbackHandlers( $store, $configuration ) {

		$propertyRegistry = new PropertyRegistry();

		/**
		 * @see https://github.com/SemanticMediaWiki/SemanticMediaWiki/blob/master/docs/technical/hooks.md
		 */
		$this->handlers['SMW::Property::initProperties'] = function () use ( $propertyRegistry ) {
			return $propertyRegistry->register();
		};

		/**
		 * @see https://www.mediawiki.org/wiki/Manual:Hooks/SkinTemplateOutputPageBeforeExec
		 */
		$this->handlers['SkinTemplateOutputPageBeforeExec'] = function ( &$skin, &$template ) use( $store, $configuration ) {

			$bySubpageLinksFinder = new BySubpageLinksFinder();
			$bySubpageLinksFinder->setSubpageDiscoverySupportState( $configuration['useSubpageFinderFallback'] );

			$byPropertyHierarchicalLinksFinder = new ByPropertyHierarchicalLinksFinder( $store );
			$byPropertyHierarchicalLinksFinder->setFindClosestDescendantState( $configuration['tryToFindClosestDescendant'] );
			$byPropertyHierarchicalLinksFinder->setPropertySearchPatternByNamespace( $configuration['propertySearchPatternByNamespace'] );

			$htmlBreadcrumbLinksBuilder = new HtmlBreadcrumbLinksBuilder(
				$byPropertyHierarchicalLinksFinder,
				$bySubpageLinksFinder
			);

			$htmlBreadcrumbLinksBuilder->setLinker( new DummyLinker() );
			$htmlBreadcrumbLinksBuilder->setBreadcrumbTrailStyleClass( $configuration['breadcrumbTrailStyleClass'] );
			$htmlBreadcrumbLinksBuilder->setBreadcrumbDividerStyleClass( $configuration['breadcrumbDividerStyleClass'] );

			$skinTemplateOutputModifier = new SkinTemplateOutputModifier( $htmlBreadcrumbLinksBuilder );
			$skinTemplateOutputModifier->modifyTemplate( $template );
			$skinTemplateOutputModifier->modifyOutput( $skin->getOutput() );

			return true;
		};

		/**
		 * @see https://www.mediawiki.org/wiki/Manual:Hooks/BeforePageDisplay
		 */
		$this->handlers['BeforePageDisplay'] = function ( &$output, &$skin ) use ( $configuration ) {

			$pageDisplayOutputModifier = new PageDisplayOutputModifier();
			$pageDisplayOutputModifier->setHideSubpageParentState( $configuration['hideSubpageParent'] );
			$pageDisplayOutputModifier->setSubpageByNamespace( $configuration['wgNamespacesWithSubpages'] );

			$pageDisplayOutputModifier->modifyOutput( $output );

			return true;
		};
	}

}
