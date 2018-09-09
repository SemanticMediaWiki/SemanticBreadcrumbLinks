<?php

namespace SBL;

use SMW\DIWikiPage;
use SMW\DIProperty;
use SMW\Store;
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
	private $propertySearchPatternByNamespace = [];

	/**
	 * @var array
	 */
	private $antecedentHierarchyLinks = [];

	/**
	 * @var array
	 */
	private $closestDescendantLinks = [];

	/**
	 * @since 1.0
	 *
	 * @param Store $store
	 */
	public function __construct( Store $store ) {
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
	public function findLinksBySubject( DIWikiPage $subject ) {

		if ( !isset( $this->propertySearchPatternByNamespace[ $subject->getNamespace() ] ) ) {
			return;
		}

		$propertySearchPattern = $this->propertySearchPatternByNamespace[ $subject->getNamespace() ];

		$requestOptions = new RequestOptions();
		$requestOptions->sort = true;
		$requestOptions->conditionConstraint = true;

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

		if ( $propertySearchPattern === [] ) {
			return null;
		}

		$property = array_shift( $propertySearchPattern );

		$propertyValues = $this->store->getPropertyValues(
			$subject,
			DIProperty::newFromUserLabel( $property ),
			$requestOptions
		);

		if ( $propertyValues === [] ) {
			return null;
		}

		foreach ( $propertyValues as $value ) {

			if ( !$value instanceOf DIWikiPage || $subject->equals( $value ) ) {
				continue;
			}

			// A flat display can only display one parent in its hierarchy
			$dataItem =  $this->store->getRedirectTarget( $value );
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

		// Limit the search
		$requestOptions->limit = 20;

		$children = $this->store->getPropertySubjects(
			$property,
			$subject,
			$requestOptions
		);

		foreach ( $children as $dataItem ) {

			if ( $subject->equals( $dataItem ) ) {
				continue;
			}

			$this->closestDescendantLinks[] = $this->store->getRedirectTarget(
				$dataItem
			);
		}
	}

}
