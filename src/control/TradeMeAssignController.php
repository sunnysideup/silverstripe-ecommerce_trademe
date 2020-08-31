<?php

class TradeMeAssignController extends Controller
{

    private static $allowed_actions = [
        'index' => 'ADMIN',
        'save' => 'ADMIN',
        'Form' => 'ADMIN',
    ];


    public function index()
    {
        return [];
    }

    public function Link($action = null) {
		return Controller::join_links(Director::baseURL(), $this->RelativeLink($action));
	}


    public function RelativeLink($action = null)
    {
        return Controller::join_links('set-trade-me-categories', $action);
    }

    public function Form()
    {
        $fields = new FieldList();
        $list = TradeMeCategories::get_categories();
        foreach (ProductGroup::get() as $group) {
            $name = '___GROUP___'.$group->ID;
            $fields->push(
                ReadonlyField::create(
                    'HEADER'.$name,
                    $group->singular_name(),
                    $group->Breadcrumbs()
                    )
            );
            $fields->push(
                DropdownField::create(
                    'DATA'.$name,
                    'Trade Me Category',
                    $list
                )->setValue($group->TradeMeCategoryID)
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
        $updateArray = [];
        foreach($data as $key => $value) {
            $array = explode('___', $key);
            $type = $array[0];
            if(isset($array[0]) && $array[0] === 'DATA') {
                if(isset($array[1]) && $array[1] === 'GROUP') {
                    $value = intval($value);
                    $groupID = $array[2];
                    $group = ProductGroup::get()->byID($groupID);
                    if($group) {
                        if($group->TradeMeCategoryID !== $value) {
                            $group->TradeMeCategoryID = $value;
                            $group->writeToStage('Stage');
                            $group->publish('Stage', 'Live');
                            $group->TradeMeCategoryID;
                            $updateArray[] = 'Updated '.$group->Title;
                        }
                    } else {
                        user_error('Could not find Category based on '.$key);
                        die('sdfsadf');
                    }
                }
            }
        }
        if(count($updateArray)) {
            $form->sessionMessage('Saved '.implode(',', $updateArray) . '.', 'good');
        }

        return $this->redirectBack();

    }
}
