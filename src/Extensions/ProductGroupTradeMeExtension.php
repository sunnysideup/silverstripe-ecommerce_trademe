<?php
/**
 * Product Group is a 'holder' for Products within the CMS
 * It contains functions for versioning child products
 *
 * @package ecommerce
 */
class ProductGroupTradeMeExtension extends DataExtension
{
    private static $db = [
        'TradeMeCategoryID' => 'Int',
        'ListProductsOnTradeMe' => 'Enum("none, some, all", "some")',
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldsToTab(
            'Root.TradeMe',
            [
                new DropdownField(
                    'TradeMeCategoryID',
                    'Category',
                    TradeMeCategories::get_categories()
                ),
                OptionsetField::create(
                    'ListProductsOnTradeMe',
                    'List ' . $this->owner->Title . ' on TradeMe?',
                    $this->owner->dbObject('ListProductsOnTradeMe')->enumValues()
                )->setDescription('
                    Careful - saving this will also change the value for any underlying categories.
                    <br />E.g. If you set this value for Vegetables, it will also apply to Brocoli'),
                TradeMeCategories::calculated_categories_field($this->owner),
                LiteralField::create('TradeMeLink', '<h2><a href="'.TradeMeAssignGroupController::my_link().'">quick edit categories</a></h2>')
            ]
        );


    }

    public function onBeforeWrite()
    {
        $this->owner->updateChildGroupsForTradeMe();
    }

    public function getCalculatedTradeMeCategory(): int
    {
        $parent = $this->owner;
        while ($parent) {
            if ($parent->TradeMeCategoryID) {
                return $parent->TradeMeCategoryID;
            }
            $parent = ProductGroup::get()->byID($parent->ParentID);
        }

        return 0;
    }

    public function CalculatedTradeMeCategoryWithDefaultAndAdjustments(): int
    {
        return $this->owner->getCalculatedTradeMeCategory();
    }

    public function updateChildGroupsForTradeMe()
    {
        $newValue = null;
        switch ($this->owner->ListProductsOnTradeMe) {
            case 'none':
                $newValue = 0;
                break;
            case 'all':
                $newValue = 1;
                break;
            case 'some':
            default:
                //do nothing
        }
        if ($newValue !== null) {
            $children = ProductGroup::get()->filter(['ParentID' => $this->owner->ID]);
            foreach ($children as $child) {
                if ($child->ListProductsOnTradeMe !== $this->owner->ListProductsOnTradeMe) {
                    $child->ListProductsOnTradeMe = $this->owner->ListProductsOnTradeMe;
                    $child->writeToStage('Stage');
                    $child->doPublish();
                }
            }
        }
    }
}
