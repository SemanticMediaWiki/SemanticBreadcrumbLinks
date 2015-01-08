<?php

namespace SBL\Tests;

use SBL\PropertyRegistry;

use SMW\DIProperty;

/**
 * @covers \SBL\PropertyRegistry
 *
 * @group semantic-breadcrumb-links
 *
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class PropertyRegistryTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {

		$this->assertInstanceOf(
			'\SBL\PropertyRegistry',
			new PropertyRegistry()
		);
	}

	public function testRegister() {

		$instance = new PropertyRegistry();
		$instance->register();

		$this->assertNotEmpty(
			DIProperty::findPropertyLabel( PropertyRegistry::SBL_PARENTPAGE )
		);

		$this->assertSame(
			SBL_PROP_PARENTPAGE,
			DIProperty::findPropertyLabel( PropertyRegistry::SBL_PARENTPAGE )
		);
	}

}
