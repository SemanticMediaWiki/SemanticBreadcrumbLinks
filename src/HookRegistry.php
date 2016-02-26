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
	 * @param Options $options
	 */
	public function __construct( Store $store, Options $options ) {
		$this->addCallbackHandlers( $store, $options );
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
	public function getHandlerFor( $name ) {
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

	private function addCallbackHandlers( $store, $options ) {

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
		$this->handlers['SkinTemplateOutputPageBeforeExec'] = function ( &$skin, &$template ) use( $store, $options ) {

			$bySubpageLinksFinder = new BySubpageLinksFinder();
			$bySubpageLinksFinder->setSubpageDiscoverySupportState(
				$options->get( 'useSubpageFinderFallback' )
			);

			$byPropertyHierarchicalLinksFinder = new ByPropertyHierarchicalLinksFinder( $store );
			$byPropertyHierarchicalLinksFinder->setFindClosestDescendantState(
				$options->get( 'tryToFindClosestDescendant' )
			);

			$byPropertyHierarchicalLinksFinder->setPropertySearchPatternByNamespace(
				$options->get( 'propertySearchPatternByNamespace' )
			);

			$htmlBreadcrumbLinksBuilder = new HtmlBreadcrumbLinksBuilder(
				$byPropertyHierarchicalLinksFinder,
				$bySubpageLinksFinder
			);

			$htmlBreadcrumbLinksBuilder->setLinker( new DummyLinker() );
			$htmlBreadcrumbLinksBuilder->setBreadcrumbTrailStyleClass(
				$options->get( 'breadcrumbTrailStyleClass' )
			);

			$htmlBreadcrumbLinksBuilder->setBreadcrumbDividerStyleClass(
				$options->get( 'breadcrumbDividerStyleClass' )
			);

			$htmlBreadcrumbLinksBuilder->setHideSubpageParentState(
				$options->get( 'hideSubpageParent' )
			);

			$skinTemplateOutputModifier = new SkinTemplateOutputModifier( $htmlBreadcrumbLinksBuilder );
			$skinTemplateOutputModifier->modifyTemplate( $template );
			$skinTemplateOutputModifier->modifyOutput( $skin->getOutput() );

			return true;
		};

		/**
		 * @see https://www.mediawiki.org/wiki/Manual:Hooks/BeforePageDisplay
		 */
		$this->handlers['BeforePageDisplay'] = function ( &$output, &$skin ) use ( $options ) {

			$pageDisplayOutputModifier = new PageDisplayOutputModifier();

			$pageDisplayOutputModifier->setHideSubpageParentState(
				$options->get( 'hideSubpageParent' )
			);

			$pageDisplayOutputModifier->setSubpageByNamespace(
				$options->get( 'wgNamespacesWithSubpages' )
			);

			$pageDisplayOutputModifier->modifyOutput( $output );

			return true;
		};
	}

}
