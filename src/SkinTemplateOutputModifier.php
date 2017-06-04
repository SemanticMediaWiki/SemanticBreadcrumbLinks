<?php

namespace SBL;

use SMW\ApplicationFactory;
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
	 * @since 1.0
	 *
	 * @param HtmlBreadcrumbLinksBuilder $htmlBreadcrumbLinksBuilder
	 */
	public function __construct( HtmlBreadcrumbLinksBuilder $htmlBreadcrumbLinksBuilder ) {
		$this->htmlBreadcrumbLinksBuilder = $htmlBreadcrumbLinksBuilder;
	}

	/**
	 * @since  1.0
	 *
	 * @param OutputPage $output
	 *
	 * @return boolean
	 */
	public function modifyOutput( OutputPage $output ) {
		return $this->canModifyOutput( $output ) ? $this->doModifyOutput( $output ) : true;
	}

	/**
	 * @since  1.0
	 *
	 * @param &$template
	 */
	public function modifyTemplate( &$template ) {

		// Always set subtitle to be empty when SBL is used to avoid any output
		// distraction
		$template->data['subtitle'] = '';
	}

	private function canModifyOutput( OutputPage $output ) {

		if ( !$this->isEnabled( $output->getTitle() ) ) {
			return false;
		}

		if ( Action::getActionName( $output->getContext() ) !== 'view' ) {
			return false;
		}

		if ( isset( $output->smwmagicwords ) && in_array( 'SBL_NOBREADCRUMBLINKS', $output->smwmagicwords ) ) {
			return false;
		}

		return true;
	}

	private function doModifyOutput( OutputPage $output ) {

		$this->htmlBreadcrumbLinksBuilder->buildBreadcrumbs( $output->getTitle() );

		$this->htmlBreadcrumbLinksBuilder->isRTL(
			$output->getTitle()->getPageLanguage()->isRTL()
		);

		$output->prependHTML( $this->htmlBreadcrumbLinksBuilder->getHtml() );

		return true;
	}

	private function isEnabled( Title $title ) {
		return $title->isKnown() &&
			!$title->isSpecialPage() &&
			ApplicationFactory::getInstance()->getNamespaceExaminer()->isSemanticEnabled( $title->getNamespace() );
	}

}
