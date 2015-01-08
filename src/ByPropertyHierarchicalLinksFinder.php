<?php

namespace SBL;

use SMW\DIWikiPage;
use SMW\DIProperty;

use SMWRequestOptions as RequestOptions;
use Title;

/**
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class ByPropertyHierarchicalLinksFinder {

	/**
	 * @var Store
	 */
	private $store;

	/**
	 * @var boolean
	 */
	private $findClosestDescendant = true;

	/**
	 * @var array
	 */
	private $propertySearchPatternByNamespace = array();

	/**
	 * @var array
	 */
	private $antecedentHierarchyLinks = array();

	/**
	 * @var array
	 */
	private $closestDescendantLinks = array();

	/**
	 * @since 1.0
	 *
	 * @param Store $store
	 */
	public function __construct( $store ) {
		$this->store = $store;
	}

	/**
	 * @since 1.0
	 *
	 * @param boolean $findClosestDescendant
	 */
	public function setFindClosestDescendantState( $findClosestDescendant ) {
		$this->findClosestDescendant = $findClosestDescendant;
	}

	/**
	 * @since 1.0
	 *
	 * @param array $propertySearchPatternByNamespace
	 */
	public function setPropertySearchPatternByNamespace( array $propertySearchPatternByNamespace ) {
		$this->propertySearchPatternByNamespace = $propertySearchPatternByNamespace;
	}

	/**
	 * @since  1.0
	 *
	 * @param DIWikiPage $subject
	 */
	public function tryToFindLinksFor( DIWikiPage $subject ) {

		if ( !isset( $this->propertySearchPatternByNamespace[ $subject->getNamespace() ] ) ) {
			return;
		}

		$propertySearchPattern = $this->propertySearchPatternByNamespace[ $subject->getNamespace() ];

		$requestOptions = new RequestOptions();
		$requestOptions->sort = true;

		// Use 3 as buffer to broaden match possibilities
		$requestOptions->limit = 3;

		$this->doResolveAntecedentHierarchyRecursively(
			$subject,
			$propertySearchPattern,
			$requestOptions
		);

		krsort( $this->antecedentHierarchyLinks );

		if ( !$this->findClosestDescendant ) {
			return;
		}

		$this->doFindClosestDescendantByInverseLink(
			$subject,
			$propertySearchPattern,
			$requestOptions
		);
	}

	/**
	 * @since  1.0
	 *
	 * @return array
	 */
	public function getParents() {
		return $this->antecedentHierarchyLinks;
	}

	/**
	 * @since  1.0
	 *
	 * @return array
	 */
	public function getChildren() {
		return $this->closestDescendantLinks;
	}

	private function doResolveAntecedentHierarchyRecursively( DIWikiPage $subject, array $propertySearchPattern, RequestOptions $requestOptions, $currentDepth = 0 ) {

		$dataItem = null;

		if ( $propertySearchPattern === array() ) {
			return null;
		}

		$property = array_shift( $propertySearchPattern );

		$propertyValues = $this->store->getPropertyValues(
			$subject,
			DIProperty::newFromUserLabel( $property ),
			$requestOptions
		);

		if ( $propertyValues === array() ) {
			return null;
		}

		foreach ( $propertyValues as $value ) {

			if ( !$value instanceOf DIWikiPage || $subject->equals( $value ) ) {
				continue;
			}

			// A flat display can only display one parent in its hierarchy
			$dataItem = $value;
			break;
		}

		if ( $dataItem === null ) {
			return null;
		}

		$this->antecedentHierarchyLinks[] = $dataItem;
		$currentDepth++;

		return $this->doResolveAntecedentHierarchyRecursively(
			$dataItem,
			$propertySearchPattern,
			$requestOptions,
			$currentDepth
		);
	}

	private function doFindClosestDescendantByInverseLink( DIWikiPage $subject, array $propertySearchPattern, RequestOptions $requestOptions ) {

		$property = array_shift( $propertySearchPattern );

		$property = DIProperty::newFromUserLabel( $property );

		if ( $property->findPropertyTypeId() !== '_wpg' ) {
			return;
		}

		$children = $this->store->getPropertySubjects(
			$property,
			$subject,
			$requestOptions
		);

		foreach ( $children as $dataItem ) {

			// A flat display can only display one child
			if ( $this->closestDescendantLinks !== array() ) {
				break;
			}

			if ( $subject->equals( $dataItem ) ) {
				continue;
			}

			$this->closestDescendantLinks[] = $dataItem;
		}
	}

}
