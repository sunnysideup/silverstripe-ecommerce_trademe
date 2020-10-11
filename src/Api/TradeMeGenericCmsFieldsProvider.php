<?php

class TradeMeGenericCmsFieldsProvider
{

    public static function get_fields(?int $groupID = 0) : array
    {
        return [
            ReadonlyField::create(
                'TradeMeLink1',
                'Categories',
                DBField::create_field(
                    'HTMLText',
                    '<h2><a href="'.TradeMeAssignGroupController::my_link().'">edit all categories</a></h2>'
                )
            ),
            ReadonlyField::create(
                'TradeMeLink2',
                'Products',
                DBField::create_field(
                    'HTMLText',
                    '<h2><a href="'.TradeMeAssignProductController::my_link().'?parentid='.$groupID.'">edit products in '.$groupID.'</a></h2>'
                )
            ),
            ReadonlyField::create(
                'TradeMeLink3',
                'Export',
                DBField::create_field(
                    'HTMLText',
                    '<h2><a href="dev/tasks/'.Config::inst()->get('TradeMeAssignGroupController', 'create_trademe_csv_task_class_name').'">Export to TradeMe</a></h2>'
                )
            ),
            ReadonlyField::create(
                'TradeMeLink3',
                'Export',
                DBField::create_field(
                    'HTMLText',
                    '<h2><a href="admin/siteconfig/">TradeMe Sitewide Settings</a></h2>'
                )
            ),
        ];

    }

}
