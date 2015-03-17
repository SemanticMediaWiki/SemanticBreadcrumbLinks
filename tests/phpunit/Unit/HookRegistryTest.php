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
			new HookRegistry(
				$store,
				$configuration,
				$this->getMock( 'SBL\PropertyRegistry' )
			)
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

		$wgHooks = array();

		$instance = new HookRegistry(
			$store,
			$configuration
		);

		$instance->register( $wgHooks );

		$this->assertNotEmpty(
			$wgHooks
		);

		$this->doTestInitProperties( $wgHooks );
		$this->doTestSkinTemplateOutputPageBeforeExec( $wgHooks, $skin );
		$this->doTestBeforePageDisplay( $wgHooks, $outputPage, $skin );
	}

	private function doTestInitProperties( $wgHooks ) {

		$this->assertThatHookIsExcutable(
			$wgHooks,
			'smwInitProperties',
			array()
		);
	}

	private function doTestSkinTemplateOutputPageBeforeExec( $wgHooks, $skin ) {

		$template = new \stdClass;

		$this->assertThatHookIsExcutable(
			$wgHooks,
			'SkinTemplateOutputPageBeforeExec',
			array( &$skin, &$template )
		);
	}

	private function doTestBeforePageDisplay( $wgHooks, $outputPage, $skin ) {

		$this->assertThatHookIsExcutable(
			$wgHooks,
			'BeforePageDisplay',
			array( &$outputPage, &$skin )
		);
	}

	private function assertThatHookIsExcutable( $wgHooks, $hookName, $arguments ) {
		foreach ( $wgHooks[ $hookName ] as $hook ) {
			$this->assertInternalType(
				'boolean',
				call_user_func_array( $hook, $arguments )
			);
		}
	}

}
