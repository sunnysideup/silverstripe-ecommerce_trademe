<?php

namespace Sunnysideup\EcommerceTrademe\Api;

use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\View\ViewableData;

/**
 * ### @@@@ START REPLACEMENT @@@@ ###
 * WHY: automated upgrade
 * OLD:  extends Object (ignore case)
 * NEW:  extends ViewableData (COMPLEX)
 * EXP: This used to extend Object, but object does not exist anymore. You can also manually add use Extensible, use Injectable, and use Configurable
 * ### @@@@ STOP REPLACEMENT @@@@ ###
 */
class TradeMeCategories extends ViewableData
{
    /**
     * e.g.
     * 4751 => 'Electronics-photography > Batteries > Disposable > AA
     * @var array
     */
    private static $trade_me_categories = 0;

    /**
     * default category for trademe (backup)
     * @var int
     */
    private static $trade_me_default = '';

    public static function get_categories(): array
    {
        return Config::inst()->get(TradeMeCategories::class, 'trade_me_categories');
    }

    public static function get_title_from_id($categoryID): string
    {
        $array = self::get_categories();
        if (! $categoryID) {
            $categoryID = Config::inst()->get(TradeMeCategories::class, 'trade_me_default');
        }
        return $array[$categoryID] ?? 'unknown category';
    }

    public static function categories_field()
    {
        return new DropdownField(
            'TradeMeCategoryID',
            'TradeMe Category',
            TradeMeCategories::get_categories()
        );
    }

    public static function calculated_categories_field($object)
    {
        $calculatedCategory = $object->getCalculatedTradeMeCategory();
        if ($calculatedCategory !== $object->TradeMeCategoryID) {
            return ReadonlyField::create(
                'CalculatedCategory',
                'Calculated Category',
                self::get_title_from_id($calculatedCategory)
            )->setDescription('
                    The TradeMe Category for this Category/Product is set by one of the parent Product Categories.
                ');
        }
        return HiddenField::create('CalculatedCategory');
    }

    public static function get_trade_me_categories() : array
    {
        $categories = [];
        foreach (self::get_categories() as $id => $category) {
            $categories[$id] = $category . ' (' . $id . ')';
        }
        return $categories;
    }
}
