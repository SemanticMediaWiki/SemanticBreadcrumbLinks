<?php

namespace SBL;

use SMW\DIProperty;

/**
 * @license GNU GPL v2+
 * @since 1.0
 *
 * @author mwjames
 */
class PropertyRegistry {

	const SBL_PARENTPAGE = '__sbl_parentpage';

	/**
	 * @since 1.0
	 *
	 * @return boolean
	 */
	public static function register() {

		$propertyDefinitions = array(

			self::SBL_PARENTPAGE => array(
				'label' => 'Has parent page',
				'type'  => '_wpg',
				'alias' => wfMessage( 'sbl-property-alias-parentpage' )->text()
			)
		);

		foreach ( $propertyDefinitions as $propertyId => $definition ) {

			DIProperty::registerProperty(
				$propertyId,
				$definition['type'],
				$definition['label'],
				true
			);

			DIProperty::registerPropertyAlias(
				$propertyId,
				$definition['alias']
			);
		}

		return true;
	}

}
