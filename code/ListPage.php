<?php

class ListPage extends Page {
    
    private static $db = array (
        'ToggleEffect' => 'Boolean',
        'BottomContent' => 'HTMLText'
    );
    
    private static $has_many = array (
        'ListItems' => 'ListItem',
        'ListCategories' => 'ListCategory'
    );

    private static $defaults = array (
        "ToggleEffect" => true
    );
    
    private static $icon = "listpage/images/listpage";
    
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->addFieldToTab('Root.Main', HTMLEditorField::create('BottomContent')->setTitle('Content for below the list items'),'Metadata');
        $ListItemGridField = new GridField(
            'ListItems',
            'List Item',
            $this->ListItems(),
            GridFieldConfig::create()
                ->addComponent(new GridFieldToolbarHeader())
                ->addComponent(new GridFieldAddNewButton('toolbar-header-right'))
                ->addComponent(new GridFieldSortableHeader())
                ->addComponent(new GridFieldDataColumns())
                ->addComponent(new GridFieldPaginator(50))
                ->addComponent(new GridFieldEditButton())
                ->addComponent(new GridFieldDeleteAction())
                ->addComponent(new GridFieldDetailForm())
                ->addComponent(new GridFieldSortableRows('SortID'))
        );
        $fields->addFieldToTab("Root.ListItems", $ListItemGridField);
        $ListCategoryGridField = new GridField(
            'ListCategories',
            'List Category',
            $this->ListCategories(),
            GridFieldConfig::create()
                ->addComponent(new GridFieldToolbarHeader())
                ->addComponent(new GridFieldAddNewButton('toolbar-header-right'))
                ->addComponent(new GridFieldSortableHeader())
                ->addComponent(new GridFieldDataColumns())
                ->addComponent(new GridFieldPaginator(50))
                ->addComponent(new GridFieldEditButton())
                ->addComponent(new GridFieldDeleteAction())
                ->addComponent(new GridFieldDetailForm())
                ->addComponent(new GridFieldSortableRows('SortID'))
        );
        $fields->addFieldToTab("Root.ListCategories", $ListCategoryGridField);
        $fields->addFieldToTab('Root.Options', HeaderField::create('ToggleDescription')->setTitle('Display Options'));
        $fields->addFieldToTab('Root.Options', CheckboxField::create('ToggleEffect')->setTitle('Use Toggle Effect'));
        return $fields;
    }   

}

class ListPage_Controller extends Page_Controller {

    public static function load_requirements() {
        Requirements::css("listpage/css/listpage.css");
        Requirements::javascript("listpage/js/functions.listpage.js");
    }

    public function init() {
        parent::init();
        self::load_requirements();
    }

    public function ListCategories() {
        $listcategoriesfiltered = new ArrayList();
        $listcategories = $this->getComponents('ListCategories');
        if($listcategories) {
            foreach($listcategories AS $listcategory) {
                if($listcategory->getComponents('ListItems')->count() > 0) {
                    $listcategoriesfiltered->push($listcategory); 
                }
            }
        }
        return $listcategoriesfiltered;
    }

    public function UncategorizedListItems() {
        $uncategorizedlistitems = new ArrayList();
        $listitems = $this->getComponents('ListItems');
        if($listitems) {
            foreach($listitems AS $listitem) {
                if($listitem->Category() == "Other") {
                    $uncategorizedlistitems->push($listitem); 
                }
            }
        }
        return $uncategorizedlistitems;
    }

    public function MoreThanOneListCategory() {
        if($this->ListCategories()->count() + $this->UncategorizedListItems()->count() > 1)
            return true;
    }
    
}

?>