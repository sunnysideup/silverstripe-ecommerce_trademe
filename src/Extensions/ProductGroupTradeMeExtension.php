<?php

namespace Sunnysideup\EcommerceTrademe\Extensions;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\ORM\DataExtension;
use Sunnysideup\Ecommerce\Pages\ProductGroup;
use Sunnysideup\EcommerceTrademe\Api\TradeMeCategories;
use Sunnysideup\EcommerceTrademe\Api\TradeMeGenericCmsFieldsProvider;

/**
 * Product Group is a 'holder' for Products within the CMS
 * It contains functions for versioning child products
 *
 * @package ecommerce
 */

/**
 * ### @@@@ START REPLACEMENT @@@@ ###
 * WHY: automated upgrade
 * OLD:  extends DataExtension (ignore case)
 * NEW:  extends DataExtension (COMPLEX)
 * EXP: Check for use of $this->anyVar and replace with $this->anyVar[$this->owner->ID] or consider turning the class into a trait
 * ### @@@@ STOP REPLACEMENT @@@@ ###
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
                TradeMeGenericCmsFieldsProvider::get_fields($this->owner, true)
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
