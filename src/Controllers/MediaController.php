<?php

namespace Spiderworks\Webadmin\Controllers;

use Spiderworks\Webadmin\Controllers\BaseController as Controller;
use Spiderworks\Webadmin\Traits\ResourceTrait;

use Illuminate\Http\Request as HttpRequest;
use Spiderworks\Webadmin\Models\Media;
use View, Auth;

class MediaController extends Controller
{
    public function __construct()
    {
    	parent::__construct();
        $this->model = new Media;

        $this->route .= '.media';
        $this->views .= '.media';

        View::share(['route' => $this->route, 'views' => $this->views]);
    }

    public function index(HttpRequest $request)
	{
		if(request()->has('action'))
		{
			if(request()->get('action') == 'delete'){
				$id = request()->get('id');
				$file = Media::find($id);
				if($file)
				{
					if(file_exists(public_path($file->file_path))){
	                    @unlink(public_path($file->file_path));
	                }
	                if($file->media_type == 'Image'){
		                if($file->thumb_file_path && file_exists(public_path($file->thumb_file_path))){
		                    @unlink(public_path($file->thumb_file_path));
		                }
		                if($file->slider_file_path && file_exists(public_path($file->slider_file_path))){
		                    @unlink(public_path($file->slider_file_path));
		                }
		            }
		            $file->forcedelete();
		        }
			}
			if(request()->get('action') == 'bulk_delete')
			{
				$ids = request()->get('ids');
				foreach ($ids as $key => $id) {
					$file = Media::find($id);
					if($file)
					{
						if(file_exists(public_path($file->file_path))){
		                    @unlink(public_path($file->file_path));
		                }
		                if($file->media_type == 'Image'){
			                if($file->thumb_file_path && file_exists(public_path($file->thumb_file_path))){
			                    @unlink(public_path($file->thumb_file_path));
			                }
			                if($file->slider_file_path && file_exists(public_path($file->slider_file_path))){
			                    @unlink(public_path($file->slider_file_path));
			                }
			            }
			            $file->forcedelete();
					}
				}
			}
		}
		$result = Media::orderBy('created_at', 'desc');
		$req = isset($_GET['req'])?$_GET['req']:null;
		$page = isset($_GET['page'])?$_GET['page']:1;
		if(request()->has('req'))
		{
			$req = request()->get('req');
			$result->where('file_name', 'like', '%' . request()->get('req') . '%');
		}
		$files = $result->Paginate(16);
		if ($request->ajax()) {
			return view($this->views.'.ajax_index', array('files'=>$files, 'req'=>$req, 'page'=>$page));
		}
		else{
			return view($this->views.'.index', array('files'=>$files, 'req'=>$req, 'page'=>$page));
		}
	}

	public function ajaxIndex()
	{
		return view($this->views.'.ajax_index');
	}

	public function popup(httpRequest $request, $popup_type="photos", $type=null, $holder_attr="", $related_id=null)
	{
		if($type && $type != 'all')
		{
			$typeArr =  explode('-', $type);
			$result = Media::whereIn('media_type', $typeArr)->orderBy('created_at', 'DESC');
		}
		else
			$result = Media::orderBy('created_at', 'DESC');

		$req = isset($_GET['req'])?$_GET['req']:null;
		$page = isset($_GET['page'])?$_GET['page']:1;
		if($request->has('req'))
		{
			$req = $request->get('req');
			$type = $request->get('type');
			if($type && $type != 'all')
			{
				$typeArr =  explode('-', $type);
				$result = $result->whereIn('media_type', $typeArr);
			}
			$result->where('file_name', 'like', '%' . $request->get('req') . '%');
		}

		$files = $result->Paginate(12);
		
		$popTypeArr = explode('-', $popup_type);
		$popup_type = $popTypeArr[0];
		$id = isset($popTypeArr[1])?$popTypeArr[1]:null;

		if (isset($_GET['req']) || isset($_GET['page'])) {
			return view($this->views.'.ajax_index_popup', array('files'=>$files, 'req'=>$req, 'page'=>$page, 'popup_type'=>$popup_type, 'id'=>$id, 'holder_attr'=>$holder_attr, 'related_id'=>$related_id, 'type'=>$type));
		}
		else{
			return view($this->views.'.popup', array('files'=>$files, 'req'=>$req, 'page'=>$page, 'popup_type'=>$popup_type, 'id'=>$id, 'holder_attr'=>$holder_attr, 'related_id'=>$related_id, 'type'=>$type));
		}
	}

	public function save(HttpRequest $request)
	{
		$files = request()->file('files');
		$json = array(
        	'files' => array()
        );
	    foreach ($files as $key=> $file) {
		    $filename = $file->getClientOriginalName().".".$file->getClientOriginalExtension();

		    $upload = $this->uploadFile($file, $this->model->uploadPath['media']);
		    if($upload['success']) {

		    	$media = $this->model;
		    	$media->file_name = $upload['filename'];
		    	$media->file_path = $upload['filepath'];
		    	$media->thumb_file_path = $upload['mediathumb'];
		    	$media->file_type = $upload['filetype'];
		    	$media->file_size = $upload['filesize'];
		    	$media->dimensions = $upload['filedimensions'];
		    	$media->media_type = $upload['mediatype'];
		    	$media->created_by = Auth::user()->id;
		    	$media->updated_by = Auth::user()->id;
		    	$media->save();
		    	
			    $json['files'][] = array(
			            'name' => $filename,
			            'size' => $upload['filesize'],
			            'url' => \URL::to('').'/public/'.$media->thumb_file_path,
			            'id' => $media->id,
			            'id_encrypted' => encrypt($media->id),
			            'original_file' => \URL::to('').'/public/'.$media->file_path,
			            'type' => $media->file_type,
			            'media_type' => $media->media_type,
			    );
			}
	    }
	    return response()->json($json);
	}

	public function storeExtra($id)
	{
		if($media = Media::find($id))
		{
			$media->title = request()->get('title');
			$media->description = request()->get('description');
			$media->alt_text = request()->get('alt_text');
			$media->save();
			return response()->json(array('success'=>'Media details successfully updated!'));
		}
		return response()->json(array('error'=>'Media not found!'));
	}

	public function edit($id)
	{
		$id = decrypt($id);
		if($file = Media::find($id))
		{
			$file->file_size = $this->filesize($file->file_size);
			return view($this->views.'.edit', array('file'=>$file));
		}
	}

	public function filesize($bytes, $decimals = 2){
		//$bytes = filesize($file);
		$size = array('B','KB','MB','GB','TB','PB','EB','ZB','YB');
	    $factor = floor((strlen($bytes) - 1) / 3);
	    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
	}
}
