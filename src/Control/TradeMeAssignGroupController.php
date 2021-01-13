<?php

namespace Sunnysideup\EcommerceTrademe\Control;

use Controller;
use PermissionProvider;
use CreateTradeMeCsvTask;
use Permission;
use Security;
use DataObject;
use ProductGroup;
use Director;
use Config;
use PaginatedList;
use DataList;
use FieldList;
use CompositeField;
use OptionsetField;
use ReadonlyField;
use DBField;
use TradeMeCategories;
use LiteralField;
use Form;
use FormAction;
use HiddenField;
use ArrayList;
use ArrayData;


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


/**
  * ### @@@@ START REPLACEMENT @@@@ ###
  * OLD:     public function init() (ignore case)
  * NEW:     protected function init() (COMPLEX)
  * EXP: Controller init functions are now protected  please check that is a controller.
  * ### @@@@ STOP REPLACEMENT @@@@ ###
  */
    protected function init()
    {
        parent::init();
        if ( !Permission::check('CMS_ACCESS_TRADE_ME')) {
            return Security::permissionFailure($this);
        }
        $this->setGetParams();
    }

    public function index($request)
    {

/**
  * ### @@@@ START REPLACEMENT @@@@ ###
  * WHY: automated upgrade
  * OLD: ->RenderWith( (ignore case)
  * NEW: ->RenderWith( (COMPLEX)
  * EXP: Check that the template location is still valid!
  * ### @@@@ STOP REPLACEMENT @@@@ ###
  */
        return $this->RenderWith($this->Config()->get('template'));
    }

    protected $getParams = [];

    protected $getParamsDefaults = [
        'start' => 0,
        'filter' => 0,
        'parentid' => 0,
    ];

    protected function getListProductsOnTradeMeOptions() : array
    {
        return DataObject::get_one(ProductGroup::class)->dbObject('ListProductsOnTradeMe')->enumValues();
    }

    protected function setGetParams()
    {
        $potentials = $this->request->requestVars();
        foreach($this->getParamsDefaults as $key => $defaultValue) {
            $newValue = $potentials[$key] ?? $defaultValue;
            $this->getParams[$key] = $newValue;
        }

        if(! empty($this->getParams['filter'])) {
            $options = $this->getListProductsOnTradeMeOptions();
            if(! in_array($this->getParams['filter'], $options)) {
                $this->getParams['filter'] = '';
                return $this->httpError('404', 'Category does not exist');
            }
        }
    }

    public function getFilter()
    {
        return $this->getParams['filter'];
    }

    public function getFilterCount()
    {
        return $this->getListForForm()->count();
    }

    public function Link($action = null) {
        return Director::absoluteURL($this->RelativeLink($action), $this->getParams);
    }

    public function RelativeLink($action = null)
    {
        return self::my_link($action, $this->getParams);
    }

    public static function my_link($action = null, $getParams = [])
    {
        $link = '/' . Controller::join_links(Config::inst()->get(get_called_class(), 'url_segment'), $action);

        $link .= '?' . http_build_query($getParams);

        return $link;

    }

    public function getListForForm():PaginatedList
    {
        $list = PaginatedList::create(
            $this->getListForFormInner(),
            $this->getRequest()
        );
        $list->setPageLength(100);

        return $list;
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
                $productLink = TradeMeAssignProductController::my_link('', ['parentid' => $group->ID]);
                $name = '___GROUP___'.$group->ID;
                $breadcrumb = $group->Breadcrumbs();
                $breadcrumbRaw = $breadcrumb->RAW();
                $breadcrumbClean = strip_tags($breadcrumbRaw);
                $fieldListSortable[$breadcrumbClean] = new CompositeField();
                $fieldListSortable[$breadcrumbClean]->push(
                    OptionsetField::create(
                        'ListProductsOnTradeMe'.$name,
                        '',
                        $options
                    )
                        ->setValue($group->ListProductsOnTradeMe)
                        ->addExtraClass('float-left')
                );
                $fieldListSortable[$breadcrumbClean]->push(
                    ReadonlyField::create(
                        'HEADER'.$name,
                        '<a href="'.$group->CMSEditLink().'">✎</a>',
                        DBField::create_field('HTMLText', $breadcrumbRaw . ' » <a href="'.$productLink.'">Edit Individual Products ('.$productCount.')</a>')
                    )
                        ->setRightTitle(
                            '» ' . TradeMeCategories::get_title_from_id($group->getCalculatedTradeMeCategory())
                        )
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
        foreach($this->getParams as $key => $value) {
            $arrayOfFields[] = HiddenField::create($key)->setValue($value);
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

        $link = '/dev/tasks/'. $this->Config()->get('create_trademe_csv_task_class_name');

        return $this->redirect($link);
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
            if(count($array) === 3) {
                $field = $array[0];
                $type = $array[1];
                $groupId = intval($array[2]);
                if($field === 'ListProductsOnTradeMe' && $type === 'GROUP') {
                    $group = ProductGroup::get()->byID($groupId);
                    if($group) {
                        if($group->ListProductsOnTradeMe !== $value) {
                            $group->ListProductsOnTradeMe = $value;
                            $group->writeToStage('Stage');
                            $group->publish('Stage', 'Live');
                            $updateCount++;
                        }
                    } else {
                        user_error('Could not find Category based on '.$key);
                    }
                }
            }
        }
        if ($updateCount) {
            $form->sessionMessage('Updated '.$updateCount . ' fields.', 'good');
        }
    }

    public function getMainLinks()
    {
        $al =  ArrayList::create();


        // no filter
        $al->push(ArrayData::create([
            'Link' => TradeMeAssignGroupController::my_link(),
            'Title' => 'Categories',
            'IsCurrent' => static::class === TradeMeAssignGroupController::class,
        ]));
        $al->push(ArrayData::create([
            'Link' => TradeMeAssignProductController::my_link(),
            'Title' => 'Products',
            'IsCurrent' => static::class === TradeMeAssignProductController::class,
        ]));

        $this->extend('getMainLinksAdditional', $al);
        return $al;
    }

    public function getFilterLinks() :ArrayList
    {
        $al =  ArrayList::create();
        //reset filter first ...
        $currentStart = $this->getParams['start'] ?? 0;
        $this->getParams['start'] = 0;
        $currentFilter = $this->getParams['filter'] ?? '';
        $this->getParams['filter'] = '';

        // no filter
        $array = [
            'Link' => $this->Link(),
            'LinkingMode' => $currentFilter ? 'link' : 'current',
            'Title' => 'No Filter',
        ];
        $al->push(ArrayData::create($array));

        //loop through options
        foreach($this->getListProductsOnTradeMeOptions() as $option) {
            $this->getParams['filter'] = $option;
            $array = [
                'Link' => $this->Link(),
                'LinkingMode' => $option === $currentFilter ? 'current' : 'link',
                'Title' => ucfirst($option),
            ];
            $al->push(ArrayData::create($array));
        }

        //set filter back
        $this->getParams['start'] = $currentStart;
        $this->getParams['filter'] = $currentFilter;

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
