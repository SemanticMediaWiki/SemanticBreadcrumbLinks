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
	public function register( $propertyRegistry ) {

		$propertyDefinitions = [

			self::SBL_PARENTPAGE => [
				'label' => SBL_PROP_PARENTPAGE,
				'type'  => '_wpg',
				'alias' => 'sbl-property-alias-parentpage',
				'visbility' => true
			]
		];

		foreach ( $propertyDefinitions as $propertyId => $definition ) {

			$propertyRegistry->registerProperty(
				$propertyId,
				$definition['type'],
				$definition['label'],
				$definition['visbility']
			);

			$propertyRegistry->registerPropertyAlias(
				$propertyId,
				wfMessage( $definition['alias'] )->text()
			);

			// 2.4+
			if ( method_exists( $propertyRegistry, 'registerPropertyAliasByMsgKey' ) ) {
				$propertyRegistry->registerPropertyAliasByMsgKey(
					$propertyId,
					$definition['alias']
				);
			}
		}

		return true;
	}

}
