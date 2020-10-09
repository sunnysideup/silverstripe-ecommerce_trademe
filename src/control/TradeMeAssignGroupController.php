<?php

class TradeMeAssignGroupController extends Controller
{


    /**
     * @var string
     */
    private static $url_segment = 'set-trade-me-categories';

    private static $allowed_actions = [
        'index' => 'ADMIN',
        'save' => 'ADMIN',
        'saveandexport' => 'ADMIN',
        'Form' => 'ADMIN',
    ];

    private static $create_trademe_csv_task_class_name = CreateTradeMeCSVTask::class;

    private static $group_filter = [];

    private static $template = 'TradeMeAssignGroupController_Content';

    public function init()
    {
        parent::init();
        if(!Permission::check('ADMIN')) {
            return Security::permissionFailure($this);
        }
        $this->setShowValue();
    }

    public function index($request)
    {
        return $this->renderWith($this->Config()->get('template'));
    }

    protected $showValue = '';

    protected function getListProductsOnTradeMeOptions() : array
    {
        return ProductGroup::get()->first()->dbObject('ListProductsOnTradeMe')->enumValues();
    }

    protected function setShowValue()
    {
        $this->showValue = $this->request->requestVar('showvalue');
        if($this->showValue) {
            $options = $this->getListProductsOnTradeMeOptions();
            if(! in_array($this->showValue, $options)) {
                $this->showValue = '';
                return $this->httpError('404', 'Category does not exist');
            }
        }
    }

    public function Link($action = null) {
        return Controller::join_links(Director::baseURL(), $this->RelativeLink($action));
    }


    public function RelativeLink($action = null)
    {
        return self::my_link($action);
    }

    public static function my_link($action = null)
    {
        return '/' . Controller::join_links(Config::inst()->get(get_called_class(), 'url_segment'), $action);
    }

    protected function getListForForm()
    {
        $list = TradeMeAssignGroupController::base_list();
        if($this->showValue) {
            $list = $list->filter(['ListProductsOnTradeMe' => $this->showValue]);
        }
        return $list;
    }

    public static function base_list() : DataList
    {
        $list = ProductGroup::get();
        $filter = Config::inst()->get('TradeMeAssignGroupController', 'group_filter');
        if(! empty($filter)) {
            $list = $list->filter($filter);
        }

        return $list;
    }
    public function Form()
    {
        $fields = new FieldList();
        $fieldListSortable = [];
        $options = $this->getListProductsOnTradeMeOptions();
        foreach ($this->getListForForm() as $group) {
            $productList = TradeMeAssignProductController::base_list();
            $productList = $productList->filter(['ParentID' => $group->ID]);
            $productCount = $productList->count();
            if($productCount){
                $productLink = TradeMeAssignProductController::my_link().'?showvalue='.$group->ID;
                $name = '___GROUP___'.$group->ID;
                $breadcrumb = $group->Breadcrumbs();
                $breadcrumbRaw = $breadcrumb->RAW();
                $breadcrumbClean = strip_tags($breadcrumbRaw);
                $fieldListSortable[$breadcrumbClean] = new CompositeField();
                $fieldListSortable[$breadcrumbClean]->push(
                    ReadonlyField::create(
                        'HEADER'.$name,
                        '<a href="'.$group->CMSEditLink().'">✎</a>',
                        DBField::create_field('HTMLText', $breadcrumbRaw . ' » <a href="'.$productLink.'">'.$productCount.' Potential Products</a>')
                    )->setRightTitle(
                        '» ' . TradeMeCategories::get_title_from_id($group->getCalculatedTradeMeCategory()).
                        ''
                    )
                );
                $fieldListSortable[$breadcrumbClean]->push(
                    DropdownField::create(
                        'TYPE'.$name,
                        '',
                        $options
                    )->setValue($group->ListProductsOnTradeMe)
                );
                $fieldListSortable[$breadcrumbClean]->push(
                    LiteralField::create(
                        'HR'.$name.'HR',
                        '<hr />'
                    )
                );
            }
        }
        ksort($fieldListSortable);
        foreach($fieldListSortable as $fieldListSortableField) {
            $fields->push($fieldListSortableField);
        }
        $actions = new FieldList(
            FormAction::create('save', 'Save Changes'),
            FormAction::create('saveandexport', 'Save and Start Upload Process ...')
        );
        $fields->push(HiddenField::create('showvalue')->setValue($this->showValue));

        $form = new Form($this, 'Form', $fields, $actions);

        return $form;
    }


    public function Title()
    {
        return 'Set TradeMe Categories';
    }

    public function saveandexport($data, $form)
    {
        $this->saveInner($data, $form);
        $this->redirect('dev/tasks/'. $this->Config()->get('create_trademe_csv_task_class_name'));
    }

    public function save($data, $form)
    {
        $this->saveInner($data, $form);
        return $this->redirectBack();
    }

    protected function saveInner($data, $form)
    {
        $updateCount = 0;
        foreach($data as $key => $value) {
            $array = explode('___', $key);
            $type = $array[0];
            if(isset($array[1]) && $array[1] === 'GROUP') {
                $groupID = $array[2];
                $group = ProductGroup::get()->byID($groupID);
                if($group) {
                    if(isset($array[0]) && $array[0] === 'TYPE') {
                        if($group->ListProductsOnTradeMe !== $value) {
                            $group->ListProductsOnTradeMe = $value;
                            $group->writeToStage('Stage');
                            $group->publish('Stage', 'Live');
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
    }

    public function getFilterLinks() :ArrayList
    {
        $al =  ArrayList::create();
        foreach($this->getListProductsOnTradeMeOptions() as $option) {
            $array = [
                'Link' => $this->Link().'?showvalue=' . $option,
                'LinkingMode' => $this->showValue === $option ? 'current' : 'link',
                'Title' => ucfirst($option)
            ];
            $al->push(ArrayData::create($array));
        }
        return $al;
    }
}
