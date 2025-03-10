<?php

namespace SBL\Tests;

use SBL\HookRegistry;
use SBL\Options;
use Title;

/**
 * @covers \SBL\HookRegistry
 * @group semantic-breadcrumb-links
 *
 * @license GPL-2.0-or-later
 * @since 1.0
 *
 * @author mwjames
 */
class HookRegistryTest extends \PHPUnit\Framework\TestCase {

	public function testCanConstruct() {
		$store = $this->getMockBuilder( '\SMW\Store' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$options = $this->getMockBuilder( '\SBL\Options' )
			->disableOriginalConstructor()
			->getMock();

		$this->assertInstanceOf(
			'\SBL\HookRegistry',
			new HookRegistry( $store, new $options )
		);
	}

	public function testRegister() {
		$title = Title::newFromText( __METHOD__ );

		$outputPage = $this->getMockBuilder( '\OutputPage' )
			->disableOriginalConstructor()
			->getMock();

		$outputPage->expects( $this->any() )
			->method( 'getTitle' )
			->willReturn( $title );

		$skin = $this->getMockBuilder( '\Skin' )
			->disableOriginalConstructor()
			->getMock();

		$skin->expects( $this->any() )
			->method( 'getOutput' )
			->willReturn( $outputPage );

		$store = $this->getMockBuilder( '\SMW\Store' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$configuration = [
			'useSubpageFinderFallback' => false,
			'tryToFindClosestDescendant' => false,
			'propertySearchPatternByNamespace' => [],
			'breadcrumbTrailStyleClass' => 'foo',
			'breadcrumbDividerStyleClass' => 'bar',
			'hideSubpageParent' => true,
			'enabledSubpageParentAnnotation' => true,
			'disableTranslationSubpageAnnotation' => false,
			'wgNamespacesWithSubpages' => []
		];

		$instance = new HookRegistry(
			$store,
			new Options( $configuration )
		);

		$instance->register();

		$this->doTestInitProperties( $instance );
		$this->doTestSkinSubPageSubtitle( $instance, $skin );
		$this->doTestBeforePageDisplay( $instance, $outputPage, $skin );
		$this->doTestParserAfterTidy( $instance );
		$this->doTestParserAfterTidyToBailOutEarly( $instance );
		$this->doTestSmwParserBeforeMagicWordsFinder( $instance );
		$this->doTestOutputPageParserOutput( $instance, $outputPage );
	}

	private function doTestInitProperties( $instance ) {
		$handler = 'SMW::Property::initProperties';

		$propertyRegistry = $this->getMockBuilder( '\SMW\PropertyRegistry' )
			->disableOriginalConstructor()
			->getMock();

		$this->assertTrue(
			$instance->isRegistered( $handler )
		);

		$this->assertThatHookIsExcutable(
			$instance->getHandlerFor( $handler ),
			[ $propertyRegistry ]
		);
	}

	private function doTestSkinSubPageSubtitle( $instance, $skin ) {
		$handler = 'SkinSubPageSubtitle';

		$this->assertTrue(
			$instance->isRegistered( $handler )
		);

		$subpages = '';

		$this->assertThatHookIsExcutable(
			$instance->getHandlerFor( $handler ),
			[ &$subpages, $skin ]
		);
	}

	private function doTestBeforePageDisplay( $instance, $outputPage, $skin ) {
		$handler = 'BeforePageDisplay';

		$this->assertTrue(
			$instance->isRegistered( $handler )
		);

		$this->assertThatHookIsExcutable(
			$instance->getHandlerFor( $handler ),
			[ &$outputPage, &$skin ]
		);
	}

	private function doTestParserAfterTidy( $instance ) {
		$handler = 'ParserAfterTidy';

		$this->assertTrue(
			$instance->isRegistered( $handler )
		);

		$title = Title::newFromText( __METHOD__ );

		$parserOptions = $this->getMockBuilder( '\ParserOptions' )
			->disableOriginalConstructor()
			->getMock();

		$parserOutput = $this->getMockBuilder( '\ParserOutput' )
			->disableOriginalConstructor()
			->getMock();

		$parser = $this->getMockBuilder( '\Parser' )
			->disableOriginalConstructor()
			->getMock();

		$parser->expects( $this->any() )
			->method( 'getTitle' )
			->willReturn( $title );

		$parser->expects( $this->any() )
			->method( 'getOptions' )
			->willReturn( $parserOptions );

		$parser->expects( $this->any() )
			->method( 'getOutput' )
			->willReturn( $parserOutput );

		$text = '';

		$this->assertThatHookIsExcutable(
			$instance->getHandlerFor( $handler ),
			[ &$parser, &$text ]
		);
	}

	private function doTestParserAfterTidyToBailOutEarly( $instance ) {
		$handler = 'ParserAfterTidy';

		$this->assertTrue(
			$instance->isRegistered( $handler )
		);

		$title = Title::newFromText( __METHOD__ );

		$parserOptions = $this->getMockBuilder( '\ParserOptions' )
			->disableOriginalConstructor()
			->getMock();

		$parserOptions->expects( $this->any() )
			->method( 'getInterfaceMessage' )
			->willReturn( true );

		$parserOutput = $this->getMockBuilder( '\ParserOutput' )
			->disableOriginalConstructor()
			->getMock();

		$parser = $this->getMockBuilder( '\Parser' )
			->disableOriginalConstructor()
			->getMock();

		$parser->expects( $this->any() )
			->method( 'getTitle' )
			->willReturn( $title );

		$parser->expects( $this->any() )
			->method( 'getOptions' )
			->willReturn( $parserOptions );

		$text = '';

		$this->assertThatHookIsExcutable(
			$instance->getHandlerFor( $handler ),
			[ &$parser, &$text ]
		);
	}

	private function doTestSmwParserBeforeMagicWordsFinder( $instance ) {
		$handler = 'SMW::Parser::BeforeMagicWordsFinder';

		$this->assertTrue(
			$instance->isRegistered( $handler )
		);

		$magicWords = [];

		$this->assertThatHookIsExcutable(
			$instance->getHandlerFor( $handler ),
			[ &$magicWords ]
		);

		$this->assertContains(
			'SBL_NOBREADCRUMBLINKS',
			$magicWords
		);
	}

	private function doTestOutputPageParserOutput( $instance, $outputPage ) {
		$handler = 'OutputPageParserOutput';

		$this->assertTrue(
			$instance->isRegistered( $handler )
		);

		$parserOutput = $this->getMockBuilder( '\ParserOutput' )
			->disableOriginalConstructor()
			->getMock();

		$magicWords = [];

		$this->assertThatHookIsExcutable(
			$instance->getHandlerFor( $handler ),
			[ &$outputPage, $parserOutput ]
		);

		$this->assertSame(
			null,
			$outputPage->smwmagicwords
		);
	}

	private function assertThatHookIsExcutable( \Closure $handler, $arguments ) {
		$this->assertIsBool(

			call_user_func_array( $handler, $arguments )
		);
	}

}
