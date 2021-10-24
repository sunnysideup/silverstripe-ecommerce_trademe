<?php

namespace Sunnysideup\EcommerceTrademe\Control;

use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBField;
use Sunnysideup\Ecommerce\Pages\Product;
use Sunnysideup\Ecommerce\Pages\ProductGroup;
use Sunnysideup\EcommerceTrademe\Api\TradeMeCategories;

class TradeMeAssignProductController extends TradeMeAssignGroupController
{
    /**
     * @var string
     */
    private static $url_segment = 'tradmeadmin/set-trade-me-products';

    private static $product_filter = [];

    private static $template = 'TradeMeAssignProductController_Content';

    private static $allowed_actions = [
        'index' => 'CMS_ACCESS_TRADE_ME',
        'save' => 'CMS_ACCESS_TRADE_ME',
        'saveandexport' => 'CMS_ACCESS_TRADE_ME',
        'Form' => 'CMS_ACCESS_TRADE_ME',
    ];

    public function Form()
    {
        $fields = new FieldList();
        $list = $this->getListForForm();
        foreach ($list as $product) {
            $name = '___PRODUCT___' . $product->ID;
            $fields->push(
                OptionsetField::create(
                    'ShowOnTradeMe' . $name,
                    '',
                    $this->getListProductsOnTradeMeOptions()
                )
                    ->setValue($product->ShowOnTradeMe)
                    ->addExtraClass('float-left')
            );
            $parent = $product->ParentGroup();
            $fields->push(
                ReadonlyField::create(
                    'HEADER' . $name,
                    DBField::create_field('HTMLText', '<a href="' . $product->CMSEditLink() . '">✎</a>'),
                    DBField::create_field(
                        'HTMLText',
                        '<a href="' . $product->Link() . '">' . $product->InternalItemID . ' - ' . $product->Title . '</a>' .
                        ' (in <a href="' . $parent->CMSEditLink() . '">' . $parent->Title . '</a>)'
                    )
                )->setRightTitle(
                    '» ' . TradeMeCategories::get_title_from_id($product->getCalculatedTradeMeCategory()) .
                    ''
                )
            );
            $fields->push(
                LiteralField::create(
                    'HR' . $name . 'HR',
                    '<hr />'
                )
            );
        }

        foreach ($this->getHiddenFields() as $hiddenField) {
            $fields->push($hiddenField);
        }

        $actions = $this->getFormActions();

        return new Form($this, 'Form', $fields, $actions);
    }

    public static function base_list(): DataList
    {
        $list = Product::get()->filter(['AllowPurchase' => true]);
        $filter = Config::inst()->get(TradeMeAssignProductController::class, 'product_filter');
        if (! empty($filter)) {
            $list = $list->filter($filter);
        }

        return $list;
    }

    public function getProductGroup()
    {
        return $this->productGroup;
    }

    public function Title(): string
    {
        if ($this->productGroup) {
            return 'Select what Products in the "' . $this->productGroup->Title . '" category go to TradeMe';
        }

        return 'Select what Products go to TradeMe';
    }

    public function Description(): string
    {
        return 'Below is a list of all product categories on the site with their associated TradeMe Category.
        You can select the rules for each category in terms of what products are sent to TradeMe.';
    }

    /**
     * NOTE: this should not contain any redirects!
     *
     * @param array $data
     * @param Form  $form
     */
    public function saveInner($data, $form)
    {
        $updateCount = 0;
        foreach ($data as $key => $value) {
            $array = explode('___', $key);
            if (3 === count($array)) {
                $field = $array[0];
                $type = $array[1];
                $productID = intval($array[2]);
                if ('PRODUCT' === $type && 'ShowOnTradeMe' === $field) {
                    $product = Product::get()->byID($productID);
                    if ($product) {
                        if ($product->ShowOnTradeMe !== $value) {
                            $product->ShowOnTradeMe = $value;
                            $product->writeToStage('Stage');
                            $product->publish('Stage', 'Live');
                            ++$updateCount;
                        }
                    } else {
                        user_error('Could not find Product based on ' . $key);
                    }
                }
            }
        }

        if ($updateCount) {
            $form->sessionMessage('Updated ' . $updateCount . ' records.', 'good');
        }
    }

    protected function getListProductsOnTradeMeOptions(): array
    {
        return DataObject::get_one(Product::class)->dbObject('ShowOnTradeMe')->enumValues();
    }

    protected function getListForFormInner(): DataList
    {
        $list = TradeMeAssignProductController::base_list();
        if ($this->getParams['parentid']) {
            $list = $list->filter(['ParentID' => $this->getParams['parentid']]);
        }
        if ($this->getParams['filter']) {
            $list = $list->filter(
                [
                    'ShowOnTradeMe' => $this->getParams['filter'],
                ]
            );
        }

        return $list;
    }

    protected function setGetParams()
    {
        parent::setGetParams();
        $this->productGroup = ProductGroup::get()->byID($this->getParams['parentid']);
        if ($this->getParams['parentid'] && ! $this->productGroup) {
            return $this->httpError(404, 'Could not find category with ID = ' . $this->getParams['parentid']);
        }
    }
}
