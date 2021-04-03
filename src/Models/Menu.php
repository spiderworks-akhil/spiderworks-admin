<?php

namespace Spiderworks\Webadmin\Models;

use Spiderworks\Webadmin\Models\BaseModel as Model;
use Spiderworks\Webadmin\Traits\ValidationTrait;

class Menu extends Model
{
    use ValidationTrait {
        ValidationTrait::validate as private parent_validate;
    }
    
    public function __construct() {
        
        parent::__construct();
        $this->__validationConstruct();
    }

    protected $table = 'menus';

    protected $fillable = array('name', 'position', 'title', 'status');

    protected $dates = ['created_at','updated_at'];

    protected function setRules() {

        $this->val_rules = array(
            'name' => 'required|max:250|unique:menus,name,ignoreId',
            'position' => 'required',
        );
    }

    protected function setAttributes() {
        $this->val_attributes = array(
            'name' => 'menu name',
            'position' => 'menu position',
        );
    }

    public function validate($data = null, $ignoreId = 'NULL') {
        if( isset($this->val_rules['name']) )
        {
            $this->val_rules['name'] = str_replace('ignoreId', $ignoreId, $this->val_rules['name']);
        }
        return $this->parent_validate($data);
    }

    public function menu_items()
    {
        return $this->hasMany('Spiderworks\Webadmin\Models\MenuItem');
    }

    public function parent_menu_items()
    {
        return $this->menu_items()->where('parent_id',0);
    }
}
