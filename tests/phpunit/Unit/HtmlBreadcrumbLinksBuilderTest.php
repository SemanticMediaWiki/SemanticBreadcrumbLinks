<?php

namespace SBL\Tests;

use SBL\HtmlBreadcrumbLinksBuilder;
use SMW\DIWikiPage;
use SMW\Tests\PHPUnitCompat;
use Title;

/**
 * @covers \SBL\HtmlBreadcrumbLinksBuilder
 * @group semantic-breadcrumb-links
 *
 * @license GPL-2.0-or-later
 * @since 1.0
 *
 * @author mwjames
 */
class HtmlBreadcrumbLinksBuilderTest extends \PHPUnit\Framework\TestCase {

	use PHPUnitCompat;

	public function testCanConstruct() {
		$byPropertyHierarchicalLinksFinder = $this->getMockBuilder( '\SBL\ByPropertyHierarchicalLinksFinder' )
			->disableOriginalConstructor()
			->getMock();

		$bySubpageLinksFinder = $this->getMockBuilder( '\SBL\BySubpageLinksFinder' )
			->disableOriginalConstructor()
			->getMock();

		$this->assertInstanceOf(
			'\SBL\HtmlBreadcrumbLinksBuilder',
			new HtmlBreadcrumbLinksBuilder( $byPropertyHierarchicalLinksFinder, $bySubpageLinksFinder )
		);
	}

	public function testHetHtmlForEmptyContent() {
		$byPropertyHierarchicalLinksFinder = $this->getMockBuilder( '\SBL\ByPropertyHierarchicalLinksFinder' )
			->disableOriginalConstructor()
			->getMock();

		$bySubpageLinksFinder = $this->getMockBuilder( '\SBL\BySubpageLinksFinder' )
			->disableOriginalConstructor()
			->getMock();

		$linker = $this->getMockBuilder( '\Linker' )
			->disableOriginalConstructor()
			->getMock();

		$instance = new HtmlBreadcrumbLinksBuilder(
			$byPropertyHierarchicalLinksFinder,
			$bySubpageLinksFinder
		);

		$instance->setBreadcrumbTrailStyleClass( 'Foo' );
		$instance->setLinker( $linker );
		$instance->hideSubpageParent( true );

		$this->assertIsString(

			$instance->getHtml()
		);
	}

	public function testBuildBreadcrumbsForValidHierarchicalLinks() {
		$byPropertyHierarchicalLinksFinder = $this->getMockBuilder( '\SBL\ByPropertyHierarchicalLinksFinder' )
			->disableOriginalConstructor()
			->getMock();

		$byPropertyHierarchicalLinksFinder->expects( $this->once() )
			->method( 'getParents' )
			->willReturn( [ new DIWikiPage( 'Foo', NS_MAIN ) ] );

		$byPropertyHierarchicalLinksFinder->expects( $this->once() )
			->method( 'getChildren' )
			->willReturn( [
				new DIWikiPage( 'Bar', NS_MAIN ),
				new DIWikiPage( 'Foobar', NS_MAIN ) ] );

		$bySubpageLinksFinder = $this->getMockBuilder( '\SBL\BySubpageLinksFinder' )
			->disableOriginalConstructor()
			->getMock();

		$bySubpageLinksFinder->expects( $this->never() )
			->method( 'isDiscoveryFallback' );

		$instance = new HtmlBreadcrumbLinksBuilder(
			$byPropertyHierarchicalLinksFinder,
			$bySubpageLinksFinder
		);

		$instance->isRTL( false );
		$instance->setBreadcrumbDividerStyleClass( 'DividerStyleClass' );

		$instance->buildBreadcrumbs( Title::newFromText( __METHOD__ ) );

		$this->assertIsString(

			$instance->getHtml()
		);

		$this->assertContains(
			'dir="ltr"',
			$instance->getHtml()
		);

		$this->assertContains(
			'DividerStyleClass',
			$instance->getHtml()
		);
	}

	public function testBuildBreadcrumbsForNoHierarchicalLinksButSubpageFallback() {
		$byPropertyHierarchicalLinksFinder = $this->getMockBuilder( '\SBL\ByPropertyHierarchicalLinksFinder' )
			->disableOriginalConstructor()
			->getMock();

		$byPropertyHierarchicalLinksFinder->expects( $this->once() )
			->method( 'getParents' )
			->willReturn( [] );

		$byPropertyHierarchicalLinksFinder->expects( $this->once() )
			->method( 'getChildren' )
			->willReturn( [] );

		$bySubpageLinksFinder = $this->getMockBuilder( '\SBL\BySubpageLinksFinder' )
			->disableOriginalConstructor()
			->getMock();

		$bySubpageLinksFinder->expects( $this->once() )
			->method( 'isDiscoveryFallback' )
			->willReturn( true );

		$bySubpageLinksFinder->expects( $this->once() )
			->method( 'getParents' )
			->willReturn( [ new DIWikiPage( 'Foo', NS_MAIN ) ] );

		$instance = new HtmlBreadcrumbLinksBuilder(
			$byPropertyHierarchicalLinksFinder,
			$bySubpageLinksFinder
		);

		$instance->buildBreadcrumbs( Title::newFromText( __METHOD__ ) );

		$this->assertIsString(

			$instance->getHtml()
		);
	}

