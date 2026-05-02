<?php

namespace SBL\Tests;

use SBL\Options;

/**
 * @covers \SBL\Options
 * @group semantic-breadcrumb-links
 *
 * @license GPL-2.0-or-later
 * @since   1.2
 *
 * @author mwjames
 */
class OptionsTest extends \PHPUnit\Framework\TestCase {

	public function testCanConstruct() {
		$this->assertInstanceOf(
			'\SBL\Options',
			new Options()
		);
	}

	public function testAddOption() {
		$instance = new Options();

		$this->assertFalse(
			$instance->has( 'Foo' )
		);

		$instance->set( 'Foo', 42 );

		$this->assertEquals(
			42,
			$instance->get( 'Foo' )
		);
	}

	public function testUnregisteredKeyThrowsException() {
		$instance = new Options();

		$this->expectException( 'InvalidArgumentException' );
		$instance->get( 'Foo' );
	}

}
