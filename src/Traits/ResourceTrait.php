<?php 

namespace Spiderworks\Webadmin\Traits;

use View, Request, DataTables, Form, Redirect;
use Illuminate\Http\Exception\HttpResponseException;
use Illuminate\Http\Request as HttpRequest;

trait ResourceTrait {

	protected $model, $entity;

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function resourceConstruct()
	{
		$this->entity = $this->getEntityName();

		View::share(['route' => $this->route, 'views' => $this->views, 'entity' => $this->entity]);
	}

	protected function getEntityName() {
		$name = class_basename($this->model);
        $parts = preg_split('/(?=[A-Z])/', $name, -1, PREG_SPLIT_NO_EMPTY);
        return ucfirst(strtolower(implode(' ', $parts)));
	}

	/**
	 * Show the data list.
	 *
	 * @return Response
	 */
	public function index()
	{
        if (Request::ajax()) {
            $collection = $this->getCollection();
            return $this->setDTData($collection)->make(true);
        } else {
			return view($this->views . '.index');
        }
	}

	abstract protected function getCollection();

	protected function initDTData($collection, $queries = []) {
		$route = $this->route;
		return Datatables::of($collection)
            ->setRowId('row-{{ $id }}')
            ->editColumn('status', function($obj) use($route) { 
                if($obj->status == 1)
                {
                    return '<a href="' . route($route.'.change-status', [encrypt($obj->id)]).'" class=" btn-sm webadmin-btn-warning-popup" data-message="Are you sure, want to disable this record?"><i class="fa fa-check-circle"></i></a>';
                }
                else{
                    return '<a href="' . route($route.'.change-status', [encrypt($obj->id)]) . '" class=" btn-sm webadmin-btn-warning-popup" data-message="Are you sure, want to enable this record?"><i class="fa fa-times-circle"></i></a>';
                }
            })
            ->addColumn('action_edit', function($obj) use ($route, $queries) { 
                return '<a href="'.route($route.'.edit', [encrypt($obj->id)]).'" class="" title="' . ($obj->updated_at ? 'Last updated at : ' . date('d/m/Y - h:i a', strtotime($obj->updated_at)) : ''). '" ><i class="fa fa-pencil"></i></a>';
            })
            ->addColumn('action_delete', function($obj) use ($route, $queries) { 
                return '<a href="'.route($route.'.destroy', [encrypt($obj->id)]).'" class="webadmin-btn-warning-popup" data-message="Are you sure to delete?  Associated data will be removed if it is deleted." title="' . ($obj->updated_at ? 'Last updated at : ' . date('d/m/Y - h:i a', strtotime($obj->updated_at)) : '') . '"><i class="fa fa-trash"></i></a>';
            });
	}

	protected function setDTData($collection) {
		return $this->initDTData($collection);
	}

	/**
	 * Show the add form.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view($this->views . '.form')->with('obj', $this->model);
	}

	public function show($id)
	{
		return $this->edit($id);
	}

    public function store(HttpRequest $request)
    {
        $data = $request->all();
    	$this->model->validate($data);
        return $this->_store($data);
    }

	protected function _store($data)
	{
        if(isset($data['is_featured']))
            $data['is_featured'] = 1;
		$this->model->fill($data);
		$this->model->save();
		return $this->redirect('created', 'success', 'edit', [encrypt($this->model->id)]);
	}

    public function edit($id) {
        $id = decrypt($id);
        if($obj = $this->model->find($id)){
            return view($this->views . '.form')->with('obj', $obj);
        } else {
            return $this->redirect('notfound');
        }
    }

    public function update(HttpRequest $request) {
        $data = $request->all();
        $id = decrypt($data['id']);
    	$this->model->validate($data, $id);
        return $this->_update($id, $data);
    }

    protected function _update($id, $data) {
        if($obj = $this->model->find($id)){
            $data['is_featured'] = isset($data['is_featured'])?1:0;
        	$obj->update($data);
            return $this->redirect('updated','success', 'edit', [encrypt($obj->id)]);
        } else {
            return $this->redirect('notfound');
        }
    }

    public function changeStatus($id)
    {
        $id = decrypt($id);
        $obj = $this->model->find($id);
        if ($obj) {
            $status = $obj->status;
            $set_status = ($status == 1)?0:1;
            $obj->status = $set_status;
            $obj->save();
            $message = ($status == 1)?"disabled":"enabled";
            return $this->redirect($message,'success', 'index');
        }
        return $this->redirect('notfound');
    }
    
    public function destroy($id) {
        $id = decrypt($id);
        $obj = $this->model->find($id);
        if ($obj) {
            $obj->delete();
            return $this->redirect('removed','success', 'index');
        }
        
        return $this->redirect('notfound');
    }


    /**
     * Redirect after an operation
     * @return Redirect redirect object
     */
	protected function redirect($op = null, $type = 'success', $view = 'edit', $params='')
	{
        if($type == 'success')
        {
            $message = '';
            
            if($op =='created')
                $message = 'created';
            elseif($op =='removed')
                $message = 'deleted';
            elseif($op =='disabled')
                $message = 'disabled';
            elseif($op =='enabled')
                $message = 'enabled';
            elseif($op == 'updated')
                $message = 'updated';

            if (Request::ajax())
                $response = response()->json(['success'=>$this->entity.' successfully '.$message.'!']);
            else
                $response = Redirect::route($this->route . '.' . $view, $params)->withSuccess($this->entity.' successfully '.$message.'!');
        }
        else
            if (Request::ajax())
                $response = response()->json(['error'=>'Oops!! something went wrong...Please try again.']);
            else
                $response = Redirect::back()->withInput();

        return $response;
	}

}