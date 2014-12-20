<?php

namespace SBL\Tests;

use SBL\SubpageLinksFinder;

use SMW\DIWikiPage;

use Title;

/**
 * @covers \SBL\SubpageLinksFinder
 *
 * @group semantic-breadcrumb-links
 *
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class SubpageLinksFinderTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {

		$this->assertInstanceOf(
			'\SBL\SubpageLinksFinder',
			new SubpageLinksFinder()
		);
	}

	public function testDisabledFinder() {

		$instance = new SubpageLinksFinder();
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

		$instance = new SubpageLinksFinder();
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

		$provider[] = array(
			'Foo',
			0,
			array()
		);

		$provider[] = array(
			'Foo/',
			1,
			array(
				new DIWikiPage( 'Foo', NS_MAIN )
			)
		);

		$provider[] = array(
			'Foo/Bar/Baz',
			2,
			array(
				new DIWikiPage( 'Foo/Bar', NS_MAIN ),
				new DIWikiPage( 'Foo', NS_MAIN )
			)
		);

		$provider[] = array(
			'Foo/Bar/Baz/Yin/Yan',
			4,
			array(
				new DIWikiPage( 'Foo/Bar/Baz/Yin', NS_MAIN ),
				new DIWikiPage( 'Foo/Bar/Baz', NS_MAIN ),
				new DIWikiPage( 'Foo/Bar', NS_MAIN ),
				new DIWikiPage( 'Foo', NS_MAIN )
			)
		);

		return $provider;
	}

}
