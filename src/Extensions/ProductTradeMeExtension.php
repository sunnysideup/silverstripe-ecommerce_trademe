<?php

namespace Sunnysideup\EcommerceTrademe\Extensions;

use Extension;
use FieldList;
use OptionsetField;
use TradeMeCategories;
use UploadField;
use TradeMeGenericCmsFieldsProvider;
use ProductGroup;
use Config;
use EcommerceDBConfig;




/**
  * ### @@@@ START REPLACEMENT @@@@ ###
  * WHY: automated upgrade
  * OLD:  extends Extension (ignore case)
  * NEW:  extends Extension (COMPLEX)
  * EXP: Check for use of $this->anyVar and replace with $this->anyVar[$this->owner->ID] or consider turning the class into a trait
  * ### @@@@ STOP REPLACEMENT @@@@ ###
  */
class ProductTradeMeExtension extends Extension
{
    /**
     * stadard SS declaration
     * @var array
     */
    private static $db = [
        'TradeMeCategoryID' => 'Int',
        'ShowOnTradeMe' => 'Enum("follow category, always, never", "follow category")',
    ];

    /**
     * stadard SS declaration
     * @var array
     */
    private static $has_one = [

/**
  * ### @@@@ START REPLACEMENT @@@@ ###
  * WHY: automated upgrade
  * OLD:  => 'Image' (case sensitive)
  * NEW:  => 'Image' (COMPLEX)
  * EXP: you may want to add ownership (owns)
  * ### @@@@ STOP REPLACEMENT @@@@ ###
  */
        'TradeMeImage' => 'Image',
    ];

    /**
     * stadard SS declaration
     * @var array
     */
    private static $indexes = [
        'ShowOnTradeMe' => true
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
     *
     * @var int
     */
    private static $trade_me_title_char_limit = 50;

    /**
     *
     * @var int
     */
    private static $trade_me_title_description_limit = 2048;

    /**
     * stadard SS method
     * @return FieldList
     */
    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldsToTab(
            'Root.TradeMe',
            array_merge(
                [
                    $listOptions = OptionsetField::create(
                        'ShowOnTradeMe',
                        'Show on TradeMe?',
                        $this->owner->dbObject('ShowOnTradeMe')->enumValues()
                    ),
                    TradeMeCategories::categories_field(),
                    TradeMeCategories::calculated_categories_field($this->owner),
                    UploadField::create('TradeMeImage', 'TradeMeImage')
                        ->setDescription('Recommended is a minimum size of 800px wide by 600px high.')
                ],
                TradeMeGenericCmsFieldsProvider::get_fields($this->owner->Parent(), true)
            )
        );
        $parent = $this->owner->Parent();
        if($parent && $parent->exists()) {
            $listOptions->setDescription('
                Currently this product\'s main category <strong>'.$parent->Title.'</strong>
                it is set to include include <strong>'.strtoupper($parent->ListProductsOnTradeMe).'</strong>
                if its products.
            ');
        }
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
            $parent = ProductGroup::get()->byID($parent->owner->ParentID);
        }

        return 0;
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
            $limit = Config::inst()->get('ProductTradeMeExtension', 'trade_me_title_char_limit');
            $result = substr($result, 0, $limit - 1);
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

        //merge content
        $items = array_filter([$intro, $content]);
        $content = implode('<br /><br />', $items);

        //replacers
        $content = str_replace('&', ' and ', $content);
        $content = str_replace('<p>', '<br />', $content);
        $content = str_replace('</p>', '<br />', $content);

        //strip tags
        $content = strip_tags($content, '<br>') ?: '';


        //trim
        $result = trim($content);
        $result = trim($result, '<br><br />');
        $result = trim($result, '<br><br />');
        $result = str_replace('<br />', '<br>', $result);

        //limit
        if ($checkLimit) {
            $limit = $limit = Config::inst()->get('ProductTradeMeExtension', 'trade_me_title_description_limit');
            $result = substr($result, 0, $limit - 1);
        }

        return (string) $result;
    }

}
