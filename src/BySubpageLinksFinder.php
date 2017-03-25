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
class BySubpageLinksFinder {

	/**
	 * @var boolean
	 */
	private $isDiscoveryFallback = true;

	/**
	 * @var array
	 */
	private $antecedentHierarchyLinks = array();

	/**
	 * @since 1.0
	 *
	 * @param boolean $isDiscoveryFallback
	 */
	public function setSubpageDiscoveryFallback( $isDiscoveryFallback ) {
		$this->isDiscoveryFallback = $isDiscoveryFallback;
	}

	/**
	 * @since 1.0
	 *
	 * @return boolean
	 */
	public function isDiscoveryFallback() {
		return $this->isDiscoveryFallback;
	}

	/**
	 * @since  1.0
	 *
	 * @param DIWikiPage $subject
	 */
	public function findLinksBySubject( DIWikiPage $subject ) {

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

		$growinglink = '';
		$links = explode( '/', $text );

		// Remove the source
		array_pop( $links );

		foreach ( $links as $link ) {

			if ( $link !== '' && substr( $link, -1 ) !== ' ' ) {
				$growinglink .= $link;
				$this->antecedentHierarchyLinks[] = DIWikiPage::newFromTitle( Title::newFromText( $growinglink ) );
			}

			$growinglink .= '/';
		}
	}

}
