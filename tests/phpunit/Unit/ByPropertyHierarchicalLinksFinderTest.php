<?php

namespace SBL\Tests;

use SBL\ByPropertyHierarchicalLinksFinder;
use SMW\DIWikiPage;
use SMW\DIProperty;
use SMW\ApplicationFactory;
use Title;

/**
 * @covers \SBL\ByPropertyHierarchicalLinksFinder
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

		$instance->findLinksBySubject( new DIWikiPage( 'Foo', NS_MAIN ) );

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
			->will( $this->returnValue( [] ) );

		$instance = new ByPropertyHierarchicalLinksFinder( $store );

		$instance->setFindClosestDescendantState( false );
		$instance->setPropertySearchPatternByNamespace(
			[ NS_MAIN => [ 'Bar' ] ]
		);

		$instance->findLinksBySubject( $subject );

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
			->setMethods( [ 'getRedirectTarget' ] )
			->getMockForAbstractClass();

		$store->expects( $this->at( 0 ) )
			->method( 'getPropertyValues' )
			->with(
				$this->equalTo( $subject ),
				$this->equalTo( DIProperty::newFromUserLabel( 'Bar' ) ) )
			->will( $this->returnValue( [ new DIWikiPage( 'Ichi', NS_MAIN ) ] ) );

		$store->expects( $this->at( 1 ) )
			->method( 'getRedirectTarget' )
			->with(
				$this->equalTo( new DIWikiPage( 'Ichi', NS_MAIN ) ) )
			->will( $this->returnValue( new DIWikiPage( 'Ichi', NS_MAIN ) ) );

		$store->expects( $this->at( 2 ) )
			->method( 'getPropertyValues' )
			->with(
				$this->equalTo( new DIWikiPage( 'Ichi', NS_MAIN )  ),
				$this->equalTo( DIProperty::newFromUserLabel( 'Yin' )) )
			->will( $this->returnValue( [ new DIWikiPage( 'Ni', NS_MAIN ) ] ) );

		$store->expects( $this->at( 3 ) )
			->method( 'getRedirectTarget' )
			->with(
				$this->equalTo( new DIWikiPage( 'Ni', NS_MAIN ) ) )
			->will( $this->returnValue( new DIWikiPage( 'San', NS_MAIN ) ) );

		$instance = new ByPropertyHierarchicalLinksFinder( $store );

		$instance->setFindClosestDescendantState( false );

		$instance->setPropertySearchPatternByNamespace(
			[ NS_MAIN => [ 'Bar', 'Yin' ] ]
		);

		$instance->findLinksBySubject( $subject );

		$this->assertEquals(
			[
				new DIWikiPage( 'Ichi', NS_MAIN ),
				new DIWikiPage( 'San', NS_MAIN ) ],
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
			->will( $this->returnValue( [ $subject ] ) );

		$instance = new ByPropertyHierarchicalLinksFinder( $store );

		$instance->setFindClosestDescendantState( false );

		$instance->setPropertySearchPatternByNamespace(
			[ NS_MAIN => [ 'Bar', 'Yin' ] ]
		);

		$instance->findLinksBySubject( $subject );

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
			->will( $this->returnValue( [] ) );

		$store->expects( $this->at( 1 ) )
			->method( 'getPropertySubjects' )
			->with(
				$this->equalTo( $property ),
				$this->equalTo( $subject ) )
			->will( $this->returnValue( [
				new DIWikiPage( 'Foo', NS_MAIN ),
				new DIWikiPage( 'NotEqualToFoo', NS_MAIN ),
				new DIWikiPage( 'AnotherChild', NS_MAIN ) ] ) );

		$instance = new ByPropertyHierarchicalLinksFinder( $store );

		$instance->setFindClosestDescendantState( true );

		$instance->setPropertySearchPatternByNamespace(
			[ NS_MAIN => [ 'Bar' ] ]
		);

		$instance->findLinksBySubject( $subject );

		$this->assertEmpty(
			$instance->getParents()
		);

		$this->assertEquals(
			[
				new DIWikiPage( 'NotEqualToFoo', NS_MAIN ),
				new DIWikiPage( 'AnotherChild', NS_MAIN ) ],
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
			->will( $this->returnValue( [] ) );

		$store->expects( $this->never() )
			->method( 'getPropertySubjects' );

		$instance = new ByPropertyHierarchicalLinksFinder( $store );

		$instance->setFindClosestDescendantState( true );

		$instance->setPropertySearchPatternByNamespace(
			[ NS_MAIN => [ '_MDAT' ] ]
		);

		$instance->findLinksBySubject( $subject );

		$this->assertEmpty(
			$instance->getParents()
		);

		$this->assertEmpty(
			$instance->getChildren()
		);
	}

}
