<?php

namespace SBL\Tests;

use SBL\ByPropertyHierarchicalLinksFinder;
use SMW\DIProperty;
use SMW\DIWikiPage;

/**
 * @covers \SBL\ByPropertyHierarchicalLinksFinder
 * @group semantic-breadcrumb-links
 *
 * @license GPL-2.0-or-later
 * @since 1.0
 *
 * @author mwjames
 */
class ByPropertyHierarchicalLinksFinderTest extends \PHPUnit\Framework\TestCase {

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
				$subject,
				$property )
			->willReturn( [] );

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
			->onlyMethods( [ 'getRedirectTarget' ] )
			->getMockForAbstractClass();

		$store->expects( $this->at( 0 ) )
			->method( 'getPropertyValues' )
			->with(
				$subject,
				DIProperty::newFromUserLabel( 'Bar' ) )
			->willReturn( [ new DIWikiPage( 'Ichi', NS_MAIN ) ] );

		$store->expects( $this->at( 1 ) )
			->method( 'getRedirectTarget' )
			->with(
				new DIWikiPage( 'Ichi', NS_MAIN ) )
			->willReturn( new DIWikiPage( 'Ichi', NS_MAIN ) );

		$store->expects( $this->at( 2 ) )
			->method( 'getPropertyValues' )
			->with(
				new DIWikiPage( 'Ichi', NS_MAIN ),
				DIProperty::newFromUserLabel( 'Yin' ) )
			->willReturn( [ new DIWikiPage( 'Ni', NS_MAIN ) ] );

		$store->expects( $this->at( 3 ) )
			->method( 'getRedirectTarget' )
			->with(
				new DIWikiPage( 'Ni', NS_MAIN ) )
			->willReturn( new DIWikiPage( 'San', NS_MAIN ) );

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
				$subject,
				DIProperty::newFromUserLabel( 'Bar' ) )
			->willReturn( [ $subject ] );

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
			->willReturn( [] );

		$store->expects( $this->at( 1 ) )
			->method( 'getPropertySubjects' )
			->with(
				$property,
				$subject )
			->willReturn( [
				new DIWikiPage( 'Foo', NS_MAIN ),
				new DIWikiPage( 'NotEqualToFoo', NS_MAIN ),
				new DIWikiPage( 'AnotherChild', NS_MAIN ) ] );

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
			->willReturn( [] );

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
