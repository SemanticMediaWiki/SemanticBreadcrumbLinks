<?php

namespace SBL\Tests;

use SBL\ByPropertyHierarchicalLinksFinder;
use SMW\DataItems\Property;
use SMW\DataItems\WikiPage;

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

		$instance->findLinksBySubject( new WikiPage( 'Foo', NS_MAIN ) );

		$this->assertEmpty(
			$instance->getParents()
		);

		$this->assertEmpty(
			$instance->getChildren()
		);
	}

	public function testEmptyResultByTryingToFindAntecedent() {
		$subject = new WikiPage( 'Foo', NS_MAIN );
		$property = Property::newFromUserLabel( 'Bar' );

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
		$subject = new WikiPage( 'Foo', NS_MAIN );

		$store = $this->getMockBuilder( '\SMW\Store' )
			->disableOriginalConstructor()
			->onlyMethods( [ 'getRedirectTarget' ] )
			->getMockForAbstractClass();

		$store->expects( $this->at( 0 ) )
			->method( 'getPropertyValues' )
			->with(
				$subject,
				Property::newFromUserLabel( 'Bar' ) )
			->willReturn( [ new WikiPage( 'Ichi', NS_MAIN ) ] );

		$store->expects( $this->at( 1 ) )
			->method( 'getRedirectTarget' )
			->with(
				new WikiPage( 'Ichi', NS_MAIN ) )
			->willReturn( new WikiPage( 'Ichi', NS_MAIN ) );

		$store->expects( $this->at( 2 ) )
			->method( 'getPropertyValues' )
			->with(
				new WikiPage( 'Ichi', NS_MAIN ),
				Property::newFromUserLabel( 'Yin' ) )
			->willReturn( [ new WikiPage( 'Ni', NS_MAIN ) ] );

		$store->expects( $this->at( 3 ) )
			->method( 'getRedirectTarget' )
			->with(
				new WikiPage( 'Ni', NS_MAIN ) )
			->willReturn( new WikiPage( 'San', NS_MAIN ) );

		$instance = new ByPropertyHierarchicalLinksFinder( $store );

		$instance->setFindClosestDescendantState( false );

		$instance->setPropertySearchPatternByNamespace(
			[ NS_MAIN => [ 'Bar', 'Yin' ] ]
		);

		$instance->findLinksBySubject( $subject );

		$this->assertEquals(
			[
				new WikiPage( 'Ichi', NS_MAIN ),
				new WikiPage( 'San', NS_MAIN ) ],
			$instance->getParents()
		);

		$this->assertEmpty(
			$instance->getChildren()
		);
	}

	public function testCheckCircularReferenceForSomeSubject() {
		$subject = new WikiPage( 'Foo', NS_MAIN );

		$store = $this->getMockBuilder( '\SMW\Store' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$store->expects( $this->at( 0 ) )
			->method( 'getPropertyValues' )
			->with(
				$subject,
				Property::newFromUserLabel( 'Bar' ) )
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
		$subject = new WikiPage( 'Foo', NS_MAIN );

		$property = Property::newFromUserLabel( 'Bar' );
		$property->setPropertyValueType( '_wpg' );

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
				new WikiPage( 'Foo', NS_MAIN ),
				new WikiPage( 'NotEqualToFoo', NS_MAIN ),
				new WikiPage( 'AnotherChild', NS_MAIN ) ] );

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
				new WikiPage( 'NotEqualToFoo', NS_MAIN ),
				new WikiPage( 'AnotherChild', NS_MAIN ) ],
			$instance->getChildren()
		);
	}

	public function testChildSearchForInvalidPropertyType() {
		$subject = new WikiPage( 'Foo', NS_MAIN );

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
