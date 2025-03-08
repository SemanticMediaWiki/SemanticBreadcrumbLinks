<?php

namespace SBL\Tests;

use SBL\SkinTemplateOutputModifier;

/**
 * @covers \SBL\SkinTemplateOutputModifier
 * @group semantic-breadcrumb-links
 *
 * @license GPL-2.0-or-later
 * @since 1.0
 *
 * @author mwjames
 */
class SkinTemplateOutputModifierTest extends \PHPUnit\Framework\TestCase {

	private $namespaceExaminer;

	protected function setUp(): void {
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
			->willReturn( true );

		$htmlBreadcrumbLinksBuilder = $this->getMockBuilder( '\SBL\HtmlBreadcrumbLinksBuilder' )
			->disableOriginalConstructor()
			->getMock();

		$title = $this->getMockBuilder( '\Title' )
			->disableOriginalConstructor()
			->getMock();

		$title->expects( $this->once() )
			->method( 'isKnown' )
			->willReturn( false );

		$output = $this->getMockBuilder( '\OutputPage' )
			->disableOriginalConstructor()
			->getMock();

		$output->expects( $this->never() )
			->method( 'prependHTML' );

		$output->expects( $this->atLeastOnce() )
			->method( 'getTitle' )
			->willReturn( $title );

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
			->willReturn( true );

		$title->expects( $this->once() )
			->method( 'isSpecialPage' )
			->willReturn( true );

		$output = $this->getMockBuilder( '\OutputPage' )
			->disableOriginalConstructor()
			->getMock();

		$output->expects( $this->atLeastOnce() )
			->method( 'getTitle' )
			->willReturn( $title );

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
			->willReturn( true );

		$title->expects( $this->once() )
			->method( 'getNamespace' )
			->willReturn( NS_MAIN );

		$title->expects( $this->once() )
			->method( 'isSpecialPage' )
			->willReturn( false );

		$output = $this->getMockBuilder( '\OutputPage' )
			->disableOriginalConstructor()
			->getMock();

		$output->expects( $this->atLeastOnce() )
			->method( 'getTitle' )
			->willReturn( $title );

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
			->willReturn( true );

		$title->expects( $this->once() )
			->method( 'getNamespace' )
			->willReturn( NS_MAIN );

		$title->expects( $this->once() )
			->method( 'isSpecialPage' )
			->willReturn( false );

		$output = $this->getMockBuilder( '\OutputPage' )
			->disableOriginalConstructor()
			->getMock();

		$output->expects( $this->atLeastOnce() )
			->method( 'getTitle' )
			->willReturn( $title );

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
			->willReturn( true );

		$htmlBreadcrumbLinksBuilder = $this->getMockBuilder( '\SBL\HtmlBreadcrumbLinksBuilder' )
			->disableOriginalConstructor()
			->getMock();

		$htmlBreadcrumbLinksBuilder->expects( $this->once() )
			->method( 'buildBreadcrumbs' )
			->willReturnSelf();

		$htmlBreadcrumbLinksBuilder->expects( $this->once() )
			->method( 'getHtml' )
			->willReturn( 'bar' );

		$language = $this->getMockBuilder( '\Language' )
			->disableOriginalConstructor()
			->getMock();

		$title = $this->getMockBuilder( '\Title' )
			->disableOriginalConstructor()
			->getMock();

		$title->expects( $this->once() )
			->method( 'isKnown' )
			->willReturn( true );

		$title->expects( $this->once() )
			->method( 'getNamespace' )
			->willReturn( NS_MAIN );

		$title->expects( $this->once() )
			->method( 'isSpecialPage' )
			->willReturn( false );

		$title->expects( $this->once() )
			->method( 'getPageLanguage' )
			->willReturn( $language );

		$output = $this->getMockBuilder( '\OutputPage' )
			->disableOriginalConstructor()
			->getMock();

		$output->expects( $this->atLeastOnce() )
			->method( 'getTitle' )
			->willReturn( $title );

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
