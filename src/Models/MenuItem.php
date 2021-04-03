<?php

namespace Spiderworks\Webadmin\Models;

use Spiderworks\Webadmin\Models\BaseModel as Model;
use Spiderworks\Webadmin\Traits\ValidationTrait;

class MenuItem extends Model
{
    use ValidationTrait {
        ValidationTrait::validate as private parent_validate;
    }
    
    public function __construct() {
        
        parent::__construct();
        $this->__validationConstruct();
    }

    protected $table = 'menu_items';

    protected $fillable = array('menu_id', 'title', 'original_title', 'url', 'menu_type', 'menu_nextable_id', 'linkable_id', 'linkable_type', 'menu_order', 'parent_id', 'target_blank');

    protected $dates = ['created_at','updated_at'];

    protected function setRules() {

        $this->val_rules = array(
        );
    }

    protected function setAttributes() {
        $this->val_attributes = array(
        );
    }

    public function linkable()
    {
        return $this->morphTo();
    }
}
