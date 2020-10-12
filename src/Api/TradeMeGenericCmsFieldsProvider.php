<?php

class TradeMeGenericCmsFieldsProvider
{

    /**
     *
     * @param  ProductGroup|null
     * @return array[FormField]
     */
    public static function get_fields($group = null) : array
    {
        if($group && $group->exists()) {
            $productField = ReadonlyField::create(
                'TradeMeLink2',
                'Products',
                DBField::create_field(
                    'HTMLText',
                    '<a href="'.TradeMeAssignProductController::my_link('', ['parentid' => $group->ID]).'">edit products in <strong>'.$group->Title.'</strong></a>'
                )
            );
        } else {
            $productField = ReadonlyField::create(
                'TradeMeLink2',
                'Products',
                DBField::create_field(
                    'HTMLText',
                    '<a href="'.TradeMeAssignProductController::my_link().'">edit products in categories</a>'
                )
            );

        }
        return [
            ReadonlyField::create(
                'TradeMeLink1',
                'Categories',
                DBField::create_field(
                    'HTMLText',
                    '<a href="'.TradeMeAssignGroupController::my_link().'">edit all categories</a>'
                )
            ),
            $productField,
            ReadonlyField::create(
                'TradeMeLink3',
                'Export',
                DBField::create_field(
                    'HTMLText',
                    '<a href="dev/tasks/'.Config::inst()->get('TradeMeAssignGroupController', 'create_trademe_csv_task_class_name').'">Export to TradeMe</a>'
                )
            ),
            ReadonlyField::create(
                'TradeMeLink3',
                'Export',
                DBField::create_field(
                    'HTMLText',
                    '<a href="admin/siteconfig/">Sitewide TradeMe Settings</a>'
                )
            ),
        ];

    }

}
