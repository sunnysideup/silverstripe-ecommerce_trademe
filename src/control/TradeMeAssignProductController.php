<?php

class TradeMeAssignProductController extends TradeMeAssignGroupController
{
    /**
     * @var string
     */
    private static $url_segment = 'admin/set-trade-me-products';

    private static $product_filter = [];

    private static $template = 'TradeMeAssignProductController_Content';

    protected $potentiallyUnsetDefaulData = [];

    public function Form()
    {
        $fields = new FieldList();
        $list = $this->getListForForm();
        foreach ($list as $product) {
            $name = '___PRODUCT___'.$product->ID;
            $fields->push(
                ReadonlyField::create(
                    'HEADER'.$name,
                    '<a href="'.$product->Link().'">✎</a>',
                    DBField::create_field('HTMLText', '<a href="'.$product->CMSEditLink().'">'.$product->Title.'</a>')
                )->setRightTitle(
                    '» ' . TradeMeCategories::get_title_from_id($product->getCalculatedTradeMeCategory()).
                    ''
                )
            );
            $fields->push(
                OptionSetField::create(
                    'ShowOnTradeMe'.$name,
                    'Include on TradeMe?',
                    $this->getListProductsOnTradeMeOptions()
                )->setValue($product->ShowOnTradeMe)
            );
            $fields->push(
                LiteralField::create(
                    'HR'.$name.'HR',
                    '<hr />'
                )
            );
        }

        foreach($this->getHiddenFields() as $hiddenField) {
            $fields->push($hiddenField);
        }

        $actions = $this->getFormActions();

        $form = new Form($this, 'Form', $fields, $actions);

        return $form;
    }

    protected function getListProductsOnTradeMeOptions() : array
    {
        return DataObject::get_one(Product::class)->dbObject('ShowOnTradeMe')->enumValues();
    }

    protected function getListForFormInner():DataList
    {
        $list = TradeMeAssignProductController::base_list();
        if ($this->getParams['parentid']) {
            $list = $list->filter(['ParentID' => $this->getParams['parentid']]);
        }
        if ($this->getParams['filter']) {
            $list = $list->filter(
                [
                    'ShowOnTradeMe' => $this->getParams['filter']
                ]
            );
        }
        if(! ($this->getParams['parentid'] || $this->getParams['filter'])) {
            $list = $list->filter(
                [
                    'ShowOnTradeMe' => 'always'
                ]
            );
        }

        return $list;
    }

    public static function base_list() :DataList
    {
        $list = Product::get()->filter(['AllowPurchase' => true]);
        $filter = Config::inst()->get('TradeMeAssignProductController', 'product_filter');
        if(! empty($filter)) {
            $list = $list->filter($filter);
        }

        return $list;
    }

    public function getProductGroup()
    {
        return $this->productGroup;
    }

    protected function setShowValue()
    {
        $this->getParams['parentid'] = intval($this->request->requestVar('parentid'));
        $this->productGroup = ProductGroup::get()->byID($this->getParams['parentid']);
        if($this->getParams['parentid'] && ! $this->productGroup) {
            return $this->httpError(404, 'Could not find category with ID = '.$this->getParams['parentid']);
        }
        $this->getParams['filter'] = intval($this->request->requestVar('showvaluefilter'));
        if($this->getParams['filter'] && ! in_array($this->getParams['filter'], $this->getListProductsOnTradeMeOptions(), true)) {
            return $this->httpError(404, 'Could not find a filter: '.$this->getParams['filter']);
        }
    }

    public function Title()
    {
        return 'TradeMe Settings for "'.$this->productGroup->Title.'"';
    }

    public function save($data, $form)
    {
        $data = array_merge($this->potentiallyUnsetDefaulData, $data);
        $updateCount = 0;
        foreach($data as $key => $value) {
            $array = explode('___', $key);
            $type = $array[0];
            if(isset($array[1]) && $array[1] === 'PRODUCT') {
                $productID = $array[2];
                $product = Product::get()->byID($productID);
                if($product) {
                    if(isset($array[0]) && $array[0] === 'IncludeOnTradeMe') {
                        $value = intval($value);
                        if( (bool)$product->IncludeOnTradeMe !== (bool) $value) {
                            $product->IncludeOnTradeMe = $value;
                            $product->writeToStage('Stage');
                            $product->publish('Stage', 'Live');
                            $updateCount++;
                        }
                    }
                    if(isset($array[0]) && $array[0] === 'ShowOnTradeMe') {
                        if($product->ShowOnTradeMe !== $value) {
                            $product->ShowOnTradeMe = $value;
                            $product->writeToStage('Stage');
                            $product->publish('Stage', 'Live');
                            $updateCount++;
                        }
                    }
                } else {
                    user_error('Could not find Category based on '.$key);
                }
            }
        }

        if ($updateCount) {
            $form->sessionMessage('Updated '.$updateCount . ' fields.', 'good');
        }

        return $this->redirectBack();
    }

}
