<?php

namespace SBL\Tests;

use SBL\BySubpageLinksFinder;

use SMW\DIWikiPage;

use Title;

/**
 * @covers \SBL\BySubpageLinksFinder
 *
 * @group semantic-breadcrumb-links
 *
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class BySubpageLinksFinderTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {

		$this->assertInstanceOf(
			'\SBL\BySubpageLinksFinder',
			new BySubpageLinksFinder()
		);
	}

	public function testDisabledFinder() {

		$instance = new BySubpageLinksFinder();
		$instance->setSubpageDiscoverySupportState( false );

		$this->assertFalse(
			$instance->canUseSubpageDiscoveryForFallback()
		);
	}

	/**
	 * @dataProvider titleProvider
	 */
	public function testFindParentBreadcrumbs( $title,$count, $expected ) {

		$subject = DIWikiPage::newFromTitle( Title::newFromText( $title ) );

		$instance = new BySubpageLinksFinder();
		$instance->setSubpageDiscoverySupportState( true );

		$this->assertEmpty(
			$instance->getParents()
		);

		$instance->tryToFindLinksFor( $subject );

		$this->assertCount(
			$count,
			$instance->getParents()
		);

		$this->assertEquals(
			$expected,
			$instance->getParents()
		);
	}

	public function titleProvider() {

		#0
		$provider[] = array(
			'Foo',
			0,
			array()
		);

		#1
		$provider[] = array(
			'Foo/',
			1,
			array(
				new DIWikiPage( 'Foo', NS_MAIN )
			)
		);

		#2
		$provider[] = array(
			'Foo/Bar/Baz',
			2,
			array(
				new DIWikiPage( 'Foo', NS_MAIN ),
				new DIWikiPage( 'Foo/Bar', NS_MAIN )
			)
		);

		#3
		$provider[] = array(
			'Foo/Bar/Baz/Yin/Yan',
			4,
			array(
				new DIWikiPage( 'Foo', NS_MAIN ),
				new DIWikiPage( 'Foo/Bar', NS_MAIN ),
				new DIWikiPage( 'Foo/Bar/Baz', NS_MAIN ),
				new DIWikiPage( 'Foo/Bar/Baz/Yin', NS_MAIN )
			)
		);

		return $provider;
	}

}
