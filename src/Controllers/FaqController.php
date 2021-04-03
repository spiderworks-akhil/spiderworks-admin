<?php

namespace Spiderworks\Webadmin\Controllers;

use Spiderworks\Webadmin\Controllers\BaseController as Controller;
use Spiderworks\Webadmin\Traits\ResourceTrait;

use Spiderworks\Webadmin\Models\Faq;
use Spiderworks\Webadmin\Models\FaqQuestionAnswer;

use Illuminate\Http\Request;
use Redirect;

class FaqController extends Controller
{
	use ResourceTrait;

    public function __construct()
    {
        parent::__construct();

        $this->model = new Faq;

        $this->route .= '.faq';
        $this->views .= '.faq';

        $this->resourceConstruct();

    }


    protected function getCollection() {
        return $this->model->select('id','name', 'status', 'created_at', 'updated_at');
        
    }

    protected function setDTData($collection) {
        $route = $this->route;
        return $this->initDTData($collection)
            ->rawColumns(['action_edit', 'action_delete', 'status']);
    }

    public function store(Request $request)
    {
        $this->model->validate();
        $data = request()->all();
        $this->model->fill($data);
        if($this->model->save())
        {
            if(isset($data['faq']))
                foreach ($data['faq'] as $key => $value) {
                    if($value['question'] !='' && $value['answer'] != '')
                    {
                        $question_answers = new FaqQuestionAnswer; 
                        $question_answers->display_order = $value['display_order'];
                        $question_answers->question = $value['question'];
                        $question_answers->answer = $value['answer'];
                        $this->model->question_answers()->save($question_answers);
                    }
                }
        }
        return Redirect::to(url('admin/faq'))->withSuccess('Faq successfully added!');
    }

    public function update($id)
    {

        $this->model->validate(request()->all(), $id);
         if($obj = $this->model->find($id)){
            $data = request()->all();
            $obj->update($data);

            FaqQuestionAnswer::where('faq_id', $obj->id)->forcedelete();
            if(isset($data['faq']))
                foreach ($data['faq'] as $key => $value) {
                    if($value['question'] !='' && $value['answer'] != '')
                    {
                        $question_answers = new FaqQuestionAnswer;
                        $question_answers->display_order = $value['display_order'];
                        $question_answers->question = $value['question'];
                        $question_answers->answer = $value['answer'];
                        $obj->question_answers()->save($question_answers);
                    }
                }
                
            return Redirect::to(url('admin/faq/edit', ['id'=>encrypt($id)]))->withSuccess('Faq successfully updated!');
        } else {
            return Redirect::back()
                    ->withErrors("Ooops..Something wrong happend.Please try again.") // send back all errors to the login form
                    ->withInput(request()->all());
        }
    }
}
