<?php

namespace Spiderworks\Webadmin\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $table = 'medias';

    protected $fillable = [
        'file_name', 'file_path', 'thumb_file_path', 'file_type', 'file_size', 'dimensions', 'media_type', 'title', 'description', 'alt_text', 'related_type', 'related_id'
    ];

    protected $dates = ['created_at','updated_at'];

    public $uploadPath = array(
        'media' => 'uploads/media/',
        'media_thumb' => 'uploads/media/thumb/',
    );
}