	/**
	 * Test to ensure that no subobject is assigned from a Title that contains
	 * a fragment
	 */
	public function testBuildBreadcrumbsToNeverUseFragmentedPartOfTitle() {
		$subject = new DIWikiPage( __METHOD__, NS_MAIN, '', '' );

		$byPropertyHierarchicalLinksFinder = $this->getMockBuilder( '\SBL\ByPropertyHierarchicalLinksFinder' )
			->disableOriginalConstructor()
			->getMock();

		$byPropertyHierarchicalLinksFinder->expects( $this->once() )
			->method( 'findLinksBySubject' )
			->with( $subject );

		$byPropertyHierarchicalLinksFinder->expects( $this->once() )
			->method( 'getParents' )
			->willReturn( [] );

		$byPropertyHierarchicalLinksFinder->expects( $this->once() )
			->method( 'getChildren' )
			->willReturn( [] );

		$bySubpageLinksFinder = $this->getMockBuilder( '\SBL\BySubpageLinksFinder' )
			->disableOriginalConstructor()
			->getMock();

		$instance = new HtmlBreadcrumbLinksBuilder(
			$byPropertyHierarchicalLinksFinder,
			$bySubpageLinksFinder
		);

		$title = Title::newFromText( __METHOD__ );
		$title->setFragment( 'Foo' );

		$instance->buildBreadcrumbs( $title );
	}

	public function testBuildBreadcrumbsToUseDisplayTitle() {
		$subject = new DIWikiPage( __METHOD__, NS_MAIN, '', '' );

		$byPropertyHierarchicalLinksFinder = $this->getMockBuilder( '\SBL\ByPropertyHierarchicalLinksFinder' )
			->disableOriginalConstructor()
			->getMock();

		$byPropertyHierarchicalLinksFinder->expects( $this->once() )
			->method( 'findLinksBySubject' )
			->with( $subject );

		$byPropertyHierarchicalLinksFinder->expects( $this->once() )
			->method( 'getParents' )
			->willReturn( [ $subject ] );

		$byPropertyHierarchicalLinksFinder->expects( $this->once() )
			->method( 'getChildren' )
			->willReturn( [] );

		$bySubpageLinksFinder = $this->getMockBuilder( '\SBL\BySubpageLinksFinder' )
			->disableOriginalConstructor()
			->getMock();

		$dataValue = $this->getMockBuilder( '\SMWWikiPageValue' )
			->disableOriginalConstructor()
			->getMock();

		$dataValue->expects( $this->atLeastOnce() )
			->method( 'getDisplayTitle' );

		$dataValueFactory = $this->getMockBuilder( '\SMW\DataValueFactory' )
			->disableOriginalConstructor()
			->getMock();

		$dataValueFactory->expects( $this->atLeastOnce() )
			->method( 'newDataValueByItem' )
			->willReturn( $dataValue );

		$instance = new HtmlBreadcrumbLinksBuilder(
			$byPropertyHierarchicalLinksFinder,
			$bySubpageLinksFinder
		);

		$title = Title::newFromText( __METHOD__ );

		$instance->setDataValueFactory( $dataValueFactory );
		$instance->buildBreadcrumbs( $title );
	}

	public function testRedirectDoesNotTryToFindBreadcrumbs() {
		$byPropertyHierarchicalLinksFinder = $this->getMockBuilder( '\SBL\ByPropertyHierarchicalLinksFinder' )
			->disableOriginalConstructor()
			->getMock();

		$byPropertyHierarchicalLinksFinder->expects( $this->never() )
			->method( 'findLinksBySubject' );

		$bySubpageLinksFinder = $this->getMockBuilder( '\SBL\BySubpageLinksFinder' )
			->disableOriginalConstructor()
			->getMock();

		$title = $this->getMockBuilder( '\Title' )
			->disableOriginalConstructor()
			->getMock();

		$title->expects( $this->once() )
			->method( 'isRedirect' )
			->willReturn( true );

		$instance = new HtmlBreadcrumbLinksBuilder(
			$byPropertyHierarchicalLinksFinder,
			$bySubpageLinksFinder
		);

		$instance->buildBreadcrumbs( $title );
	}

}
