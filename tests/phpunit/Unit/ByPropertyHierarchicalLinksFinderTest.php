<?php

namespace SBL\Tests;

use SBL\ByPropertyHierarchicalLinksFinder;

use SMW\DIWikiPage;
use SMW\DIProperty;
use SMW\ApplicationFactory;

use Title;

/**
 * @covers \SBL\ByPropertyHierarchicalLinksFinder
 *
 * @group semantic-breadcrumb-links
 *
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class ByPropertyHierarchicalLinksFinderTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {

		$store = $this->getMockBuilder( '\SMW\Store' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$this->assertInstanceOf(
			'\SBL\ByPropertyHierarchicalLinksFinder',
			new ByPropertyHierarchicalLinksFinder( $store )
		);
	}

	public function testNoValidConfigurationForAnyNamespace() {

		$store = $this->getMockBuilder( '\SMW\Store' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$instance = new ByPropertyHierarchicalLinksFinder( $store );

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

		$instance = new ByPropertyHierarchicalLinksFinder( $store );

		$instance->setFindClosestDescendantState( false );
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

		$instance = new ByPropertyHierarchicalLinksFinder( $store );

		$instance->setFindClosestDescendantState( false );

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
			->will( $this->returnValue( array( $subject ) ) );

		$instance = new ByPropertyHierarchicalLinksFinder( $store );

		$instance->setFindClosestDescendantState( false );

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

		$store->expects( $this->at( 1 ) )
			->method( 'getPropertySubjects' )
			->with(
				$this->equalTo( $property ),
				$this->equalTo( $subject ) )
			->will( $this->returnValue( array(
				new DIWikiPage( 'Foo', NS_MAIN ),
				new DIWikiPage( 'Ichi', NS_MAIN ),
				new DIWikiPage( 'NotBeSelectable', NS_MAIN ) ) ) );

		$instance = new ByPropertyHierarchicalLinksFinder( $store );

		$instance->setFindClosestDescendantState( true );

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

	public function testChildSearchForInvalidPropertyType() {

		$subject = new DIWikiPage( 'Foo', NS_MAIN );

		$store = $this->getMockBuilder( '\SMW\Store' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$store->expects( $this->atLeastOnce() )
			->method( 'getPropertyValues' )
			->will( $this->returnValue( array() ) );

		$store->expects( $this->never() )
			->method( 'getPropertySubjects' );

		$instance = new ByPropertyHierarchicalLinksFinder( $store );

		$instance->setFindClosestDescendantState( true );

		$instance->setPropertySearchPatternByNamespace(
			array( NS_MAIN => array( '_MDAT' ) )
		);

		$instance->tryToFindLinksFor( $subject );

		$this->assertEmpty(
			$instance->getParents()
		);

		$this->assertEmpty(
			$instance->getChildren()
		);
	}

}
