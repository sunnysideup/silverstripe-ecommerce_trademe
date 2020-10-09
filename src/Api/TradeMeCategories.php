<?php


class TradeMeCategories extends Object
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
        return Config::inst()->get('TradeMeCategories', 'trade_me_categories');
    }

    public static function get_title_from_id($categoryID): string
    {
        $array = self::get_categories();
        if(! $categoryID) {
            $categoryID = Config::inst()->get('TradeMeCategories', 'trade_me_default');
        }
        return $array[$categoryID] ?? 'unknown category';
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
                    The TradeMe Category for this group is set by one of the parent Product Groups
                ');
        }
        return HiddenField::create('CalculatedCategory');
    }

    public static function get_trade_me_categories()
    {
        foreach (self::get_categories() as $id => $category) {
            $categories[$id] = $category . ' (' . $id . ')';
        }
        return $categories;
    }
}
