<?php

namespace SBL;

use OutputPage;
use Title;
use SMW\DataValueFactory;
use SMW\DIWikiPage;

/**
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class PageDisplayOutputModifier {

	/**
	 * @var boolean
	 */
	private $hideSubpageParent;

	/**
	 * @var array
	 */
	private $subpageByNamespace;

	/**
	 * @since  1.0
	 *
	 * @param boolean $hideSubpageParent
	 */
	public function hideSubpageParent( $hideSubpageParent ) {
		$this->hideSubpageParent = $hideSubpageParent;
	}

	/**
	 * @since  1.0
	 *
	 * @param array $subpageByNamespace
	 */
	public function setSubpageByNamespace( array $subpageByNamespace ) {
		$this->subpageByNamespace = $subpageByNamespace;
	}

	/**
	 * @since  1.0
	 *
	 * @param OutputPage $outputPage
	 */
	public function modifyOutput( OutputPage $outputPage ) {

		$outputPage->addModuleStyles( 'ext.semanticbreadcrumblinks.styles' );
		$outputPage->addModules( 'ext.semanticbreadcrumblinks' );

		$title = $outputPage->getTitle();

		if ( !$this->hideSubpageParent || !$this->hasEnabledSubpageByNamespace( $title->getNamespace() ) ) {
			return;
		}

		if ( $this->isSubpage( $title ) ) {
			$outputPage->setPageTitle( $this->getPageTitle( $title ) );
		}
	}

	private function isSubpage( Title $title ) {

		if ( !$title->isSubpage() ) {
			return false;
		}

		$parts = explode( '/', $title->getText() );

		if ( count( $parts ) > 1 ) {
			unset( $parts[count( $parts ) - 1] );
		}

		$base = implode( '/', $parts );

		// #23 (Foo /Bar vs. Foo/ Bar)
		return substr( $base, -1 ) !== ' ';
	}

	private function getPageTitle( Title $title ) {

		$displayTitle = '';

		$dataValue = DataValueFactory::getInstance()->newDataItemValue(
			DIWikiPage::newFromTitle( $title )
		);

		// 2.4+
		if ( method_exists( $dataValue , 'getDisplayTitle' ) ) {
			$displayTitle = $dataValue->getDisplayTitle();
		}

		return $displayTitle !== '' ? $displayTitle : $title->getSubpageText();
	}

	private function hasEnabledSubpageByNamespace( $namespace ) {
		return isset( $this->subpageByNamespace[ $namespace ] ) && $this->subpageByNamespace[ $namespace ];
	}

}
