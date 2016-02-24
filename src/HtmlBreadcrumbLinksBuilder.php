<?php

namespace SBL;

use SMW\DIWikiPage;
use SMW\DataValueFactory;
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
	 */
	public function buildBreadcrumbs( Title $title ) {

		if ( $title->isRedirect() ) {
			return;
		}

		// Ensure no subobject is used by replacing the fragment
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

		list( $child, $data ) = $this->findElementsForChildren( $children  );

		if ( $parent !== '' || $child !== '' ) {
			$this->breadcrumbs = $parent . $this->wrapHtml( 'location', $this->getDvShortHtmlText( $subject ) ) . $child . $this->addHtmlDataElement( $data );
		}
	}

	private function getDvShortHtmlText( $subject, $linker = null ) {

		$dataValue = DataValueFactory::getInstance()->newDataItemValue(
			$subject
		);

		return $dataValue->getShortHtmlText( $linker );
	}

	private function wrapHtml( $subClass, $html = '' ) {
		return Html::rawElement( 'span', array(
			'class' => $this->breadcrumbDividerStyleClass . '-' . $subClass ),
			$html
		);
	}

	private function findElementsForChildren( array $children ) {

		$child = '';
		$data = '';

		foreach ( $children as $breadcrumb ) {

			// The first child is added as visible element while others
			// are added as data-element
			if ( $child !== '' ) {
				$data .=  $this->addHtmlListElement( $this->getDvShortHtmlText( $breadcrumb, $this->linker ) );
				continue;
			}

			$child .=  $this->wrapHtml( 'left' ) . $this->wrapHtml( 'child', $this->getDvShortHtmlText( $breadcrumb, $this->linker ) );
		}

		return array( $child, $data );
	}

	private function addHtmlListElement( $html = '' ) {
		return Html::rawElement( 'li', array(), $html );
	}

	private function addHtmlDataElement( $data = '' ) {

		if ( $data === '' ) {
			return '';
		}

		return Html::rawElement( 'span', array(
			'class' => 'sbl-breadcrumb-children',
			'data-children' => $data ),
			''
		);
	}

}
