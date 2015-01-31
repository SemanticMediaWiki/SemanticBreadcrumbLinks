<?php

namespace SBL\Tests;

use SBL\PageDisplayOutputModifier;

/**
 * @covers \SBL\PageDisplayOutputModifier
 *
 * @group semantic-breadcrumb-links
 *
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class PageDisplayOutputModifierTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {

		$this->assertInstanceOf(
			'\SBL\PageDisplayOutputModifier',
			new PageDisplayOutputModifier()
		);
	}

	public function testDisabledHideSubpageParentForTitleManipulation() {

		$instance = new PageDisplayOutputModifier();
		$instance->setHideSubpageParentState( false );
		$instance->setSubpageByNamespace( array( NS_MAIN => true ) );

		$output = $this->getMockBuilder( '\OutputPage' )
			->disableOriginalConstructor()
			->getMock();

		$output->expects( $this->never() )
			->method( 'getTitle' );

		$instance->modifyOutput( $output );
	}

	public function testDisabledSubpageNamespaceForTitleManipulation() {

		$instance = new PageDisplayOutputModifier();
		$instance->setHideSubpageParentState( true );
		$instance->setSubpageByNamespace( array( NS_MAIN => false ) );

		$title = $this->getMockBuilder( '\Title' )
			->disableOriginalConstructor()
			->getMock();

		$title->expects( $this->once() )
			->method( 'getNamespace' )
			->will( $this->returnValue( NS_MAIN ) );

		$output = $this->getMockBuilder( '\OutputPage' )
			->disableOriginalConstructor()
			->getMock();

		$output->expects( $this->once() )
			->method( 'getTitle' )
			->will( $this->returnValue( $title ) );

		$instance->modifyOutput( $output );
	}

	public function testEnabledSubpageForTitleManipulation() {

		$instance = new PageDisplayOutputModifier();
		$instance->setHideSubpageParentState( true );
		$instance->setSubpageByNamespace( array( NS_MAIN => true ) );

		$title = $this->getMockBuilder( '\Title' )
			->disableOriginalConstructor()
			->getMock();

		$title->expects( $this->once() )
			->method( 'isSubpage' )
			->will( $this->returnValue( true ) );

		$title->expects( $this->once() )
			->method( 'getNamespace' )
			->will( $this->returnValue( NS_MAIN ) );

		$output = $this->getMockBuilder( '\OutputPage' )
			->disableOriginalConstructor()
			->getMock();

		$output->expects( $this->atLeastOnce() )
			->method( 'getTitle' )
			->will( $this->returnValue( $title ) );

		$instance->modifyOutput( $output );
	}

}
