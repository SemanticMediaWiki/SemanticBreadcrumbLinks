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
	 * @param OutputPage $output
	 */
	public function modifyOutput( OutputPage $output ) {

		$output->addModules( 'ext.semanticbreadcrumblinks' );

		if ( !$this->hideSubpageParentState || !$this->hasSubpageEnabledNamespace( $output->getTitle()->getNamespace() ) ) {
			return;
		}

		if ( $output->getTitle()->isSubpage() ) {
			$output->setPageTitle( $output->getTitle()->getSubpageText() );
		}
	}

	private function hasSubpageEnabledNamespace( $namespace ) {
		return isset( $this->subpageByNamespace[ $namespace ] ) && $this->subpageByNamespace[ $namespace ];
	}

}
