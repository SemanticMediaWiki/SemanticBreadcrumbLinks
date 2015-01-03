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
	 * @var HierarchicalLinksFinderByProperty
	 */
	private $hierarchicalLinksFinderByProperty;

	/**
	 * @var SubpageLinksFinder
	 */
	private $subpageLinksFinder;

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
	 * @var boolean
	 */
	private $isRTL = false;

	/**
	 * @since 1.0
	 *
	 * @param HierarchicalLinksFinderByProperty $hierarchicalLinksFinderByProperty
	 * @param SubpageLinksFinder $subpageLinksFinder
	 */
	public function __construct( HierarchicalLinksFinderByProperty $hierarchicalLinksFinderByProperty, SubpageLinksFinder $subpageLinksFinder ) {
		$this->hierarchicalLinksFinderByProperty = $hierarchicalLinksFinderByProperty;
		$this->subpageLinksFinder = $subpageLinksFinder;
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

		$subject = DIWikiPage::newFromTitle( $title );

		$this->hierarchicalLinksFinderByProperty->tryToFindLinksFor( $subject );

		$parents = $this->hierarchicalLinksFinderByProperty->getParents();
		$children = $this->hierarchicalLinksFinderByProperty->getChildren();

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

		if ( $parents !== array() || !$this->subpageLinksFinder->canUseSubpageDiscoveryForFallback() ) {
			return $parents;
		}

		$this->subpageLinksFinder->tryToFindLinksFor( $subject );

		return $this->subpageLinksFinder->getParents();
	}

	private function formatToFlatList( DIWikiPage $subject, $parents, $children ) {

		$parent = '';

		foreach ( $parents as $breadcrumb ) {
			$parent .= $this->wrapHtml( 'parent', $this->getDvShortHtmlText( $breadcrumb, $this->linker ) ) .  $this->wrapHtml( 'arrow-right' );
		}

		$child = '';

		foreach ( $children as $breadcrumb ) {
			$child .=  $this->wrapHtml( 'arrow-left' ) . $this->wrapHtml( 'child', $this->getDvShortHtmlText( $breadcrumb, $this->linker ) );
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
			'class' => 'sbl-breadcrumb-' . $subClass ),
			$html
		);
	}

}
