<?php

namespace Spiderworks\Webadmin\Controllers;

use Spiderworks\Webadmin\Controllers\BaseController as Controller;
use Spiderworks\Webadmin\Traits\ResourceTrait;
use Spiderworks\Webadmin\Models\Menu, Input, View, Redirect, DB, Datatables;
use Spiderworks\Webadmin\Models\MenuItem;
use Spiderworks\Webadmin\Models\Page;
use Spiderworks\Webadmin\Models\Category;
use Spiderworks\Webadmin\Models\FrontendPage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MenuController extends Controller
{
    use ResourceTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->model = new Menu;

        $this->route .= '.menus';
        $this->views .= '.menus';

        $this->resourceConstruct();

    }


    protected function getCollection() {
        return $this->model->select('id', 'name', 'position', 'status', 'created_at', 'updated_at');
        
    }

    protected function setDTData($collection) {
        $route = $this->route;
        return $this->initDTData($collection)
            ->rawColumns(['action_edit', 'action_delete', 'status']);
    }

    public function create()
    {
        $pages = $this->displayPageHierarchy();
        $categories = $this->displayCategoryHierarchy();
        $front_pages = $this->displayFrontPages();
        return view($this->views . '.form', ['obj'=>$this->model, 'pages'=> $pages, 'categories'=>$categories, 'front_pages'=>$front_pages]);
    }

    public function edit($id) {
    	$id = decrypt($id);
        if($obj = $this->model->find($id)){
            $pages = $this->displayPageHierarchy();
	        $categories = $this->displayCategoryHierarchy();
	        $front_pages = $this->displayFrontPages();
            $obj->menu_items = $this->menu_tree($id, 0);
            return view($this->views . '.form', ['obj'=>$obj, 'pages'=> $pages, 'categories'=>$categories, 'front_pages'=>$front_pages]);
        } else {
            return $this->redirect('notfound');
        }
    }

    public function store()
    {
        $this->model->validate();
        $data = request()->all();
        $this->model->fill($data);
        $this->model->save();
        $menu_settings = json_decode($data['menu_settings']);
        if(isset($data['menu']))
            $this->store_recurssion($menu_settings, $data['menu'], 0, $this->model->id);
        return Redirect::to(route($this->route.'.edit', ['id'=>encrypt($this->model->id)]))->withSuccess('Menu successfully added!');
    }

    public function store_recurssion($menu_settings, $menu, $parent=0, $menu_id)
    {
        if($menu_settings)
        {
            foreach ($menu_settings as $key => $setting) {
                $id = $setting->id;
                if(isset($menu[$id]))
                {
                    $item_array = explode('-', $id);
                    $obj = new MenuItem;
                    $obj->menu_id = $menu_id;
                    if($item_array[0] == 'page_link')
                    {
                    	$obj->linkable_type = "Spiderworks\Webadmin\Models\Page";                     
                        $obj->linkable_id = $menu[$id]['id'];
                    }
                    elseif($item_array[0] == 'custom_link')
                    {
                        $obj->url = $menu[$id]['url'];
                        if(isset($menu[$id]['target_blank']))
                            $obj->target_blank = 1;
                        $obj->original_title = $menu[$id]['original_title'];
                    }
                    elseif($item_array[0] == 'frontpage_links')
                    {
                        $obj->linkable_type = "Spiderworks\Webadmin\Models\FrontendPage";                     
                        $obj->linkable_id = $menu[$id]['id'];
                    }
                    elseif($item_array[0] == 'category_link')
                    {
                        $obj->linkable_type = "Spiderworks\Webadmin\Models\Category";                     
                        $obj->linkable_id = $menu[$id]['id'];
                    }
                    $obj->title = $menu[$id]['text'];
                    $obj->menu_order = $key;
                    $obj->menu_type = $item_array[0];
                    $obj->parent_id = $parent;
                    $obj->menu_nextable_id = $menu[$id]['menu_nextable_id'];
                    $obj->save();
                    if(isset($setting->children))
                        $this->store_recurssion($setting->children, $menu, $obj->id, $menu_id);
                }
            }
        }
    }


    public function menu_tree($menu_id, $parent)
    {
        $items = MenuItem::where('menu_id', $menu_id)->where('parent_id', $parent)->orderBy('menu_order')->get();
        if($items)
        {
            foreach ($items as $key => $item) {
                $check_children = MenuItem::where('parent_id', $item->id)->count();
                if($check_children>0)
                {
                    $item['children'] = $this->menu_tree($menu_id, $item->id);
                }
            }
        }
        return $items;
    }

    public function update() {
    	$data = request()->all();
    	$id =  decrypt($data['id']);
        $this->model->validate($data, $id);
        if($obj = $this->model->find($id)){
            $obj->update($data);
            MenuItem::where('menu_id', $obj->id)->forcedelete();
            $menu_settings = json_decode($data['menu_settings']);
            if(isset($data['menu']))
                $this->store_recurssion($menu_settings, $data['menu'], 0, $obj->id);
            return Redirect::to(route($this->route.'.edit', ['id'=>encrypt($obj->id)]))->withSuccess('Menu successfully updated!');
        }
        else {
            return Redirect::back()
                    ->withErrors("Ooops..Something wrong happend.Please try again.") // send back all errors to the login form
                    ->withInput(Input::all());
        }
    }

    public function displayPageHierarchy()
    {
    	$query = DB::table('pages');
    	$options = $query->where('status', 1)->get();
    	$output = $this->parse_hierarchical_page_list($options, 0, null, 0, 'checkbox');
        return $output;
    }

    public function parse_hierarchical_page_list($array, $currentParent = 0, $selected, $deep=0, $outType='options')
    {
    	$html = '';

    	$indent = "";
		for($i = 0; $i < $deep; $i++){
		      $indent .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		}

    	if($array)
	    	foreach ($array as $key => $item) {
	    		if($item->parent_id == $currentParent){
		    		
		    		$query = DB::table('pages');

		    		$has_child = $query->where('status', 1)->count();
					 
					$cur_select = '';
					$select_type = "selected=selected";
					if($outType != 'options')
						$select_type = "checked=checked";

					if(is_array($selected))
					{
						if(in_array($item->id, $selected))
							$cur_select = $select_type;
					}
					else{
						if($selected == $item->id)
			    			$cur_select = $select_type;
					}
					
					if($outType == 'options')
						$html .= "<option value='".$item->id."' ".$cur_select.">".$indent.$item->name."</option>";
					else
						$html .= '<div class="checkbox">'.$indent.'<input type="checkbox" '.$cur_select.' name="page_links[]" class="page_links" value="page_link-'.$item->id.'" data-id="'.$item->id.'" data-name="'.$item->name.'" id="page-'.$item->id.'"> <label for="page-'.$item->id.'">'.$item->name.'</label></div>';

		          	if($has_child)
		          	{
		          		$html .= $this->parse_hierarchical_page_list ($array, $item->id, $selected, ($deep+1), $outType);
		          	}
	          	}
			}
		return $html;
    }

    public function displayCategoryHierarchy($type=null, $not_type=null, $not_in=null)
    {
    	$query = DB::table('categories');
    	if($type)
    		$query->where('type', $type);
    	if($not_type)
    		$query->where('type', '!=', $not_type);
    	$options = $query->where('status', 1)->get();
    	$output = $this->parse_hierarchical_category_list($options, 0, null, 0, 'checkbox');
        return $output;
    }

    public function parse_hierarchical_category_list($array, $currentParent = 0, $selected, $deep=0, $outType='options', $type=null, $not_in=null, $not_type=null)
    {
    	$html = '';

    	$indent = "";
		for($i = 0; $i < $deep; $i++){
		      $indent .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		}

    	if($array)
	    	foreach ($array as $key => $item) {
	    		if($item->parent_id == $currentParent){
		    		
		    		$query = DB::table('categories');
			    	if($type)
			    		$query->where('type', $type);
			    	if($not_type)
    					$query->where('type', '!=', $not_type);
			    	if($not_in)
    					$query->whereNotIn('id',$not_in);

		    		$has_child = $query->where('status', 1)->count();
					 
					$cur_select = '';
					$select_type = "selected=selected";
					if($outType != 'options')
						$select_type = "checked=checked";

					if(is_array($selected))
					{
						if(in_array($item->id, $selected))
							$cur_select = $select_type;
					}
					else{
						if($selected == $item->id)
			    			$cur_select = $select_type;
					}
					
					if($outType == 'options')
						$html .= "<option value='".$item->id."' ".$cur_select.">".$indent.$item->name."</option>";
					else
						$html .= '<div class="checkbox">'.$indent.'<input type="checkbox" '.$cur_select.' name="category_links[]" class="category_links" value="category_link-'.$item->id.'" data-id="'.$item->id.'" data-name="'.$item->name.'" id="category_links-'.$item->id.'"> <label for="category_links-'.$item->id.'">'.$item->name.'</label></div>';

		          	if($has_child)
		          	{
		          		$html .= $this->parse_hierarchical_category_list ($array, $item->id, $selected, ($deep+1), $outType, $type, $not_in, $not_type);
		          	}
	          	}
			}
		return $html;
    }

    public function displayFrontPages()
    {
    	$query = DB::table('frontend_pages');
    	$options = $query->where('status', 1)->get();
    	$html = '';
    	if(count($options)>0)
    	{
    		foreach ($options as $key => $item) {
    			$html .= '<div class="checkbox"><input type="checkbox"  name="frontpage_links[]" class="frontpage_links" value="frontpage_links-'.$item->id.'" data-id="'.$item->id.'" data-name="'.$item->name.'" id="frontpage_links-'.$item->id.'"> <label for="frontpage_links-'.$item->id.'">'.$item->name.'</label></div>';
    		}
    	}
    	return $html;
    }
}
