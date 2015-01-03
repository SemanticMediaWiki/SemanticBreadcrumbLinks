<?php

namespace SBL\Tests;

use SBL\HtmlBreadcrumbLinksBuilder;

use SMW\DIWikiPage;

use Title;

/**
 * @covers \SBL\HtmlBreadcrumbLinksBuilder
 *
 * @group semantic-breadcrumb-links
 *
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class HtmlBreadcrumbLinksBuilderTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {

		$hierarchicalLinksFinderByProperty = $this->getMockBuilder( '\SBL\HierarchicalLinksFinderByProperty' )
			->disableOriginalConstructor()
			->getMock();

		$subpageLinksFinder = $this->getMockBuilder( '\SBL\SubpageLinksFinder' )
			->disableOriginalConstructor()
			->getMock();

		$this->assertInstanceOf(
			'\SBL\HtmlBreadcrumbLinksBuilder',
			new HtmlBreadcrumbLinksBuilder( $hierarchicalLinksFinderByProperty, $subpageLinksFinder )
		);
	}

	public function testHetHtmlForEmptyContent() {

		$hierarchicalLinksFinderByProperty = $this->getMockBuilder( '\SBL\HierarchicalLinksFinderByProperty' )
			->disableOriginalConstructor()
			->getMock();

		$subpageLinksFinder = $this->getMockBuilder( '\SBL\SubpageLinksFinder' )
			->disableOriginalConstructor()
			->getMock();

		$dummyLinker = $this->getMockBuilder( '\DummyLinker' )
			->disableOriginalConstructor()
			->getMock();

		$instance = new HtmlBreadcrumbLinksBuilder( $hierarchicalLinksFinderByProperty, $subpageLinksFinder );

		$instance->setBreadcrumbTrailStyleClass( 'Foo' );
		$instance->setLinker( $dummyLinker );

		$this->assertInternalType(
			'string',
			$instance->getHtml()
		);
	}

	public function testBuildBreadcrumbsForValidHierarchicalLinks() {

		$hierarchicalLinksFinderByProperty = $this->getMockBuilder( '\SBL\HierarchicalLinksFinderByProperty' )
			->disableOriginalConstructor()
			->getMock();

		$hierarchicalLinksFinderByProperty->expects( $this->once() )
			->method( 'getParents' )
			->will( $this->returnValue( array( new DIWikiPage( 'Foo', NS_MAIN ) ) ) );

		$hierarchicalLinksFinderByProperty->expects( $this->once() )
			->method( 'getChildren' )
			->will( $this->returnValue( array( new DIWikiPage( 'Bar', NS_MAIN ) ) ) );

		$subpageLinksFinder = $this->getMockBuilder( '\SBL\SubpageLinksFinder' )
			->disableOriginalConstructor()
			->getMock();

		$subpageLinksFinder->expects( $this->never() )
			->method( 'canUseSubpageDiscoveryForFallback' );

		$instance = new HtmlBreadcrumbLinksBuilder(
			$hierarchicalLinksFinderByProperty,
			$subpageLinksFinder
		);

		$instance->buildBreadcrumbs( \Title::newFromText( __METHOD__) );
		$instance->setRTLDirectionalityState( false );

		$this->assertInternalType(
			'string',
			$instance->getHtml()
		);

		$this->assertContains(
			'dir="ltr"',
			$instance->getHtml()
		);
	}

	public function testBuildBreadcrumbsForNoHierarchicalLinksButSubpageFallback() {

		$hierarchicalLinksFinderByProperty = $this->getMockBuilder( '\SBL\HierarchicalLinksFinderByProperty' )
			->disableOriginalConstructor()
			->getMock();

		$hierarchicalLinksFinderByProperty->expects( $this->once() )
			->method( 'getParents' )
			->will( $this->returnValue( array() ) );

		$hierarchicalLinksFinderByProperty->expects( $this->once() )
			->method( 'getChildren' )
			->will( $this->returnValue( array() ) );

		$subpageLinksFinder = $this->getMockBuilder( '\SBL\SubpageLinksFinder' )
			->disableOriginalConstructor()
			->getMock();

		$subpageLinksFinder->expects( $this->once() )
			->method( 'canUseSubpageDiscoveryForFallback' )
			->will( $this->returnValue( true ) );

		$subpageLinksFinder->expects( $this->once() )
			->method( 'getParents' )
			->will( $this->returnValue( array( new DIWikiPage( 'Foo', NS_MAIN ) ) ) );

		$instance = new HtmlBreadcrumbLinksBuilder(
			$hierarchicalLinksFinderByProperty,
			$subpageLinksFinder
		);

		$instance->buildBreadcrumbs( \Title::newFromText( __METHOD__) );

		$this->assertInternalType(
			'string',
			$instance->getHtml()
		);
	}

}
