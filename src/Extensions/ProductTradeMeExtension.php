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
        'IncludeOnTradeMe' => 'Boolean',
    ];
    /**
     * stadard SS declaration
     * @var array
     */
    private static $indexes = [
        'AlwaysShowOnTradeMe' => true,
        'IncludeOnTradeMe' => true,
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
                    'Always show on TradeMe'
                ),
                DropdownField::create(
                    'TradeMeCategoryID',
                    'TradeMe Category',
                    TradeMeCategories::get_categories()
                ),
                TradeMeCategories::calculated_categories_field($this->owner),
                LiteralField::create('TradeMeLink1', '<h2><a href="'.TradeMeAssignGroupController::my_link().'">quick edit categories</a></h2>'),
                LiteralField::create('TradeMeLink2', '<h2><a href="'.TradeMeAssignProductController::my_link().'?showvalue='.$this->owner->ParentID.'">quick edit products in this category</a></h2>')

            ]
        );
        $parent = $this->owner->Parent();
        if($parent && $parent->exists()) {
            $fields->addFieldToTab(
                "Root.TradeMe",
                (new CheckboxField('IncludeOnTradeMe', 'Show on TradeMe'))
                    ->setDescription('
                        If the parent category ('.$parent->Title.') is set to "SOME"
                        then checking this box will ensure the product is shown on TradeMe.
                        <br />Currently <strong>'.$parent->Title.'</strong> it is set to include <strong>'.strtoupper($parent->ListProductsOnTradeMe).'</strong> of its products.
                    '),
                'TradeMeCategoryID'
            );
        }
        return $fields;
    }

    /**
     * looks at TradeMe Category from the product itself
     * and it not found goes up the line (parent , parent.parent, etc...)
     * to find the applicable trade me category.
     * @return int
     */
    public function getCalculatedTradeMeCategory(): int
    {
        $parent = $this->owner;
        while ($parent) {
            if($parent instanceof ProductGroup) {
                $id = $parent->getCalculatedTradeMeCategory();
            } else {
                $id = $parent->TradeMeCategoryID;
            }
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

    /**
     * returns the title of the product for TradeMe.
     *
     * @param  boolean $checkLimit
     * @return string
     */
    public function getTradeMeTitle(?bool $checkLimit = true) : string
    {
        $result = $this->owner->Title;
        $result = str_replace('&', ' and ', $result);
        if ($checkLimit) {
            $limit = 50;
            $result = substr($result, 0, $limit);
        }
        return (string) $result;
    }

    /**
     *
     * @param  boolean $checkLimit
     * @return string
     */
    public function getTradeMeContent(?bool $checkLimit = true) : string
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
        return (string) $result;
    }

}
