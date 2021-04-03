<?php

namespace Spiderworks\Webadmin\Models;

use Spiderworks\Webadmin\Models\BaseModel as Model;
use Spiderworks\Webadmin\Traits\ValidationTrait;

class Setting extends Model
{

    use ValidationTrait {
        ValidationTrait::validate as private parent_validate;
    }

    public function __construct() {

        parent::__construct();
        $this->__validationConstruct();
    }

    protected $table = 'settings';

    protected $fillable = ['code', 'value_text', 'setting_type', 'value_image_id'];

    public $uploadPath = array(
        'settings' => 'uploads/settings/',
    );

    protected function setRules() {
        $this->val_rules = [
            'code' => 'required|alpha_dash|unique:settings,code,ignoreId',
            'value_text' => 'required_if:setting_type,==,Text',
            'value_image_id' => 'required_if:setting_type,==,Image',
        ];
    }

    protected function setAttributes() {
        $this->val_attributes = [
            'code' => 'key name',
            'value_text' => 'key value',
            'value_image_id' => 'key image',
        ];
    }

    public function validate($data = null, $ignoreId = 'NULL') {
        if( isset($this->val_rules['code']) )
        {
            $this->val_rules['code'] = str_replace('ignoreId', $ignoreId, $this->val_rules['code']);
        }
        return $this->parent_validate($data);
    }
    public function media()
    {
        return $this->belongsTo('Spiderworks\Webadmin\Models\Media', 'value_image_id');
    }
}
