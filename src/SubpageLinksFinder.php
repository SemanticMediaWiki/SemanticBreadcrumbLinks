<?php

namespace SBL;

use SMW\DIWikiPage;

use Title;

/**
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class SubpageLinksFinder {

	/**
	 * @var integer
	 */
	private $maxDepthOfFinderHierarchy = 2;

	/**
	 * @var boolean
	 */
	private $subpageDiscoverySupportState = true;

	/**
	 * @var array
	 */
	private $antecedentHierarchyLinks = array();

	/**
	 * @since 1.0
	 *
	 * @param integer $maxDepthOfFinderHierarchy
	 */
	public function setMaxDepthForFinderHierarchy( $maxDepthOfFinderHierarchy ) {
		$this->maxDepthOfFinderHierarchy = $maxDepthOfFinderHierarchy;
	}

	/**
	 * @since 1.0
	 *
	 * @param boolean $subpageDiscoverySupportState
	 */
	public function setSubpageDiscoverySupportState( $subpageDiscoverySupportState ) {
		$this->subpageDiscoverySupportState = $subpageDiscoverySupportState;
	}

	/**
	 * @since 1.0
	 *
	 * @return boolean
	 */
	public function canUseSubpageDiscoveryForFallback() {
		return $this->subpageDiscoverySupportState;
	}

	/**
	 * @since  1.0
	 *
	 * @param DIWikiPage $subject
	 */
	public function tryToFindLinksFor( DIWikiPage $subject ) {

		$prefixedText = $subject->getTitle()->getPrefixedText();

		if ( !$this->canBuildLinksFromText( $prefixedText ) ) {
			return;
		}

		$this->buildHierarchicalLinksFromText( $prefixedText );
	}

	/**
	 * @since  1.0
	 *
	 * @return array
	 */
	public function getParents() {
		return $this->antecedentHierarchyLinks;
	}

	private function canBuildLinksFromText( $text ) {
		return preg_match( '/\//', $text );
	}

	private function buildHierarchicalLinksFromText( $text ) {

		$subpages = array();

		$links = explode( '/', $text );

		array_pop( $links );

		$growinglink = '';

		foreach ( $links as $link ) {
			$growinglink .= $link;
			$subpages[] = DIWikiPage::newFromTitle( Title::newFromText( $growinglink ) );
			$growinglink .= '/';
		}

		krsort( $subpages );
		$iterator = 0;

		foreach ( $subpages as $subpage ) {

			if ( $iterator++ >= $this->maxDepthOfFinderHierarchy ) {
				break;
			}

			$this->antecedentHierarchyLinks[] = $subpage;
		}

		krsort( $this->antecedentHierarchyLinks );
	}

}
