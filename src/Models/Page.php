<?php

namespace Spiderworks\Webadmin\Models;

use Spiderworks\Webadmin\Models\BaseModel as Model;
use Spiderworks\Webadmin\Traits\ValidationTrait;

class Page extends Model
{
    use ValidationTrait {
        ValidationTrait::validate as private parent_validate;
    }

    public function __construct() {
        
        parent::__construct();
        $this->__validationConstruct();
    }

    protected $table = 'pages';


    protected $fillable = array('slug', 'name', 'title', 'content', 'parent_id', 'featured_image_id', 'banner_image_id', 'browser_title', 'meta_description', 'meta_keywords', 'top_description', 'bottom_description', 'og_title', 'og_description', 'og_image_id', 'extra_css', 'extra_js', 'faq_id', 'status');

    protected $dates = ['created_at','updated_at'];

    protected function setRules() {

        $this->val_rules = array(
            'name' => 'required|max:250',
            'slug' => 'required|max:250|unique:pages,slug,ignoreId',
            'content' => 'required',
        );
    }

    protected function setAttributes() {
        $this->val_attributes = array(
        );
    }

    public function validate($data = null, $ignoreId = 'NULL') {
        if( isset($this->val_rules['slug']) )
        {
            $this->val_rules['slug'] = str_replace('ignoreId', $ignoreId, $this->val_rules['slug']);
        }
        return $this->parent_validate($data);
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

    public function parent()
    {
        return $this->belongsTo('Spiderworks\Webadmin\Models\Page', 'parent_id');
    }

    public function children()
    {
        return $this->hasMany('Spiderworks\Webadmin\Models\Page', 'parent_id', 'id');
    }
}
