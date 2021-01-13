<?php
/**
 * EcommerceRole provides customisations to the {@link Member}
 * class specifically for this ecommerce module.
 *
 * @package ecommerce
 */

/**
  * ### @@@@ START REPLACEMENT @@@@ ###
  * WHY: automated upgrade
  * OLD:  extends DataExtension (ignore case)
  * NEW:  extends DataExtension (COMPLEX)
  * EXP: Check for use of $this->anyVar and replace with $this->anyVar[$this->owner->ID] or consider turning the class into a trait
  * ### @@@@ STOP REPLACEMENT @@@@ ###
  */
class EcommerceConfigTradeMeExtension extends DataExtension
{
    private static $db = [
        'TradeMeIntro' => 'Varchar(255)',
    ];

    public function UpdateCMSFields(FieldList $fields)
    {
        //offline
        $fields->addFieldsToTab(
            'Root.TradeMe',
            array_merge(
                [
                    TextareaField::create('TradeMeIntro'),

                ],
                TradeMeGenericCmsFieldsProvider::get_fields()
            )
        );
    }
}








