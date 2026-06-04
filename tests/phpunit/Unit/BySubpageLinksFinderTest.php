<?php

namespace SBL\Tests;

use MediaWiki\Title\Title;
use SBL\BySubpageLinksFinder;
use SMW\DataItems\WikiPage;

/**
 * @covers \SBL\BySubpageLinksFinder
 * @group semantic-breadcrumb-links
 *
 * @license GPL-2.0-or-later
 * @since 1.0
 *
 * @author mwjames
 */
class BySubpageLinksFinderTest extends \PHPUnit\Framework\TestCase {

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
	public function testFindParentBreadcrumbs( $title, $count, $expected ) {
		$subject = WikiPage::newFromTitle( Title::newFromText( $title ) );

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
		# 0
		$provider[] = [
			'Foo',
			0,
			[]
		];

		# 1
		$provider[] = [
			'Foo/',
			1,
			[
				new WikiPage( 'Foo', NS_MAIN )
			]
		];

		# 2
		$provider[] = [
			'Foo/Bar/Baz',
			2,
			[
				new WikiPage( 'Foo', NS_MAIN ),
				new WikiPage( 'Foo/Bar', NS_MAIN )
			]
		];

		# 3
		$provider[] = [
			'Foo/Bar/Baz/Yin/Yan',
			4,
			[
				new WikiPage( 'Foo', NS_MAIN ),
				new WikiPage( 'Foo/Bar', NS_MAIN ),
				new WikiPage( 'Foo/Bar/Baz', NS_MAIN ),
				new WikiPage( 'Foo/Bar/Baz/Yin', NS_MAIN )
			]
		];

		# 4 /a/b
		$provider[] = [
			'/a/b',
			1,
			[
				new WikiPage( '/a', NS_MAIN )
			]
		];

		# 5 /a//b/c
		$provider[] = [
			'/a//b/c',
			2,
			[
				new WikiPage( '/a', NS_MAIN ),
				new WikiPage( '/a//b', NS_MAIN )
			]
		];

		# 6 (#23 issue)
		$provider[] = [
			'Foo / Bar',
			0,
			[]
		];

		# 7 (#23 issue)
		$provider[] = [
			'Foo /Bar',
			0,
			[]
		];

		# 8 (#23 issue)
		$provider[] = [
			'Foo /Bar /Foobar',
			0,
			[]
		];

		# 9
		$provider[] = [
			'Help:Foo/Foobar',
			1,
			[
				new WikiPage( 'Foo', NS_HELP ),
			]
		];

		return $provider;
	}

}
