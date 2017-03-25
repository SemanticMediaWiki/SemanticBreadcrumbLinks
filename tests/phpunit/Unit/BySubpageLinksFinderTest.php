<?php

namespace SBL\Tests;

use SBL\BySubpageLinksFinder;
use SMW\DIWikiPage;
use Title;

/**
 * @covers \SBL\BySubpageLinksFinder
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
		$instance->setSubpageDiscoveryFallback( false );

		$this->assertFalse(
			$instance->isDiscoveryFallback()
		);
	}

	/**
	 * @dataProvider titleProvider
	 */
	public function testFindParentBreadcrumbs( $title,$count, $expected ) {

		$subject = DIWikiPage::newFromTitle( Title::newFromText( $title ) );

		$instance = new BySubpageLinksFinder();
		$instance->setSubpageDiscoveryFallback( true );

		$this->assertEmpty(
			$instance->getParents()
		);

		$instance->findLinksBySubject( $subject );

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

		#4 /a/b
		$provider[] = array(
			'/a/b',
			1,
			array(
				new DIWikiPage( '/a', NS_MAIN )
			)
		);

		#5 /a//b/c
		$provider[] = array(
			'/a//b/c',
			2,
			array(
				new DIWikiPage( '/a', NS_MAIN ),
				new DIWikiPage( '/a//b', NS_MAIN )
			)
		);

		#6 (#23 issue)
		$provider[] = array(
			'Foo / Bar',
			0,
			array()
		);

		#7 (#23 issue)
		$provider[] = array(
			'Foo /Bar',
			0,
			array()
		);

		#8 (#23 issue)
		$provider[] = array(
			'Foo /Bar /Foobar',
			0,
			array()
		);

		return $provider;
	}

}
