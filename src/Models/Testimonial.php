<?php

namespace Spiderworks\Webadmin\Models;

use Spiderworks\Webadmin\Models\BaseModel as Model;
use Spiderworks\Webadmin\Traits\ValidationTrait;

class Testimonial extends Model
{
    use ValidationTrait {
        ValidationTrait::validate as private parent_validate;
    }
    
    public function __construct() {
        
        parent::__construct();
        $this->__validationConstruct();
    }

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'testimonials';


    protected $fillable = array('name','comment','comment_type','featured_image_id', 'youtube_link', 'designation', 'video_link_id', 'is_featured');

    


    protected function setRules() {

        $this->val_rules = array(
            'name' => 'required|max:250',
            'youtube_link' => 'required_if:comment_type,==,Youtube Video',
            'video_link_id' => 'required_if:comment_type,==,Video From Computer',
            'comment' => 'required_if:comment_type,==,Text',
        );
    }

    protected function setAttributes() {
        $this->val_attributes = array(
        );
    }

    public function featured_image()
    {
        return $this->belongsTo('Spiderworks\Webadmin\Models\Media', 'featured_image_id');
    }

    public function video()
    {
        return $this->belongsTo('Spiderworks\Webadmin\Models\Media', 'video_link_id');
    }
}
