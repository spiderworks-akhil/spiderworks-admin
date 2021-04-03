<?php

namespace Spiderworks\Webadmin\Models;

use Spiderworks\Webadmin\Models\BaseModel as Model;
use Spiderworks\Webadmin\Traits\ValidationTrait;

class Team extends Model
{
	use ValidationTrait {
        ValidationTrait::validate as private parent_validate;
    }
    
    public function __construct() {
        
        parent::__construct();
        $this->__validationConstruct();
    }

    protected $table = 'team';


    protected $fillable = array('department_id', 'slug', 'name', 'title', 'designation', 'short_description', 'content', 'featured_image_id', 'banner_image_id', 'browser_title', 'meta_description', 'meta_keywords', 'og_title', 'og_description', 'og_image_id', 'extra_css', 'priority', 'top_description', 'bottom_description', 'extra_js', 'status', 'facebook_link', 'twitter_link', 'linkedin_link', 'instagram_link', 'youtube_link', 'is_featured');

    protected $dates = ['created_at','updated_at'];

    protected function setRules() {

        $this->val_rules = array(
            'name' => 'required|max:250',
            'slug' => 'required|alpha_dash|unique:team,slug,ignoreId',
        );
    }

    protected function setAttributes() {
        $this->val_attributes = array(
        );
    }

    public function validate($data = null, $ignoreId = 'NULL') {
        $ignore_array = ['slug'];
        foreach($ignore_array as $ignore){
            $this->val_rules[$ignore] = str_replace('ignoreId', $ignoreId, $this->val_rules[$ignore]);
        }
        return $this->parent_validate($data);
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

    public function department()
    {
        return $this->belongsTo('Spiderworks\Webadmin\Models\Department', 'department_id');
    }

}
