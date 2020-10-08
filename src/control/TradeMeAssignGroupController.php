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
        'Form' => 'ADMIN',
    ];

    private static $filter = [];

	/**
	 * @var string
	 */
	private static $menu_title = 'Trade Me Products';

    public function init()
    {
        parent::init();
        if(!Permission::check('ADMIN')) {
            return Security::permissionFailure($this);
        }
    }

    public function index($request)
    {
        return $this->renderWith('TradeMeAssignProductController_Content');
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
        return Controller::join_links(self::$url_segment, $action);
    }

    public function Form()
    {
        $fields = new FieldList();
        $list = TradeMeCategories::get_categories();
        $options = ProductGroup::get()->first()->dbObject('ListProductsOnTradeMe')->enumValues();
        foreach (ProductGroup::get()->filter($this->Config()->get('filter')) as $group) {
            $name = '___GROUP___'.$group->ID;
            $fields->push(
                ReadonlyField::create(
                    'HEADER'.$name,
                    '',
                    $group->Breadcrumbs()
                )->setDescription('
                    <a href="'.$group->CMSEditLink().'">✎</a>'
                )
            );
            $fields->push(
                DropdownField::create(
                    'CATEGORY'.$name,
                    '',
                    $list
                )->setValue($group->TradeMeCategoryID)
            );
            $fields->push(
                DropdownField::create(
                    'TYPE'.$name,
                    '',
                    $options
                )->setValue($group->ListProductsOnTradeMe)
            );
            $fields->push(
                LiteralField::create(
                    'HR'.$name.'HR',
                    '<hr />'
                )
            );
        }
        $actions = new FieldList(
            FormAction::create('save', 'Update')
        );

        $form = new Form($this, 'Form', $fields, $actions);

        return $form;
    }


    public function Title()
    {
        return 'Set Trade Me Categories';
    }

    public function save($data, $form)
    {
        $updateCount = 0;
        foreach($data as $key => $value) {
            $array = explode('___', $key);
            $type = $array[0];
            if(isset($array[1]) && $array[1] === 'GROUP') {
                $groupID = $array[2];
                $group = ProductGroup::get()->byID($groupID);
                if($group) {
                    if(isset($array[0]) && $array[0] === 'CATEGORY') {
                        $value = intval($value);
                        if($group->TradeMeCategoryID !== $value) {
                            $group->TradeMeCategoryID = $value;
                            $group->writeToStage('Stage');
                            $group->publish('Stage', 'Live');
                            $updateCount++;
                        }
                    }
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

        return $this->redirectBack();

    }
}
