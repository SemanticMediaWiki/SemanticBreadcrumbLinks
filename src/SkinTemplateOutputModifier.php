<?php

namespace SBL;

use MediaWiki\Output\OutputPage;
use MediaWiki\Title\Title;
use SMW\NamespaceExaminer;

/**
 * @license GPL-2.0-or-later
 * @since 1.0
 *
 * @author mwjames
 */
class SkinTemplateOutputModifier {

	/**
	 * @var HtmlBreadcrumbLinksBuilder
	 */
	private $htmlBreadcrumbLinksBuilder;

	/**
	 * @var NnamespaceExaminer
	 */
	private $namespaceExaminer;

	/**
	 * @since 1.0
	 *
	 * @param HtmlBreadcrumbLinksBuilder $htmlBreadcrumbLinksBuilder
	 * @param NamespaceExaminer $namespaceExaminer
	 */
	public function __construct( HtmlBreadcrumbLinksBuilder $htmlBreadcrumbLinksBuilder, NamespaceExaminer $namespaceExaminer ) {
		$this->htmlBreadcrumbLinksBuilder = $htmlBreadcrumbLinksBuilder;
		$this->namespaceExaminer = $namespaceExaminer;
	}

	/**
	 * @since 2.1
	 *
	 * @param OutputPage $output
	 * @param &$subpages
	 */
	public function modify( OutputPage $output, &$subpages ) {
		if ( !$this->canModifyOutput( $output ) ) {
			return;
		}

		$title = $output->getTitle();
		$this->htmlBreadcrumbLinksBuilder->buildBreadcrumbs( $title );

		$this->htmlBreadcrumbLinksBuilder->isRTL(
			$title->getPageLanguage()->isRTL()
		);

		$subpages .= $this->htmlBreadcrumbLinksBuilder->getHtml();
	}

	private function canModifyOutput( OutputPage $output ) {
		if ( !$this->isEnabled( $output->getTitle() ) ) {
			return false;
		}

		$magicWords = $output->getMetadata()->getExtensionData( 'smwmagicwords' ) ?? [];

		if ( in_array( 'SBL_NOBREADCRUMBLINKS', $magicWords ) ) {
			return false;
		}

		return true;
	}

	private function isEnabled( Title $title ) {
		return $title->isKnown() && !$title->isSpecialPage() && $this->namespaceExaminer->isSemanticEnabled( $title->getNamespace() );
	}

}
