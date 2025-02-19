<?php

namespace SBL;

use SMW\ApplicationFactory;
use SMW\NamespaceExaminer;
use OutputPage;
use Action;
use Title;

/**
 * @license GNU GPL v2+
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
	 * @param OutputPage $output
	 * @param &$subpages
	 *@since 1.5
	 *
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

		if ( isset( $output->smwmagicwords ) && in_array( 'SBL_NOBREADCRUMBLINKS', $output->smwmagicwords ) ) {
			return false;
		}

		return true;
	}

	private function isEnabled( Title $title ) {
		return $title->isKnown() && !$title->isSpecialPage() && $this->namespaceExaminer->isSemanticEnabled( $title->getNamespace() );
	}

}
