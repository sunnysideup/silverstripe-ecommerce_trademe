<?php

class TradeMeAssignProductController extends TradeMeAssignGroupController
{
    /**
     * @var string
     */
    private static $url_segment = 'set-trade-me-products';

    private static $product_filter = [];

    private static $template = 'TradeMeAssignProductController_Content';

    public function init()
    {
        parent::init();
        if(!Permission::check('ADMIN')) {
            return Security::permissionFailure($this);
        }
    }

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
                CheckboxField::create(
                    'ShowOnTradeMe'.$name,
                    'Include on TradeMe?'
                )->setValue($product->ShowOnTradeMe)
            );
            $fields->push(
                LiteralField::create(
                    'HR'.$name.'HR',
                    '<hr />'
                )
            );
        }

        $actions = new FieldList(
            FormAction::create('save', 'Save Changes'),
            FormAction::create('saveandexport', 'Save and Start Upload Process ...')
        );
        $fields->push(HiddenField::create('showvalue')->setValue($this->showValue));
        $form = new Form($this, 'Form', $fields, $actions);

        return $form;
    }

    protected function getListForForm():DataList
    {
        $list = TradeMeAssignProductController::base_list();
        if($this->showValue) {
            $list = $list->filter(['ParentID' => $this->showValue]);
        } else {
            $list = $list->filterAny(
                [
                    'ShowOnTradeMe' => true
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
        $this->showValue = intval($this->request->requestVar('showvalue'));
        $this->productGroup = ProductGroup::get()->byID($this->showValue);
        if(! $this->productGroup) {
            return $this->httpError(404, 'Could not find category with ID = '.$this->showValue);
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
