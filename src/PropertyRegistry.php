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
	public function register() {

		$propertyDefinitions = array(

			self::SBL_PARENTPAGE => array(
				'label' => SBL_PROP_PARENTPAGE,
				'type'  => '_wpg',
				'alias' => wfMessage( 'sbl-property-alias-parentpage' )->text(),
				'visbility' => true
			)
		);

		foreach ( $propertyDefinitions as $propertyId => $definition ) {

			DIProperty::registerProperty(
				$propertyId,
				$definition['type'],
				$definition['label'],
				$definition['visbility']
			);

			DIProperty::registerPropertyAlias(
				$propertyId,
				$definition['alias']
			);
		}

		return true;
	}

}
