<?php 
namespace Spiderworks\Webadmin\Models;

use Spiderworks\Webadmin\Models\BaseModel as Model;
use Spiderworks\Webadmin\Traits\ValidationTrait;

class SliderPhoto extends Model
{
    
    public function __construct() {
        
        parent::__construct();
    }

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'slider_photos';

    protected $fillable = array('sliders_id', 'media_id', 'crop_data', 'title', 'description', 'alt_text', 'button_text', 'button_link', 'button_link_target', 'button2_text', 'button2_link', 'button2_link_target');

    protected $dates = ['created_at','updated_at'];


    protected function setRules() {

        $this->val_rules = array(
        );
    }

    protected function setAttributes() {
        $this->val_attributes = array(
        );
    }

    public function slider() {
        return $this->belongsTo('Spiderworks\Webadmin\Models\Slider', 'sliders_id');
    }

    public function media() {
        return $this->belongsTo('Spiderworks\Webadmin\Models\Media', 'media_id');
    }

}
