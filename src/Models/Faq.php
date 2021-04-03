<?php

namespace Spiderworks\Webadmin\Models;

use Spiderworks\Webadmin\Models\BaseModel as Model;
use Spiderworks\Webadmin\Traits\ValidationTrait;

class Faq extends Model
{
    use ValidationTrait {
        ValidationTrait::validate as private parent_validate;
    }
    
    public function __construct() {
        
        parent::__construct();
        $this->__validationConstruct();
    }

    protected $table = 'faqs';

    protected $fillable = array('name', 'status');

    
    protected function setRules() {

        $this->val_rules = array(      
        );
    }

    protected function setAttributes() {
        $this->val_attributes = array(  
            
        );
    }
    public function question_answers()
    {
        return $this->hasMany('Spiderworks\Webadmin\Models\FaqQuestionAnswer', 'faq_id')->orderBy('display_order', 'ASC');
    }
    
}
