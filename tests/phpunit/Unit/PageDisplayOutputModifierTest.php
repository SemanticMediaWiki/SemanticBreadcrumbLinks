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

	public function testDisabledSubpageTitleManipulation() {

		$instance = new PageDisplayOutputModifier();
		$instance->setHideSubpageParentState( false );

		$output = $this->getMockBuilder( '\OutputPage' )
			->disableOriginalConstructor()
			->getMock();

		$output->expects( $this->never() )
			->method( 'getTitle' );

		$instance->modifyOutput( $output );
	}

	public function testEnabledSubpageTitleManipulation() {

		$instance = new PageDisplayOutputModifier();
		$instance->setHideSubpageParentState( true );

		$title = $this->getMockBuilder( '\Title' )
			->disableOriginalConstructor()
			->getMock();

		$title->expects( $this->once() )
			->method( 'isSubpage' )
			->will( $this->returnValue( true ) );

		$output = $this->getMockBuilder( '\OutputPage' )
			->disableOriginalConstructor()
			->getMock();

		$output->expects( $this->atLeastOnce() )
			->method( 'getTitle' )
			->will( $this->returnValue( $title ) );

		$instance->modifyOutput( $output );
	}

}
