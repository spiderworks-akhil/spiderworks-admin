<?php

namespace Spiderworks\Webadmin\Controllers;

use Spiderworks\Webadmin\Controllers\BaseController as Controller;
use Spiderworks\Webadmin\Traits\ResourceTrait;

use Spiderworks\Webadmin\Models\Event;

use View, Redirect;

class EventController extends Controller
{
    use ResourceTrait;

    public function __construct()
    {
        parent::__construct();

        $this->model = new Event;
        $this->route .= '.events';
        $this->views .= '.events';

        $this->resourceConstruct();

    }
    
    protected function getCollection() {
        return $this->model->select('id', 'slug', 'name', 'title', 'status', 'created_at', 'updated_at');
    }

    protected function setDTData($collection) {
        $route = $this->route;
        return $this->initDTData($collection)
            ->rawColumns(['action_edit', 'action_delete', 'status']);
    }

    public function store()
    {
        $this->model->validate();
        $data = request()->all();
        $data['is_featured'] = isset($data['is_featured'])?1:0;
        $date = $this->parse_date_time($data['date_time']);
        if($date)
        {
            $data['start_time'] = $date['start'];
            $data['end_time'] = $date['end'];
        }
        $this->model->fill($data);
        $this->model->save();
        return Redirect::to(route($this->route. '.edit', ['id'=> encrypt($this->model->id)]))->withSuccess('Page successfully saved!');
    }

    public function update()
    {
        $data = request()->all();
        $id = decrypt($data['id']);
        $this->model->validate(request()->all(), $id);
         if($obj = $this->model->find($id)){
            $data['is_featured'] = isset($data['is_featured'])?1:0;
            $date = $this->parse_date_time($data['date_time']);
            if($date)
            {
                $data['start_time'] = $date['start'];
                $data['end_time'] = $date['end'];
            }
            else{
                $data['start_time'] = null;
                $data['end_time'] = null;
            }
            $obj->update($data);

            return Redirect::to(route($this->route. '.edit', ['id'=>encrypt($id)]))->withSuccess('Page successfully updated!');
        } else {
            return Redirect::back()
                    ->withErrors("Ooops..Something wrong happend.Please try again.") // send back all errors to the login form
                    ->withInput(request()->all());
        }
    }

    public function parse_date_time($input)
    {
        $output = [];
        if($input)
        {
            $date_range = explode('-', $input);
            $arrStartRange = explode(' ', trim($date_range[0]));
            $arrStart = explode("/", $arrStartRange[0]);
            $arrStartTime = explode(":", $arrStartRange[1]);

            $arrEndRange = explode(' ', trim($date_range[1]));
            $arrEnd = explode("/", $arrEndRange[0]);
            $arrEndTime = explode(":", $arrEndRange[1]);
            $output['start'] = date('Y-m-d H:i:s', strtotime($arrStart[2].'-'.$arrStart[0].'-'.$arrStart[1].' '.$arrStartTime[0].':'.$arrStartTime[1].':00'));
            $output['end'] = date('Y-m-d H:i:s', strtotime($arrEnd[2].'-'.$arrEnd[0].'-'.$arrEnd[1].' '.$arrEndTime[0].':'.$arrEndTime[1].':00'));
        }
        return $output;
    }
}
