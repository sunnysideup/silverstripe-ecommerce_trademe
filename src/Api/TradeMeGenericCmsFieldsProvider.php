<?php

namespace Sunnysideup\EcommerceTrademe\Api;

use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\ORM\FieldType\DBField;
use Sunnysideup\EcommerceTrademe\Control\TradeMeAssignGroupController;
use Sunnysideup\EcommerceTrademe\Control\TradeMeAssignProductController;

class TradeMeGenericCmsFieldsProvider
{
    /**
     * @return array[FormField]
     */
    public static function get_fields($group = null, $showConfigLink = false): array
    {
        if ($group && $group->exists()) {
            $productField = ReadonlyField::create(
                'TradeMeLink2',
                'Products',
                DBField::create_field(
                    'HTMLText',
                    '<a href="' . TradeMeAssignProductController::my_link('', ['parentid' => $group->ID]) . '">edit products in <strong>' . $group->Title . '</strong></a>'
                )
            );
        } else {
            $productField = ReadonlyField::create(
                'TradeMeLink2',
                'Products',
                DBField::create_field(
                    'HTMLText',
                    '<a href="' . TradeMeAssignProductController::my_link() . '">edit products in categories</a>'
                )
            );
        }

        $link = '/dev/tasks/' . Config::inst()->get(TradeMeAssignGroupController::class, 'create_trademe_csv_task_class_name');

        $ar = [
            ReadonlyField::create(
                'TradeMeLink1',
                'Categories',
                DBField::create_field(
                    'HTMLText',
                    '<a href="' . TradeMeAssignGroupController::my_link() . '">edit all categories</a>'
                )
            ),
            $productField,
            ReadonlyField::create(
                'TradeMeLink3',
                'Export',
                DBField::create_field(
                    'HTMLText',
                    '<a href="dev/tasks/' . Config::inst()->get(TradeMeAssignGroupController::class, 'create_trademe_csv_task_class_name') . '">Export to TradeMe</a>'
                )
            ),
        ];
        if ($showConfigLink) {
            $ar[] = ReadonlyField::create(
                'TradeMeLink3',
                'Export',
                DBField::create_field(
                    'HTMLText',
                    '<a href="admin/shop/">Sitewide E-commerce TradeMe Settings</a>'
                )
            );
        }
        $ar[] = ReadonlyField::create(
            'ExportToTradeMeNow',
            'Export To TradeMe',
            DBField::create_field(
                'HTMLText',
                '<a href="' . $link . '">Start export process now</a>'
            )
        );
        return $ar;
    }
}
