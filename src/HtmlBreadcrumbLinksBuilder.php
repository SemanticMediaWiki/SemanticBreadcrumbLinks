<?php

namespace SBL;

use SMW\DIWikiPage;

use SMWWikiPageValue as WikiPageValue;

use Title;
use Html;
use DummyLinker;

/**
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class HtmlBreadcrumbLinksBuilder {

	/**
	 * @var ByPropertyHierarchicalLinksFinder
	 */
	private $byPropertyHierarchicalLinksFinder;

	/**
	 * @var BySubpageLinksFinder
	 */
	private $bySubpageLinksFinder;

	/**
	 * @var DummyLinker|null
	 */
	private $linker = null;

	/**
	 * @var string
	 */
	private $breadcrumbs = '';

	/**
	 * @var string
	 */
	private $breadcrumbTrailStyleClass = 'sbl-breadcrumb-trail-boxed';

	/**
	 * @var string
	 */
	private $breadcrumbDividerStyleClass = 'sbl-breadcrumb-arrow';

	/**
	 * @var boolean
	 */
	private $isRTL = false;

	/**
	 * @since 1.0
	 *
	 * @param ByPropertyHierarchicalLinksFinder $byPropertyHierarchicalLinksFinder
	 * @param BySubpageLinksFinder $bySubpageLinksFinder
	 */
	public function __construct( ByPropertyHierarchicalLinksFinder $byPropertyHierarchicalLinksFinder, BySubpageLinksFinder $bySubpageLinksFinder ) {
		$this->byPropertyHierarchicalLinksFinder = $byPropertyHierarchicalLinksFinder;
		$this->bySubpageLinksFinder = $bySubpageLinksFinder;
	}

	/**
	 * @since 1.0
	 *
	 * @param DummyLinker $linker
	 */
	public function setLinker( DummyLinker $linker ) {
		$this->linker = $linker;
	}

	/**
	 * @since 1.0
	 *
	 * @param string $breadcrumbTrailStyleClass
	 */
	public function setBreadcrumbTrailStyleClass( $breadcrumbTrailStyleClass ) {
		$this->breadcrumbTrailStyleClass = $breadcrumbTrailStyleClass;
	}

	/**
	 * @since 1.0
	 *
	 * @param string $breadcrumbDividerStyleClass
	 */
	public function setBreadcrumbDividerStyleClass( $breadcrumbDividerStyleClass ) {
		$this->breadcrumbDividerStyleClass = $breadcrumbDividerStyleClass;
	}

	/**
	 * @since 1.0
	 *
	 * @param boolean $isRTL
	 */
	public function setRTLDirectionalityState( $isRTL ) {
		$this->isRTL = $isRTL;
	}

	/**
	 * @since  1.0
	 *
	 * @param Title $title
	 *
	 * @return HtmlBreadcrumbsBuilder
	 */
	public function buildBreadcrumbs( Title $title ) {

		$title->setFragment( '' );
		$subject = DIWikiPage::newFromTitle( $title );

		$this->byPropertyHierarchicalLinksFinder->tryToFindLinksFor( $subject );

		$parents = $this->byPropertyHierarchicalLinksFinder->getParents();
		$children = $this->byPropertyHierarchicalLinksFinder->getChildren();

		$parents = $this->tryToUseSubpageHierarchyFallback(
			$subject,
			$parents
		);

		$this->formatToFlatList( $subject, $parents, $children );

		return $this;
	}

	/**
	 * @since 1.0
	 *
	 * @return string
	 */
	public function getHtml() {

		if ( $this->breadcrumbs === '' ) {
			return $this->breadcrumbs;
		}

		return Html::rawElement( 'div', array(
			'id'    => 'sbl-breadcrumbs',
			'class' => $this->breadcrumbTrailStyleClass,
			'dir'   => $this->isRTL ? 'rtl' : 'ltr' ),
			$this->breadcrumbs
		);
	}

	private function tryToUseSubpageHierarchyFallback( $subject, $parents ) {

		if ( $parents !== array() || !$this->bySubpageLinksFinder->canUseSubpageDiscoveryForFallback() ) {
			return $parents;
		}

		$this->bySubpageLinksFinder->tryToFindLinksFor( $subject );

		return $this->bySubpageLinksFinder->getParents();
	}

	private function formatToFlatList( DIWikiPage $subject, $parents, $children ) {

		$parent = '';

		foreach ( $parents as $breadcrumb ) {
			$parent .= $this->wrapHtml( 'parent', $this->getDvShortHtmlText( $breadcrumb, $this->linker ) ) .  $this->wrapHtml( 'right' );
		}

		$child = '';

		foreach ( $children as $breadcrumb ) {
			$child .=  $this->wrapHtml( 'left' ) . $this->wrapHtml( 'child', $this->getDvShortHtmlText( $breadcrumb, $this->linker ) );
		}

		if ( $parent !== '' || $child !== '' ) {
			$this->breadcrumbs = $parent . $this->wrapHtml( 'location', $this->getDvShortHtmlText( $subject ) ) . $child;
		}
	}

	private function getDvShortHtmlText( $subject, $linker = null ) {
		$dataValue = new WikiPageValue( '_wpg' );
		$dataValue->setDataItem( $subject );
		$dataValue->setCaption( $subject->getTitle()->getSubpageText() );
		return $dataValue->getShortHtmlText( $linker );
	}

	private function wrapHtml( $subClass, $html = '' ) {
		return Html::rawElement( 'span', array(
			'class' => $this->breadcrumbDividerStyleClass . '-' . $subClass ),
			$html
		);
	}

}
