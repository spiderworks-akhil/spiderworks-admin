<?php

namespace Spiderworks\Webadmin\Controllers;

use Spiderworks\Webadmin\Controllers\BaseController as Controller;
use Spiderworks\Webadmin\Traits\ResourceTrait;

use Spiderworks\Webadmin\Models\Page;

use Illuminate\Http\Request;
use View, Redirect;

class PageController extends Controller
{
    use ResourceTrait;

    public function __construct()
    {
        parent::__construct();

        $this->model = new Page;
        $this->route .= '.pages';
        $this->views .= '.pages';

        $this->resourceConstruct();

    }

    public function index(Request $request, $parent=null)
    {
        if ($request->ajax()) {
            $collection = $this->getCollection();
            $parent_id = ($parent)?$parent:0;
            $collection->where('pages.parent_id', '=', $parent_id);
            $route = $this->route;
            return $this->setDTData($collection)->make(true);
        } else {
            $parent_data = null;
            if($parent)
                $parent_data = $this->model->find($parent);
            return view::make($this->views . '.index', array('parent'=>$parent, 'parent_data'=>$parent_data));
        }
    }
    
    protected function getCollection() {
        return $this->model->select('id', 'name', 'parent_id', 'title', 'status', 'created_at', 'updated_at');
    }

    protected function setDTData($collection) {
        $route = $this->route;
        return $this->initDTData($collection)
        	->addColumn('sub-pages', function($obj) use ($route) {
                $has_child = $this->model->where('parent_id', '=', $obj->id)->count();
                if($has_child > 0){
                    return '<a href="' . route( $route . '.index',  [$obj->id] ) . '" class="" >' . $has_child . ' sub pages</a>';
                }else{
                    return '<a href="' . route( $route . '.index',  [$obj->id] ) . '" class="" >No sub pages</a>';
                }
            })
            ->addColumn('action_delete_category', function($obj) use ($route) { 
                $has_child = $this->model->where('parent_id', '=', $obj->id)->count();
                if($has_child)
                {
                    return '<button type="button" class= "btn-sm delete_have_child" title="Created at : ' . date('d/m/Y - h:i a', strtotime($obj->created_at)) . '" > <i class="fa fa-trash"></i></button>';
                }
                else{
                     return '<a href="' . route( $route . '.destroy',  [$obj->id] ) . '" class="webadmin-btn-warning-popup" data-message="Are you sure to delete?  Associated data will be removed if it is deleted." title="' . ($obj->updated_at ? 'Last updated at : ' . date('d/m/Y - h:i a', strtotime($obj->updated_at)) : '') . '" ><i class="fa fa-trash"></i></a>';
                }
            })
            ->rawColumns(['action_edit', 'action_delete_category', 'status', 'sub-pages']);
    }

    public function create($parent=null)
    {
        $parent_data = null;
        if($parent)
            $parent_data = $this->model->find($parent);
        $pages = $this->model->where('parent_id',0)->get();
        return view::make($this->views . '.form', array('obj'=>$this->model, 'parent'=>$parent, 'parent_data'=>$parent_data, 'pages'=>$pages));
    }

    public function store()
    {
        $this->model->validate();
        $data = request()->all();

        $this->model->fill($data);
        $this->model->save();
        return Redirect::to(route($this->route. '.edit', ['id'=> encrypt($this->model->id)]))->withSuccess('Page successfully saved!');
    }

    public function edit($id) {
    	$id = decrypt($id);
        if($obj = $this->model->find($id)){
            $parent = null;
            if($obj->parent_id >0)
                $parent = $obj->parent_id;
            $parent_data = $this->model->where('parent_id', $obj->parent_id)->first();
            $pages = $this->model->where('parent_id',0)->get();
            return view($this->views . '.form')->with('obj', $obj)->with('parent', $parent)->with('parent_data', $parent_data)->with('pages', $pages);
        } else {
            return $this->redirect('notfound');
        }
    }

    public function update()
    {
    	$data = request()->all();
    	$id = decrypt($data['id']);
        $this->model->validate(request()->all(), $id);
         if($obj = $this->model->find($id)){

            $obj->update($data);

            return Redirect::to(route($this->route. '.edit', ['id'=>encrypt($id)]))->withSuccess('Page successfully updated!');
        } else {
            return Redirect::back()
                    ->withErrors("Ooops..Something wrong happend.Please try again.") // send back all errors to the login form
                    ->withInput(request()->all());
        }
    }
}
