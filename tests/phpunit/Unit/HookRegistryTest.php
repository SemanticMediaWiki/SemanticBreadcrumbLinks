<?php

namespace SBL\Tests;

use SBL\HookRegistry;

use HashBagOStuff;
use Title;

/**
 * @covers \SBL\HookRegistry
 *
 * @group semantic-breadcrumb-links
 *
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class HookRegistryTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {

		$store = $this->getMockBuilder( '\SMW\Store' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$configuration = array();

		$this->assertInstanceOf(
			'\SBL\HookRegistry',
			new HookRegistry( $store, $configuration )
		);
	}

	public function testRegister() {

		$title = Title::newFromText( __METHOD__ );

		$outputPage = $this->getMockBuilder( '\OutputPage' )
			->disableOriginalConstructor()
			->getMock();

		$outputPage->expects( $this->any() )
			->method( 'getTitle' )
			->will( $this->returnValue( $title ) );

		$skin = $this->getMockBuilder( '\Skin' )
			->disableOriginalConstructor()
			->getMock();

		$skin->expects( $this->any() )
			->method( 'getOutput' )
			->will( $this->returnValue( $outputPage ) );

		$store = $this->getMockBuilder( '\SMW\Store' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();

		$configuration = array(
			'useSubpageFinderFallback' => false,
			'tryToFindClosestDescendant' => false,
			'propertySearchPatternByNamespace' => array(),
			'breadcrumbTrailStyleClass' => 'foo',
			'breadcrumbDividerStyleClass' => 'bar',
			'hideSubpageParent' => true,
			'wgNamespacesWithSubpages' => array()
		);

		$instance = new HookRegistry(
			$store,
			$configuration
		);

		$instance->register();

		$this->doTestInitProperties( $instance );
		$this->doTestSkinTemplateOutputPageBeforeExec( $instance, $skin );
		$this->doTestBeforePageDisplay( $instance, $outputPage, $skin );
	}

	private function doTestInitProperties( $instance ) {

		$this->assertTrue(
			$instance->isRegistered( 'SMW::Property::initProperties' )
		);

		$this->assertThatHookIsExcutable(
			$instance->getHandlersFor( 'SMW::Property::initProperties' ),
			array()
		);
	}

	private function doTestSkinTemplateOutputPageBeforeExec( $instance, $skin ) {

		$this->assertTrue(
			$instance->isRegistered( 'SkinTemplateOutputPageBeforeExec' )
		);

		$template = new \stdClass;

		$this->assertThatHookIsExcutable(
			$instance->getHandlersFor( 'SkinTemplateOutputPageBeforeExec' ),
			array( &$skin, &$template )
		);
	}

	private function doTestBeforePageDisplay( $instance, $outputPage, $skin ) {

		$this->assertTrue(
			$instance->isRegistered( 'BeforePageDisplay' )
		);

		$this->assertThatHookIsExcutable(
			$instance->getHandlersFor( 'BeforePageDisplay' ),
			array( &$outputPage, &$skin )
		);
	}

	private function assertThatHookIsExcutable( \Closure $handler, $arguments ) {
		$this->assertInternalType(
			'boolean',
			call_user_func_array( $handler, $arguments )
		);
	}

}
