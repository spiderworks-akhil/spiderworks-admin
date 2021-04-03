<?php

namespace Spiderworks\Webadmin\Models;

use Spiderworks\Webadmin\Models\BaseModel as Model;
use Spiderworks\Webadmin\Traits\ValidationTrait;

class Category extends Model
{
    use ValidationTrait {
        ValidationTrait::validate as private parent_validate;
    }
    
    public function __construct() {
        
        parent::__construct();
        $this->__validationConstruct();
    }

    protected $table = 'categories';

    protected $fillable = [
        'slug', 'name', 'category_type', 'short_description', 'description', 'title', 'parent_id', 'banner_image_id', 'featured_image_id', 'browser_title', 'meta_description', 'meta_keywords', 'top_description', 'bottom_description', 'og_title', 'og_description', 'og_image_id', 'faq_id', 'is_featured', 'status'
    ];

    protected $dates = ['created_at','updated_at'];

    protected function setRules() {

        $this->val_rules = array(
            'name' => 'required|max:250',
            'slug' => 'required|max:250|unique:categories,slug,ignoreId',
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

    public function parent()
    {
        return $this->belongsTo('Spiderworks\Webadmin\Models\Category', 'parent_id');
    }

    public function banner_image()
    {
    	return $this->belongsTo('Spiderworks\Webadmin\Models\Media', 'banner_image_id');
    }

    public function featured_image()
    {
    	return $this->belongsTo('Spiderworks\Webadmin\Models\Media', 'featured_image_id');
    }

    public function og_image()
    {
    	return $this->belongsTo('Spiderworks\Webadmin\Models\Media', 'og_image_id');
    }

    public function menu()
    {
        return $this->morphOne('Spiderworks\Webadmin\Models\MenuItem', 'linkable');
    }

    public function children()
    {
        return $this->hasMany('Spiderworks\Webadmin\Models\Category', 'parent_id', 'id');
    }
}
