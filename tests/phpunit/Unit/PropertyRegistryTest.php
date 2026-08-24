<?php

namespace SBL\Tests;

use SBL\PropertyRegistry;
use SMW\PropertyRegistry as SmwPropertyRegistry;

/**
 * @covers \SBL\PropertyRegistry
 * @group semantic-breadcrumb-links
 *
 * @license GPL-2.0-or-later
 * @since 1.0
 *
 * @author mwjames
 */
class PropertyRegistryTest extends \PHPUnit\Framework\TestCase {

	public function testCanConstruct() {
		$this->assertInstanceOf(
			PropertyRegistry::class,
			new PropertyRegistry()
		);
	}

	public function testRegister() {
		$propertyRegistry = $this->newSmwPropertyRegistry();

		$propertyRegistry->expects( $this->atLeastOnce() )
			->method( 'registerProperty' )
			->with( PropertyRegistry::SBL_PARENTPAGE );

		$propertyRegistry->expects( $this->atLeastOnce() )
			->method( 'registerPropertyAlias' )
			->with( PropertyRegistry::SBL_PARENTPAGE );

		$instance = new PropertyRegistry();
		$instance->register( $propertyRegistry );
	}

	public function testRegistersTheDescriptionUnderTheKeySmwLooksUp() {
		$propertyRegistry = $this->newSmwPropertyRegistry();

		$propertyRegistry->expects( $this->once() )
			->method( 'registerPropertyDescriptionByMsgKey' )
			->with( PropertyRegistry::SBL_PARENTPAGE, 'sbl-property-predefined-parentpage' );

		$instance = new PropertyRegistry();
		$instance->register( $propertyRegistry );
	}

	public function testRegisteredDescriptionMessagesExist() {
		$propertyRegistry = $this->newSmwPropertyRegistry();

		$registeredKey = null;
		$propertyRegistry->method( 'registerPropertyDescriptionByMsgKey' )
			->willReturnCallback( static function ( $id, $msgKey ) use ( &$registeredKey ) {
				$registeredKey = $msgKey;
			} );

		$instance = new PropertyRegistry();
		$instance->register( $propertyRegistry );

		$this->assertTrue( wfMessage( $registeredKey )->exists() );

		// SMW derives the second paragraph's key by appending `-long`; no code references it.
		$this->assertTrue( wfMessage( $registeredKey . '-long' )->exists() );
	}

	private function newSmwPropertyRegistry() {
		return $this->getMockBuilder( SmwPropertyRegistry::class )
			->disableOriginalConstructor()
			->getMock();
	}

}
