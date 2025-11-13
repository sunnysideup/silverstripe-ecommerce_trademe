<?php

namespace Sunnysideup\EcommerceTrademe\Extensions;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Versioned\Versioned;
use Sunnysideup\Ecommerce\Pages\ProductGroup;
use Sunnysideup\EcommerceTrademe\Api\TradeMeCategories;
use Sunnysideup\EcommerceTrademe\Api\TradeMeGenericCmsFieldsProvider;

/**
 * Product Group is a 'holder' for Products within the CMS
 * It contains functions for versioning child products.
 */

/**
 * ### @@@@ START REPLACEMENT @@@@ ###
 * WHY: automated upgrade
 * OLD:  extends DataExtension (ignore case)
 * NEW:  extends DataExtension (COMPLEX)
 * EXP: Check for use of $this->anyVar and replace with $this->anyVar[$this->getOwner()->ID] or consider turning the class into a trait
 * ### @@@@ STOP REPLACEMENT @@@@ ###.
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
                        'List ' . $this->getOwner()->Title . ' on TradeMe?',
                        $this->getOwner()->dbObject('ListProductsOnTradeMe')->enumValues()
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
        $this->getOwner()->updateChildGroupsForTradeMe();
    }

    public function getCalculatedTradeMeCategory(): int
    {
        $parent = $this->owner;
        while ($parent) {
            if ($parent->TradeMeCategoryID) {
                return $parent->TradeMeCategoryID;
            }
            $parent = ProductGroup::get_by_id($parent->ParentID);
        }

        return 0;
    }

    public function CalculatedTradeMeCategoryWithDefaultAndAdjustments(): int
    {
        return $this->getOwner()->getCalculatedTradeMeCategory();
    }

    public function updateChildGroupsForTradeMe()
    {
        $myValue = $this->getOwner()->ListProductsOnTradeMe;
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
        if (true === $hasUpdate) {
            $children = ProductGroup::get()->filter(['ParentID' => $this->getOwner()->ID]);
            foreach ($children as $child) {
                if ($child->ListProductsOnTradeMe !== $myValue) {
                    $child->ListProductsOnTradeMe = $myValue;
                    $child->writeToStage(Versioned::DRAFT);
                    $child->publishRecursive();
                }
            }
        }
    }
}
