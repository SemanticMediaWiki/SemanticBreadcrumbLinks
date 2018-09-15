<?php

namespace SBL;

use SMW\Store;
use SMW\ApplicationFactory;
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
	private $handlers = [];

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

		/**
		 * @see https://github.com/SemanticMediaWiki/SemanticMediaWiki/blob/master/docs/technical/hooks.md
		 */
		$this->handlers['SMW::Property::initProperties'] = function( $baseRegistry ) {

			$propertyRegistry = new PropertyRegistry();

			$propertyRegistry->register(
				$baseRegistry
			);

			return true;
		};

		/**
		 * @see https://www.semantic-mediawiki.org/wiki/Hooks/SMW::Parser::BeforeMagicWordsFinder
		 */
		$this->handlers['SMW::Parser::BeforeMagicWordsFinder'] = function( array &$magicWords ) {
			$magicWords = array_merge( $magicWords, [ 'SBL_NOBREADCRUMBLINKS' ] );
			return true;
		};

		/**
		 * @note This is bit of a hack but there is no other way to get access to
		 * the ParserOutput
		 *
		 * @see https://www.mediawiki.org/wiki/Manual:Hooks/OutputPageParserOutput
		 */
		$this->handlers['OutputPageParserOutput'] = function( &$outputPage, $parserOutput ) {
			$outputPage->smwmagicwords = $parserOutput->getExtensionData( 'smwmagicwords' );
			return true;
		};

		/**
		 * @see https://www.mediawiki.org/wiki/Manual:Hooks/SkinTemplateOutputPageBeforeExec
		 */
		$this->handlers['SkinTemplateOutputPageBeforeExec'] = function ( &$skin, &$template ) use( $store, $options ) {

			$bySubpageLinksFinder = new BySubpageLinksFinder();
			$bySubpageLinksFinder->setSubpageDiscoveryFallback(
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

			$htmlBreadcrumbLinksBuilder->hideSubpageParent(
				$options->get( 'hideSubpageParent' )
			);

			$skinTemplateOutputModifier = new SkinTemplateOutputModifier(
				$htmlBreadcrumbLinksBuilder,
				ApplicationFactory::getInstance()->getNamespaceExaminer()
			);

			$skinTemplateOutputModifier->modify( $skin->getOutput(), $template );

			return true;
		};

		/**
		 * @see https://www.mediawiki.org/wiki/Manual:Hooks/BeforePageDisplay
		 */
		$this->handlers['BeforePageDisplay'] = function ( &$output, &$skin ) use ( $options ) {

			$pageDisplayOutputModifier = new PageDisplayOutputModifier();

			$pageDisplayOutputModifier->hideSubpageParent(
				$options->get( 'hideSubpageParent' )
			);

			$pageDisplayOutputModifier->setSubpageByNamespace(
				$options->get( 'wgNamespacesWithSubpages' )
			);

			$pageDisplayOutputModifier->modifyOutput( $output );

			return true;
		};

		/**
		 * @see https://www.mediawiki.org/wiki/Manual:Hooks/ParserAfterTidy
		 */
		$this->handlers['ParserAfterTidy'] = function ( &$parser, &$text ) use ( $options ) {

			// ParserOptions::getInterfaceMessage is being used to identify whether a
			// parse was initiated by `Message::parse`
			if ( $parser->getTitle()->isSpecialPage() || $parser->getOptions()->getInterfaceMessage() ) {
				return true;
			}

			$parserData = ApplicationFactory::getInstance()->newParserData(
				$parser->getTitle(),
				$parser->getOutput()
			);

			$subpageParentAnnotator = new SubpageParentAnnotator(
				$parserData
			);

			$subpageParentAnnotator->enableSubpageParentAnnotation(
				$options->get( 'enabledSubpageParentAnnotation' )
			);

			$subpageParentAnnotator->disableTranslationSubpageAnnotation(
				$options->get( 'disableTranslationSubpageAnnotation' )
			);

			$subpageParentAnnotator->addAnnotation();

			return true;
		};
	}

}
