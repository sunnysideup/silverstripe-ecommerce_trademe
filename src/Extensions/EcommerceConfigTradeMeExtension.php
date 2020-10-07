<?php
/**
 * EcommerceRole provides customisations to the {@link Member}
 * class specifically for this ecommerce module.
 *
 * @package ecommerce
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
            TextareaField::create('TradeMeIntro'),
            LiteralField::create('TradeMeLink', '<h2><a href="'.TradeMeAssignGroupController::my_link().'">quick edit categories</a></h2>')
        );
    }
}
