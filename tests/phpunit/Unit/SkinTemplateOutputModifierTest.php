<?php

namespace SBL\Tests;

use SBL\SkinTemplateOutputModifier;

use Title;

/**
 * @covers \SBL\SkinTemplateOutputModifier
 *
 * @group semantic-breadcrumb-links
 *
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class SkinTemplateOutputModifierTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {

		$htmlBreadcrumbLinksBuilder = $this->getMockBuilder( '\SBL\HtmlBreadcrumbLinksBuilder' )
			->disableOriginalConstructor()
			->getMock();

		$this->assertInstanceOf(
			'\SBL\SkinTemplateOutputModifier',
			new SkinTemplateOutputModifier( $htmlBreadcrumbLinksBuilder )
		);
	}

	public function testForUnknownTitle() {

		$htmlBreadcrumbLinksBuilder = $this->getMockBuilder( '\SBL\HtmlBreadcrumbLinksBuilder' )
			->disableOriginalConstructor()
			->getMock();

		$title = $this->getMockBuilder( '\Title' )
			->disableOriginalConstructor()
			->getMock();

		$title->expects( $this->once() )
			->method( 'isKnown' )
			->will( $this->returnValue( false ) );

		$output = $this->getMockBuilder( '\OutputPage' )
			->disableOriginalConstructor()
			->getMock();

		$output->expects( $this->never() )
			->method( 'prependHTML' );

		$output->expects( $this->once() )
			->method( 'getTitle' )
			->will( $this->returnValue( $title ) );

		$instance = new SkinTemplateOutputModifier( $htmlBreadcrumbLinksBuilder );

		$this->assertTrue(
			$instance->modifyOutput( $output )
		);
	}

	public function testForIsSpecialPage() {

		$htmlBreadcrumbLinksBuilder = $this->getMockBuilder( '\SBL\HtmlBreadcrumbLinksBuilder' )
			->disableOriginalConstructor()
			->getMock();

		$title = $this->getMockBuilder( '\Title' )
			->disableOriginalConstructor()
			->getMock();

		$title->expects( $this->once() )
			->method( 'isKnown' )
			->will( $this->returnValue( true ) );

		$title->expects( $this->once() )
			->method( 'isSpecialPage' )
			->will( $this->returnValue( true ) );

		$output = $this->getMockBuilder( '\OutputPage' )
			->disableOriginalConstructor()
			->getMock();

		$output->expects( $this->never() )
			->method( 'prependHTML' );

		$output->expects( $this->atLeastOnce() )
			->method( 'getTitle' )
			->will( $this->returnValue( $title ) );

		$instance = new SkinTemplateOutputModifier( $htmlBreadcrumbLinksBuilder );

		$this->assertTrue(
			$instance->modifyOutput( $output )
		);
	}

	public function testTryPrependHtmlForNonViewAction() {

		$context = new \RequestContext();
		$context->setRequest( new \FauxRequest( array( 'action' => 'edit' ), true ) );
		$context->setTitle( Title::newFromText( __METHOD__ ) );

		$htmlBreadcrumbLinksBuilder = $this->getMockBuilder( '\SBL\HtmlBreadcrumbLinksBuilder' )
			->disableOriginalConstructor()
			->getMock();

		$title = $this->getMockBuilder( '\Title' )
			->disableOriginalConstructor()
			->getMock();

		$title->expects( $this->once() )
			->method( 'isKnown' )
			->will( $this->returnValue( true ) );

		$title->expects( $this->once() )
			->method( 'isSpecialPage' )
			->will( $this->returnValue( false ) );

		$output = $this->getMockBuilder( '\OutputPage' )
			->disableOriginalConstructor()
			->getMock();

		$output->expects( $this->never() )
			->method( 'prependHTML' );

		$output->expects( $this->once() )
			->method( 'getContext' )
			->will( $this->returnValue( $context ) );

		$output->expects( $this->atLeastOnce() )
			->method( 'getTitle' )
			->will( $this->returnValue( $title ) );

		$instance = new SkinTemplateOutputModifier( $htmlBreadcrumbLinksBuilder );

		$this->assertTrue(
			$instance->modifyOutput( $output )
		);
	}

	public function testPrependHtmlForViewActionOnly() {

		$context = new \RequestContext();
		$context->setRequest( new \FauxRequest( array( 'action' => 'view'  ), true ) );

		$htmlBreadcrumbLinksBuilder = $this->getMockBuilder( '\SBL\HtmlBreadcrumbLinksBuilder' )
			->disableOriginalConstructor()
			->getMock();

		$htmlBreadcrumbLinksBuilder->expects( $this->once() )
			->method( 'buildBreadcrumbs' )
			->will( $this->returnSelf() );

		$language = $this->getMockBuilder( '\Language' )
			->disableOriginalConstructor()
			->getMock();

		$title = $this->getMockBuilder( '\Title' )
			->disableOriginalConstructor()
			->getMock();

		$title->expects( $this->once() )
			->method( 'isKnown' )
			->will( $this->returnValue( true ) );

		$title->expects( $this->once() )
			->method( 'isSpecialPage' )
			->will( $this->returnValue( false ) );

		$title->expects( $this->once() )
			->method( 'getPageLanguage' )
			->will( $this->returnValue( $language ) );

		$output = $this->getMockBuilder( '\OutputPage' )
			->disableOriginalConstructor()
			->getMock();

		$output->expects( $this->once() )
			->method( 'prependHTML' );

		$output->expects( $this->once() )
			->method( 'getContext' )
			->will( $this->returnValue( $context ) );

		$output->expects( $this->atLeastOnce() )
			->method( 'getTitle' )
			->will( $this->returnValue( $title ) );

		$instance = new SkinTemplateOutputModifier( $htmlBreadcrumbLinksBuilder );

		$this->assertTrue(
			$instance->modifyOutput( $output )
		);

		$template = new \stdClass;
		$template->data = array();

		$instance->modifyTemplate( $template );

		$this->assertEmpty(
			$template->data['subtitle']
		);
	}

}
