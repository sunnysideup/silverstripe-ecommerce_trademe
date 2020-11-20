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
        'ListProductsOnTradeMe' => 'Enum("some, none, all", "some")',
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldsToTab(
            'Root.TradeMe',
            array_merge(
                [
                    OptionsetField::create(
                        'ListProductsOnTradeMe',
                        'List ' . $this->owner->Title . ' on TradeMe?',
                        $this->owner->dbObject('ListProductsOnTradeMe')->enumValues()
                    )->setDescription('
                        Careful - saving this will also change the value for any underlying categories.
                        <br />E.g. If you set this value for Vegetables, it will also apply to Brocoli'),
                    TradeMeCategories::categories_field(),
                    TradeMeCategories::calculated_categories_field($this->owner),
                ],
                TradeMeGenericCmsFieldsProvider::get_fields($this->owner)
            )
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
        $myValue = $this->owner->ListProductsOnTradeMe;
        switch ($myValue) {
            case 'none':
            case 'all':
                $hasUpdate = true;
                break;
            case 'some':
            default:
                //do nothing
                $hasUpdate = false;
        }
        if ($hasUpdate === true) {
            $children = ProductGroup::get()->filter(['ParentID' => $this->owner->ID]);
            foreach ($children as $child) {
                if ($child->ListProductsOnTradeMe !== $myValue) {
                    $child->ListProductsOnTradeMe = $myValue;
                    $child->writeToStage('Stage');
                    $child->doPublish();
                }
            }
        }
    }
}
