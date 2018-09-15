<?php

namespace SBL\Tests;

use SBL\PropertyRegistry;

/**
 * @covers \SBL\PropertyRegistry
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

		$propertyRegistry = $this->getMockBuilder( '\SMW\PropertyRegistry' )
			->disableOriginalConstructor()
			->getMock();

		$propertyRegistry->expects( $this->atLeastOnce() )
			->method( 'registerProperty' )
			->with( $this->equalTo( PropertyRegistry::SBL_PARENTPAGE ) );

		$propertyRegistry->expects( $this->atLeastOnce() )
			->method( 'registerPropertyAlias' )
			->with( $this->equalTo( PropertyRegistry::SBL_PARENTPAGE ) );

		$instance = new PropertyRegistry();
		$instance->register( $propertyRegistry );
	}

}
