<?php

namespace Spiderworks\Webadmin\Models;

use Spiderworks\Webadmin\Models\BaseModel as Model;
use Spiderworks\Webadmin\Traits\ValidationTrait;

class FrontendPage extends Model
{
    use ValidationTrait {
        ValidationTrait::validate as private parent_validate;
    }
    
    public function __construct() {
        
        parent::__construct();
        $this->__validationConstruct();
    }
    
    protected $table = 'frontend_pages';

     protected $fillable = array('slug', 'name', 'title', 'content', 'featured_image_id', 'banner_image_id', 'browser_title', 'meta_description', 'meta_keywords', 'top_description', 'bottom_description', 'og_title', 'og_description', 'og_image_id', 'extra_css', 'extra_js', 'faq_id', 'status');

    protected $dates = ['created_at','updated_at'];

    protected function setRules() {

        $this->val_rules = array(
        );
    }

    protected function setAttributes() {
        $this->val_attributes = array(
        );
    }

    public function faq()
    {
        return $this->belongsTo('Spiderworks\Webadmin\Models\Faq', 'faq_id');
    }

    public function featured_image()
    {
    	return $this->belongsTo('Spiderworks\Webadmin\Models\Media', 'featured_image_id');
    }

    public function banner_image()
    {
    	return $this->belongsTo('Spiderworks\Webadmin\Models\Media', 'banner_image_id');
    }

    public function og_image()
    {
    	return $this->belongsTo('Spiderworks\Webadmin\Models\Media', 'og_image_id');
    }

    public function menu()
    {
        return $this->morphOne('Spiderworks\Webadmin\Models\MenuItem', 'linkable');
    }
}
