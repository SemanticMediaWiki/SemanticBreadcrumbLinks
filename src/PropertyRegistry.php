<?php

namespace SBL;

/**
 * @license GPL-2.0-or-later
 * @since 1.0
 *
 * @author mwjames
 */
class PropertyRegistry {

	public const SBL_PARENTPAGE = '__sbl_parentpage';

	/**
	 * @since 1.0
	 *
	 * @return bool
	 */
	public function register( $propertyRegistry ) {
		$propertyDefinitions = [

			self::SBL_PARENTPAGE => [
				'label' => SBL_PROP_PARENTPAGE,
				'type'  => '_wpg',
				'alias' => 'sbl-property-alias-parentpage',
				'description' => 'sbl-property-predefined-parentpage',
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

			$propertyRegistry->registerPropertyAliasByMsgKey(
				$propertyId,
				$definition['alias']
			);

			// Without this, SMW derives the key from the property id, which
			// for `__sbl_parentpage` is `smw-property-predefined--sbl-parentpage`;
			// the long text is looked up under the registered key plus `-long`.
			$propertyRegistry->registerPropertyDescriptionByMsgKey(
				$propertyId,
				$definition['description']
			);
		}

		return true;
	}

}
