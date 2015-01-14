<?php

namespace SBL;

use OutputPage;
use Title;

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
	 * @param OutputPage $output
	 */
	public function modifyOutput( OutputPage $output ) {

		$output->addModules( 'ext.semanticbreadcrumblinks' );

		if ( !$this->hideSubpageParentState ) {
			return;
		}

		if ( $output->getTitle()->isSubpage() ) {
			$output->setPageTitle( $output->getTitle()->getSubpageText() );
		}
	}

}
