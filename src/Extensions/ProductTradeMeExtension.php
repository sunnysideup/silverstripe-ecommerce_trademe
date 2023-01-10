<?php

namespace Sunnysideup\EcommerceTrademe\Extensions;

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Extension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\OptionsetField;
use Sunnysideup\Ecommerce\Model\Config\EcommerceDBConfig;
use Sunnysideup\Ecommerce\Pages\ProductGroup;
use Sunnysideup\EcommerceTrademe\Api\TradeMeCategories;
use Sunnysideup\EcommerceTrademe\Api\TradeMeGenericCmsFieldsProvider;

class ProductTradeMeExtension extends Extension
{
    /**
     * stadard SS declaration.
     *
     * @var array
     */
    private static $db = [
        'TradeMeCategoryID' => 'Int',
        'ShowOnTradeMe' => 'Enum("follow category, always, never", "follow category")',
    ];

    /**
     * stadard SS declaration.
     *
     * @var array
     */
    private static $has_one = [
        'TradeMeImage' => Image::class,
    ];

    private static $owns = [
        'TradeMeImage',
    ];

    private static $cascade_deletes = [
        'TradeMeImage',
    ];

    /**
     * stadard SS declaration.
     *
     * @var array
     */
    private static $indexes = [
        'ShowOnTradeMe' => true,
    ];

    /**
     * to identify.
     *
     * @var string
     */
    private static $trademe_group = '';

    /**
     * to identify.
     *
     * @var string
     */
    private static $trade_me_intro = '';

    /**
     * @var int
     */
    private static $trade_me_title_char_limit = 50;

    /**
     * @var int
     */
    private static $trade_me_title_description_limit = 2048;

    /**
     * stadard SS method.
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
                        $this->getOwner()->dbObject('ShowOnTradeMe')->enumValues()
                    ),
                    TradeMeCategories::categories_field(),
                    TradeMeCategories::calculated_categories_field($this->owner),
                    UploadField::create('TradeMeImage', 'TradeMeImage')
                        ->setDescription('Recommended is a minimum size of 800px wide by 600px high.')
                        ->setFolder('trade-me-product-images'),
                ],
                TradeMeGenericCmsFieldsProvider::get_fields($this->getOwner()->Parent(), true)
            )
        );
        $parent = $this->getOwner()->getParent();
        if ($parent && $parent->exists()) {
            $listOptions->setDescription('
                Currently this product\'s main category <strong>' . $parent->Title . '</strong>
                it is set to include include <strong>' . strtoupper($parent->ListProductsOnTradeMe) . '</strong>
                if its products.
            ');
        }
    }

    /**
     * looks at TradeMe Category from the product itself
     * and it not found goes up the line (parent , parent.parent, etc...)
     * to find the applicable trade me category.
     */
    public function getCalculatedTradeMeCategory(): int
    {
        $parent = $this->owner;
        while ($parent) {
            if ($parent instanceof ProductGroup) {
                $id = $parent->getCalculatedTradeMeCategory();
            } else {
                $id = $parent->TradeMeCategoryID;
            }
            if ($id) {
                return $id;
            }
            $parent = ProductGroup::get_by_id($parent->ParentID);
        }

        return 0;
    }

    /**
     * returns the right trade me category.
     */
    public function CalculatedTradeMeCategoryWithDefaultAndAdjustments(): int
    {
        $categoryID = $this->getOwner()->getCalculatedTradeMeCategory();

        if ($categoryID) {
            if ($this->getOwner()->hasMethod('getTradeMeCustomCategory')) {
                $categoryID = $this->getOwner()->getTradeMeCustomCategory($categoryID);
            }
        } else {
            $categoryID = Config::inst()->get(TradeMeCategories::class, 'trade_me_default');
        }

        return $categoryID;
    }

    /**
     * returns the title of the product for TradeMe.
     *
     * @param bool $checkLimit
     */
    public function getTradeMeTitle(?bool $checkLimit = true): string
    {
        $result = $this->getOwner()->Title;
        $result = str_replace('&', ' and ', $result);
        if ($checkLimit) {
            $limit = Config::inst()->get(ProductTradeMeExtension::class, 'trade_me_title_char_limit');
            $result = substr($result, 0, $limit - 1);
        }

        return (string) $result;
    }

    /**
     * @param bool $checkLimit
     */
    public function getTradeMeContent(?bool $checkLimit = true): string
    {
        $intro = EcommerceDBConfig::current_ecommerce_db_config()->TradeMeIntro;
        $content = $this->getOwner()->Content;

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
            $limit = $limit = Config::inst()->get(ProductTradeMeExtension::class, 'trade_me_title_description_limit');
            $result = substr($result, 0, $limit - 1);
        }

        return (string) $result;
    }
}
