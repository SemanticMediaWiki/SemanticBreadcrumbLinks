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
	private $hideSubpageParentState;

	/**
	 * @var array
	 */
	private $subpageByNamespace;

	/**
	 * @since  1.0
	 *
	 * @param boolean $hideSubpageParentState
	 */
	public function setHideSubpageParentState( $hideSubpageParentState ) {
		$this->hideSubpageParentState = $hideSubpageParentState;
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

		if ( !$this->hideSubpageParentState || !$this->hasSubpageEnabledNamespace( $outputPage->getTitle()->getNamespace() ) ) {
			return;
		}

		if ( $outputPage->getTitle()->isSubpage() ) {
			$outputPage->setPageTitle( $this->getPageTitle( $outputPage->getTitle() ) );
		}
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

	private function hasSubpageEnabledNamespace( $namespace ) {
		return isset( $this->subpageByNamespace[ $namespace ] ) && $this->subpageByNamespace[ $namespace ];
	}

}
