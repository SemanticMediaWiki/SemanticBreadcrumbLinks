<?php

namespace SBL\Tests;

use SBL\HierarchicalLinksFinderByProperty;

use SMW\DIWikiPage;
use SMW\DIProperty;
use SMW\ApplicationFactory;

use Title;

/**
 * @covers \SBL\HierarchicalLinksFinderByProperty
 *
 * @group semantic-breadcrumb-links
 *
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class HierarchicalLinksFinderByPropertyTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {

		$store = $this->getMockBuilder( '\SMW\Store' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$this->assertInstanceOf(
			'\SBL\HierarchicalLinksFinderByProperty',
			new HierarchicalLinksFinderByProperty( $store )
		);
	}

	public function testNoValidConfigurationForAnyNamespace() {

		$store = $this->getMockBuilder( '\SMW\Store' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$instance = new HierarchicalLinksFinderByProperty( $store );

		$instance->tryToFindLinksFor( new DIWikiPage( 'Foo', NS_MAIN ) );

		$this->assertEmpty(
			$instance->getParents()
		);

		$this->assertEmpty(
			$instance->getChildren()
		);
	}

	public function testEmptyResultByTryingToFindAntecedent() {

		$subject = new DIWikiPage( 'Foo', NS_MAIN );
		$property = DIProperty::newFromUserLabel( 'Bar' );

		$store = $this->getMockBuilder( '\SMW\Store' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$store->expects( $this->once() )
			->method( 'getPropertyValues' )
			->with(
				$this->equalTo( $subject ),
				$this->equalTo( $property ) )
			->will( $this->returnValue( array() ) );

		$instance = new HierarchicalLinksFinderByProperty( $store );

		$instance->tryToFindClosestDescendant( false );
		$instance->setPropertySearchPatternByNamespace(
			array( NS_MAIN => array( 'Bar' ) )
		);

		$instance->tryToFindLinksFor( $subject );

		$this->assertEmpty(
			$instance->getParents()
		);

		$this->assertEmpty(
			$instance->getChildren()
		);
	}

	public function testFindAntecedentForMultiplePropertySearchPattern() {

		$subject = new DIWikiPage( 'Foo', NS_MAIN );

		$store = $this->getMockBuilder( '\SMW\Store' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$store->expects( $this->at( 0 ) )
			->method( 'getPropertyValues' )
			->with(
				$this->equalTo( $subject ),
				$this->equalTo( DIProperty::newFromUserLabel( 'Bar' ) ) )
			->will( $this->returnValue( array( new DIWikiPage( 'Ichi', NS_MAIN ) ) ) );

		$store->expects( $this->at( 1 ) )
			->method( 'getPropertyValues' )
			->with(
				$this->equalTo( new DIWikiPage( 'Ichi', NS_MAIN )  ),
				$this->equalTo( DIProperty::newFromUserLabel( 'Yin' )) )
			->will( $this->returnValue( array( new DIWikiPage( 'Ni', NS_MAIN ) ) ) );

		$instance = new HierarchicalLinksFinderByProperty( $store );

		$instance->setMaxDepthForFinderHierarchy( 2 );
		$instance->tryToFindClosestDescendant( false );

		$instance->setPropertySearchPatternByNamespace(
			array( NS_MAIN => array( 'Bar', 'Yin' ) )
		);

		$instance->tryToFindLinksFor( $subject );

		$this->assertEquals(
			array(
				new DIWikiPage( 'Ichi', NS_MAIN ),
				new DIWikiPage( 'Ni', NS_MAIN ) ),
			$instance->getParents()
		);

		$this->assertEmpty(
			$instance->getChildren()
		);
	}

	public function testCheckCircularReferenceForSomeSubject() {

		$subject = new DIWikiPage( 'Foo', NS_MAIN );

		$store = $this->getMockBuilder( '\SMW\Store' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$store->expects( $this->at( 0 ) )
			->method( 'getPropertyValues' )
			->with(
				$this->equalTo( $subject ),
				$this->equalTo( DIProperty::newFromUserLabel( 'Bar' ) ) )
			->will( $this->returnValue( array( new DIWikiPage( 'Foo', NS_MAIN ) ) ) );

		$instance = new HierarchicalLinksFinderByProperty( $store );

		$instance->setMaxDepthForFinderHierarchy( 2 );
		$instance->tryToFindClosestDescendant( false );

		$instance->setPropertySearchPatternByNamespace(
			array( NS_MAIN => array( 'Bar', 'Yin' ) )
		);

		$instance->tryToFindLinksFor( $subject );

		$this->assertEmpty(
			$instance->getParents()
		);

		$this->assertEmpty(
			$instance->getChildren()
		);
	}

	public function testChildSearchForValidPageTypeProperty() {

		$subject = new DIWikiPage( 'Foo', NS_MAIN );

		$property = DIProperty::newFromUserLabel( 'Bar' );
		$property->setPropertyTypeId( '_wpg' );

		$store = $this->getMockBuilder( '\SMW\Store' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$store->expects( $this->atLeastOnce() )
			->method( 'getPropertyValues' )
			->will( $this->returnValue( array() ) );

		$store->expects( $this->once() )
			->method( 'getPropertySubjects' )
			->with(
				$this->equalTo( $property ),
				$this->equalTo( $subject ) )
			->will( $this->returnValue( array(
				new DIWikiPage( 'Foo', NS_MAIN ),
				new DIWikiPage( 'Ichi', NS_MAIN ) ) ) );

		$instance = new HierarchicalLinksFinderByProperty( $store );

		$instance->setMaxDepthForFinderHierarchy( 2 );
		$instance->tryToFindClosestDescendant( true );

		$instance->setPropertySearchPatternByNamespace(
			array( NS_MAIN => array( 'Bar' ) )
		);

		$instance->tryToFindLinksFor( $subject );

		$this->assertEmpty(
			$instance->getParents()
		);

		$this->assertEquals(
			array(
				new DIWikiPage( 'Ichi', NS_MAIN ) ),
			$instance->getChildren()
		);
	}

}
