<?php

class TradeMeAssignGroupController extends Controller implements PermissionProvider
{



    /**
     * @var string
     */
    private static $url_segment = 'admin/set-trade-me-categories';

    private static $allowed_actions = [
        'index' => 'CMS_ACCESS_TRADE_ME',
        'save' => 'CMS_ACCESS_TRADE_ME',
        'saveandexport' => 'CMS_ACCESS_TRADE_ME',
        'Form' => 'CMS_ACCESS_TRADE_ME',
    ];

    private static $create_trademe_csv_task_class_name = CreateTradeMeCsvTask::class;

    private static $group_filter = [];

    private static $template = 'TradeMeAssignGroupController_Content';

    public function init()
    {
        parent::init();
        if ( !Permission::check('CMS_ACCESS_TRADE_ME')) {
            return Security::permissionFailure($this);
        }
        $this->setShowValue();
    }

    public function index($request)
    {
        return $this->renderWith($this->Config()->get('template'));
    }

    protected $getParams = '';

    protected function getListProductsOnTradeMeOptions() : array
    {
        return DataObject::get_one(ProductGroup::class)->dbObject('ListProductsOnTradeMe')->enumValues();
    }

    protected function setShowValue()
    {
        $this->getParams = $this->request->requestVars;
        if(! empty($this->getParams['filter'])) {
            $options = $this->getListProductsOnTradeMeOptions();
            if(! in_array($this->getParams['filter'], $options)) {
                $this->getParams['filter'] = '';
                return $this->httpError('404', 'Category does not exist');
            }
        }
    }

    public function Link($action = null) {
        return Director::absoluteURL($this->RelativeLink($action));
    }

    public function RelativeLink($action = null)
    {
        return self::my_link($action, $this->getParams);
    }

    public static function my_link($action = null, $getParams = [])
    {
        $link = '/' . Controller::join_links(Config::inst()->get(get_called_class(), 'url_segment'), $action);
        $array = array_filter($getParams);
        if(! empty($array)) {
            $link = '?' . implode('&amp;', $array);
        }

        return $link;

    }

    public function getListForForm():PaginatedList
    {
        return PaginatedList(
            $this->getListForFormInner(),
            $this->getRequest()
        );
    }

    protected function getListForFormInner():DataList
    {
        $list = TradeMeAssignGroupController::base_list();
        if($this->getParams['filter']) {
            $list = $list->filter(['ListProductsOnTradeMe' => $this->getParams['filter']]);
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
                $productLink = TradeMeAssignProductController::my_link().'?parentid='.$group->ID;
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

        foreach($this->getHiddenFields() as $hiddenField) {
            $fields->push($hiddenField);
        }

        $actions = $this->getFormActions();

        $form = new Form($this, 'Form', $fields, $actions);

        return $form;
    }

    protected function getFormActions() : FieldList
    {
        return new FieldList(
            FormAction::create('save', 'Save Changes'),
            FormAction::create('saveandexport', 'Save and Start Upload Process ...')
        );
    }


    protected function getHiddenFields() : array
    {
        $arrayOfFields = [];
        $array = array_filter($this->getParams);
        foreach(array_keys($array) as $keys) {
            $arrayOfFields[] = HiddenField::create($key)->setValue($this->getParams[$key]);
        }
        return $arrayOfFields;
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
        $array = [
            'Link' => $this->Link(),
            'LinkingMode' => $this->getParams['filter'] ? 'link' : 'current',
            'Title' => 'No Filter',
        ];
        $al->push(ArrayData::create($array));

        $filter = $this->getParams['filter'];
        unset($this->getParams['filter']);
        foreach($this->getListProductsOnTradeMeOptions() as $option) {
            $array = [
                'Link' => $this->addLinkParameters($this->Link().'?filter=' . $option),
                'LinkingMode' => $this->getParams['filter'] === $option ? 'current' : 'link',
                'Title' => ucfirst($option),
            ];
            $al->push(ArrayData::create($array));
        }
        $this->getParams['filter'] = $filter;
        return $al;
    }


    public function providePermissions() : array
    {
        return [
            "CMS_ACCESS_TRADE_ME" => [
                'name' => 'Trade Me',
                'category' => _t('Permission.CMS_ACCESS_CATEGORY', 'CMS Access'),
                'help' => 'Export products to TradeMe',
            ]
        ];
    }
}
