<?php

namespace SBL\Tests;

use SBL\PageDisplayOutputModifier;

/**
 * @covers \SBL\PageDisplayOutputModifier
 * @group semantic-breadcrumb-links
 *
 * @license GPL-2.0-or-later
 * @since 1.0
 *
 * @author mwjames
 */
class PageDisplayOutputModifierTest extends \PHPUnit\Framework\TestCase {

	public function testCanConstruct() {
		$this->assertInstanceOf(
			'\SBL\PageDisplayOutputModifier',
			new PageDisplayOutputModifier()
		);
	}

	public function testDisabledHideSubpageParentForTitleManipulation() {
		$instance = new PageDisplayOutputModifier();
		$instance->hideSubpageParent( false );
		$instance->setSubpageByNamespace( [ NS_MAIN => true ] );

		$output = $this->getMockBuilder( '\OutputPage' )
			->disableOriginalConstructor()
			->getMock();

		$output->expects( $this->atLeastOnce() )
			->method( 'getTitle' );

		$instance->modifyOutput( $output );
	}

	public function testDisabledSubpageNamespaceForTitleManipulation() {
		$instance = new PageDisplayOutputModifier();
		$instance->hideSubpageParent( true );
		$instance->setSubpageByNamespace( [ NS_MAIN => false ] );

		$title = $this->getMockBuilder( '\Title' )
			->disableOriginalConstructor()
			->getMock();

		$title->expects( $this->once() )
			->method( 'getNamespace' )
			->willReturn( NS_MAIN );

		$output = $this->getMockBuilder( '\OutputPage' )
			->disableOriginalConstructor()
			->getMock();

		$output->expects( $this->atLeastOnce() )
			->method( 'getTitle' )
			->willReturn( $title );

		$instance->modifyOutput( $output );
	}

	public function testEnabledSubpageForTitleManipulation() {
		$instance = new PageDisplayOutputModifier();
		$instance->hideSubpageParent( true );
		$instance->setSubpageByNamespace( [ NS_MAIN => true ] );

		$title = $this->getMockBuilder( '\Title' )
			->disableOriginalConstructor()
			->getMock();

		$title->expects( $this->once() )
			->method( 'isSubpage' )
			->willReturn( true );

		$title->expects( $this->atLeastOnce() )
			->method( 'getNamespace' )
			->willReturn( NS_MAIN );

		$output = $this->getMockBuilder( '\OutputPage' )
			->disableOriginalConstructor()
			->getMock();

		$output->expects( $this->atLeastOnce() )
			->method( 'getTitle' )
			->willReturn( $title );

		$output->expects( $this->once() )
			->method( 'setPageTitle' );

		$instance->modifyOutput( $output );
	}

	public function testEnabledSubpageForTitleManipulationOnInvalidSubpage() {
		$instance = new PageDisplayOutputModifier();
		$instance->hideSubpageParent( true );
		$instance->setSubpageByNamespace( [ NS_MAIN => true ] );

		$title = $this->getMockBuilder( '\Title' )
			->disableOriginalConstructor()
			->getMock();

		$title->expects( $this->once() )
			->method( 'isSubpage' )
			->willReturn( true );

		$title->expects( $this->once() )
			->method( 'getText' )
			->willReturn( 'Foo /Bar' );

		$title->expects( $this->atLeastOnce() )
			->method( 'getNamespace' )
			->willReturn( NS_MAIN );

		$output = $this->getMockBuilder( '\OutputPage' )
			->disableOriginalConstructor()
			->getMock();

		$output->expects( $this->atLeastOnce() )
			->method( 'getTitle' )
			->willReturn( $title );

		$output->expects( $this->never() )
			->method( 'setPageTitle' );

		$instance->modifyOutput( $output );
	}

}
