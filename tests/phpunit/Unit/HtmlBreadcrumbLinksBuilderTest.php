<?php

namespace SBL\Tests;

use MediaWiki\Linker\Linker;
use MediaWiki\Title\Title;
use SBL\HtmlBreadcrumbLinksBuilder;
use SMW\DataItems\WikiPage;

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

		$linker = $this->getMockBuilder( Linker::class )
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
			->willReturn( [ new WikiPage( 'Foo', NS_MAIN ) ] );

		$byPropertyHierarchicalLinksFinder->expects( $this->once() )
			->method( 'getChildren' )
			->willReturn( [
				new WikiPage( 'Bar', NS_MAIN ),
				new WikiPage( 'Foobar', NS_MAIN ) ] );

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

		$this->assertStringContainsString(
			'dir="ltr"',
			$instance->getHtml()
		);

		$this->assertStringContainsString(
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
			->willReturn( [ new WikiPage( 'Foo', NS_MAIN ) ] );

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
		$subject = new WikiPage( __METHOD__, NS_MAIN, '', '' );

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
		$subject = new WikiPage( __METHOD__, NS_MAIN, '', '' );

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

		$dataValue = $this->getMockBuilder( '\SMW\DataValues\WikiPageValue' )
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

		$title = $this->getMockBuilder( Title::class )
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
