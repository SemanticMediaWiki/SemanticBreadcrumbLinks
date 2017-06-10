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
	 * @var DataValueFactory
	 */
	private $dataValueFactory;

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
	 * @var boolean
	 */
	private $hasChildren = false;

	/**
	 * @var boolean
	 */
	private $hideSubpageParent = false;

	/**
	 * @since 1.0
	 *
	 * @param ByPropertyHierarchicalLinksFinder $byPropertyHierarchicalLinksFinder
	 * @param BySubpageLinksFinder $bySubpageLinksFinder
	 */
	public function __construct( ByPropertyHierarchicalLinksFinder $byPropertyHierarchicalLinksFinder, BySubpageLinksFinder $bySubpageLinksFinder ) {
		$this->byPropertyHierarchicalLinksFinder = $byPropertyHierarchicalLinksFinder;
		$this->bySubpageLinksFinder = $bySubpageLinksFinder;
		$this->dataValueFactory = DataValueFactory::getInstance();
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
	 * @since 1.3
	 *
	 * @param DataValueFactory $dataValueFactory
	 */
	public function setDataValueFactory( DataValueFactory $dataValueFactory ) {
		$this->dataValueFactory = $dataValueFactory;
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
	 * @since 1.3
	 *
	 * @param boolean $hideSubpageParent
	 */
	public function hideSubpageParent( $hideSubpageParent ) {
		$this->hideSubpageParent = $hideSubpageParent;
	}

	/**
	 * @since 1.0
	 *
	 * @param boolean $isRTL
	 */
	public function isRTL( $isRTL ) {
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

		$this->byPropertyHierarchicalLinksFinder->findLinksBySubject( $subject );

		$parents = $this->byPropertyHierarchicalLinksFinder->getParents();
		$children = $this->byPropertyHierarchicalLinksFinder->getChildren();

		$parents = $this->getSubstituteLinksParentsOnDiscoveryFallback(
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

		return Html::rawElement( 'div', [
			'id'    => 'sbl-breadcrumbs',
			'class' => $this->breadcrumbTrailStyleClass,
			'dir'   => $this->isRTL ? 'rtl' : 'ltr' ],
			$this->breadcrumbs
		);
	}

	private function getSubstituteLinksParentsOnDiscoveryFallback( $subject, $parents ) {

		if ( $parents !== [] || !$this->bySubpageLinksFinder->isDiscoveryFallback() ) {
			return $parents;
		}

		$this->bySubpageLinksFinder->findLinksBySubject( $subject );

		return $this->bySubpageLinksFinder->getParents();
	}

	private function formatToFlatList( DIWikiPage $subject, $parents, $children ) {

		$parent = '';

		foreach ( $parents as $breadcrumb ) {
			$parent .= $this->wrapHtml( 'parent', $this->getDvShortHtmlText( $breadcrumb, $this->linker ) ) .  $this->wrapHtml( 'right' );
		}

		$this->hasChildren = count( $children ) > 1;

		list( $child, $data ) = $this->findElementsForChildren( $children  );

		if ( $parent !== '' || $child !== '' ) {
			$this->breadcrumbs = $parent . $this->wrapHtml( 'location', $this->getDvShortHtmlText( $subject ) ) . $child . $this->addHtmlDataElement( $data );
		}
	}

	private function getDvShortHtmlText( $subject, $linker = null ) {

		$displayTitle = '';

		$dataValue = $this->dataValueFactory->newDataValueByItem(
			$subject
		);

		// 2.4+
		if ( method_exists( $dataValue , 'getDisplayTitle' ) ) {
			$displayTitle = $dataValue->getDisplayTitle();
		}

		$dataValue->setCaption(
			$this->hideSubpageParent && $displayTitle === '' ? $subject->getTitle()->getSubpageText() : false
		);

		// Make sure non-linked titles use the _DTITLE, if available
		if ( $linker === null && $displayTitle !== '' ) {
			$dataValue->setCaption( $displayTitle );
		}

		return $dataValue->getShortHtmlText( $linker );
	}

	private function wrapHtml( $subClass, $html = '' ) {
		return Html::rawElement( 'span', [
				'class' => $this->breadcrumbDividerStyleClass . '-' . $subClass,
				'style' => $subClass === 'child' && $this->hasChildren ? 'font-style:italic;' : ''
			],
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
				$this->hasChildren = true;
				$data .=  $this->addHtmlListElement( $this->getDvShortHtmlText( $breadcrumb, $this->linker ) );
				continue;
			}

			$child .=  $this->wrapHtml( 'left' ) . $this->wrapHtml( 'child', $this->getDvShortHtmlText( $breadcrumb, $this->linker ) );
		}

		return [ $child, $data ];
	}

	private function addHtmlListElement( $html = '' ) {
		return Html::rawElement( 'li', [], $html );
	}

	private function addHtmlDataElement( $data = '' ) {

		if ( $data === '' ) {
			return '';
		}

		return Html::rawElement( 'span', [
			'class' => 'sbl-breadcrumb-children',
			'data-children' => '<ul class="sbl-breadcrumb-children-list">' . $data . '</ul>' ],
			''
		);
	}

}
