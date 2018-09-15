<?php

namespace SBL\Tests;

use SBL\SkinTemplateOutputModifier;

use Title;

/**
 * @covers \SBL\SkinTemplateOutputModifier
 * @group semantic-breadcrumb-links
 *
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class SkinTemplateOutputModifierTest extends \PHPUnit_Framework_TestCase {

	private $namespaceExaminer;

	protected function setUp() {

		$this->namespaceExaminer = $this->getMockBuilder( '\SMW\NamespaceExaminer' )
			->disableOriginalConstructor()
			->getMock();
	}

	public function testCanConstruct() {

		$htmlBreadcrumbLinksBuilder = $this->getMockBuilder( '\SBL\HtmlBreadcrumbLinksBuilder' )
			->disableOriginalConstructor()
			->getMock();

		$this->assertInstanceOf(
			SkinTemplateOutputModifier::class,
			new SkinTemplateOutputModifier( $htmlBreadcrumbLinksBuilder, $this->namespaceExaminer )
		);
	}

	public function testTryPrependHtmlOnUnknownTitle() {

		$this->namespaceExaminer->expects( $this->never() )
			->method( 'isSemanticEnabled' )
			->will( $this->returnValue( true ) );

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

		$output->expects( $this->atLeastOnce() )
			->method( 'getTitle' )
			->will( $this->returnValue( $title ) );

		$instance = new SkinTemplateOutputModifier(
			$htmlBreadcrumbLinksBuilder,
			$this->namespaceExaminer
		);

		$template = new \stdClass;
		$template->data = [];

		$instance->modify( $output, $template );
	}

	public function testTryPrependHtmlOnSpecialPage() {

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

		$output->expects( $this->atLeastOnce() )
			->method( 'getTitle' )
			->will( $this->returnValue( $title ) );

		$instance = new SkinTemplateOutputModifier(
			$htmlBreadcrumbLinksBuilder,
			$this->namespaceExaminer
		);

		$template = new \stdClass;
		$template->data = [];

		$instance->modify( $output, $template );
	}

	public function PrependHtmlOnNonViewAction() {

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
			->method( 'getNamespace' )
			->will( $this->returnValue( NS_MAIN ) );

		$title->expects( $this->once() )
			->method( 'isSpecialPage' )
			->will( $this->returnValue( false ) );

		$output = $this->getMockBuilder( '\OutputPage' )
			->disableOriginalConstructor()
			->getMock();

		$output->expects( $this->atLeastOnce() )
			->method( 'getTitle' )
			->will( $this->returnValue( $title ) );

		$instance = new SkinTemplateOutputModifier(
			$htmlBreadcrumbLinksBuilder,
			$this->namespaceExaminer
		);

		$template = new \stdClass;
		$template->data = [];

		$instance->modify( $output, $template );
	}

	public function testTryPrependHtmlOnNOBREADCRUMBLINKS() {

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
			->method( 'getNamespace' )
			->will( $this->returnValue( NS_MAIN ) );

		$title->expects( $this->once() )
			->method( 'isSpecialPage' )
			->will( $this->returnValue( false ) );

		$output = $this->getMockBuilder( '\OutputPage' )
			->disableOriginalConstructor()
			->getMock();

		$output->expects( $this->atLeastOnce() )
			->method( 'getTitle' )
			->will( $this->returnValue( $title ) );

		$instance = new SkinTemplateOutputModifier(
			$htmlBreadcrumbLinksBuilder,
			$this->namespaceExaminer
		);

		$output->smwmagicwords = [ 'SBL_NOBREADCRUMBLINKS' ];

		$template = new \stdClass;
		$template->data = [];

		$instance->modify( $output, $template );
	}

	public function testAppendHtml() {

		$this->namespaceExaminer->expects( $this->once() )
			->method( 'isSemanticEnabled' )
			->will( $this->returnValue( true ) );

		$htmlBreadcrumbLinksBuilder = $this->getMockBuilder( '\SBL\HtmlBreadcrumbLinksBuilder' )
			->disableOriginalConstructor()
			->getMock();

		$htmlBreadcrumbLinksBuilder->expects( $this->once() )
			->method( 'buildBreadcrumbs' )
			->will( $this->returnSelf() );

		$htmlBreadcrumbLinksBuilder->expects( $this->once() )
			->method( 'getHtml' )
			->will( $this->returnValue( 'bar' ) );

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
			->method( 'getNamespace' )
			->will( $this->returnValue( NS_MAIN ) );

		$title->expects( $this->once() )
			->method( 'isSpecialPage' )
			->will( $this->returnValue( false ) );

		$title->expects( $this->once() )
			->method( 'getPageLanguage' )
			->will( $this->returnValue( $language ) );

		$output = $this->getMockBuilder( '\OutputPage' )
			->disableOriginalConstructor()
			->getMock();

		$output->expects( $this->atLeastOnce() )
			->method( 'getTitle' )
			->will( $this->returnValue( $title ) );

		$instance = new SkinTemplateOutputModifier(
			$htmlBreadcrumbLinksBuilder,
			$this->namespaceExaminer
		);

		$template = new \stdClass;

		$template->data = [
			'subtitle' => 'Foo'
		];

		$instance->modify( $output, $template );

		$this->assertEquals(
			'Foobar',
			$template->data['subtitle']
		);
	}

}
