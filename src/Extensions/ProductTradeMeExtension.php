<?php


class ProductTradeMeExtension extends Extension
{
    /**
     * stadard SS declaration
     * @var array
     */
    private static $db = [
        'TradeMeCategoryID' => 'Int',
        'AlwaysShowOnTradeMe' => 'Boolean',
    ];

    /**
     * to identify
     * @var string
     */
    private static $trademe_group = '';

    /**
     * to identify
     * @var string
     */
    private static $trade_me_intro = '';

    /**
     * stadard SS method
     * @return FieldList
     */
    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldsToTab(
            'Root.TradeMe',
            [
                CheckboxField::create(
                    'AlwaysShowOnTradeMe',
                    'Always show on Trade Me'
                ),
                DropdownField::create(
                    'TradeMeCategoryID',
                    'Trade Me Category',
                    TradeMeCategories::get_categories()
                ),
                TradeMeCategories::calculated_categories_field($this->owner),
            ]
        );

        return $fields;
    }

    public function getCalculatedTradeMeCategory(): int
    {
        $parent = $this->owner;
        while ($parent) {
            $id = $parent->TradeMeCategoryID;
            if ($id) {
                return $id;
            }
            $parent = ProductGroup::get()->byID($parent->ParentID);
        }

        return 0;
    }

    /**
     * @return bool
     */
    public function isTradeMeProduct(): bool
    {
        return $this->owner->AlwaysShowOnTradeMe || $this->owner->getCalculatedTradeMeCategory() ? true : false;
    }

    /**
     * returns the right trade me category.
     * @return int
     */
    public function CalculatedTradeMeCategoryWithDefaultAndAdjustments(): int
    {
        $categoryID = $this->owner->getCalculatedTradeMeCategory();

        if ($categoryID) {
            if ($this->owner->hasMethod('getTradeMeCustomCategory')) {
                $categoryID = $this->owner->getTradeMeCustomCategory($categoryID);
            }
        } else {
            $categoryID = Config::inst()->get('TradeMeCategories', 'trade_me_default');
        }

        return $categoryID;
    }

    public function getTradeMeTitle($checkLimit = true)
    {
        $result = $this->owner->Title;
        $result = str_replace('&', ' and ', $result);
        if ($checkLimit) {
            $limit = 50;
            $result = substr($result, 0, $limit);
        }
        return $result;
    }

    public function getTradeMeContent($checkLimit = true)
    {
        $intro = EcommerceDBConfig::current_ecommerce_db_config()->TradeMeIntro;
        $content = $this->owner->Content;
        $content = str_replace('&', ' and ', $content);
        $content = str_replace('</p>', '<br><br>', $content);
        $content = strip_tags($content, '<br><br />') ?: '';
        $content = trim("${intro}<br /><br />" . ($content ?: ''));
        if ($checkLimit) {
            $limit = 2048;
            $result = substr($content, 0, $limit);
        }
        return $result;
    }

}
