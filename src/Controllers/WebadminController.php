<?php 

namespace Spiderworks\Webadmin\Controllers;

use App\Http\Controllers\Controller;
use Input, View, Validator, Redirect, Auth, DB, Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class WebadminController extends Controller {

	/**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
		return view('spiderworks.webadmin.dashboard');
	}

	public function login()
	{
		if(Auth::user() && Auth::user()->hasRole('Admin'))
		{
			return Redirect::to('admin/dashboard');
		}
		else{
			return view('spiderworks.webadmin.login');
		}
	}

    public function select2_faq(Request $request)
    {
        $items = DB::table('faqs')->where('name', 'like', $request->q.'%')->orderBy('name')
            ->get();
        $json = [];
        foreach($items as $c){
            $json[] = ['id'=>$c->id, 'text'=>$c->name];
        }
        return \Response::json($json);
    }

    public function select2_categories($type=null)
    {
        $items = DB::table('categories')->where('name', 'like', request()->q.'%');
        if($type)
            $items->where('category_type', $type);
        
        $items = $items->orderBy('name')->get();
        $json = [];
        foreach($items as $c){
            $json[] = ['id'=>$c->id, 'text'=>$c->name];
        }
        return \Response::json($json);
    }

	public function unique_slug(Request $request)
    {
         $id = $request->id;
         $slug = $request->slug;
         $table = $request->table;
         
         $where = "slug='".$slug."'";
         if($id)
            $where .= " AND id != ".decrypt($id);
         $result = DB::table($table)
                    ->whereRaw($where)
                    ->get();
         
         if (count($result)>0) {  
             echo "false";
         } else {  
             echo "true";
         }
    }
	
	public function changePassword(Request $request){
        if (!(Hash::check($request->get('current_password'), Auth::user()->password))) {
            // The passwords matches
            return redirect()->back()->with("error","Your current password does not matches with the password you provided. Please try again.");
        }
        if(strcmp($request->get('current_password'), $request->get('new_pwd')) == 0){
            //Current password and new password are same
            return redirect()->back()->with("error","New Password cannot be same as your current password. Please choose a different password.");
        }
        $validatedData = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:6',
            'new_confirm_password' => ['same:new_password'],
        ]);

        //Change Password
        $user = Auth::user();
        $user->password = bcrypt($request->get('new_password'));
        $user->save();
        return redirect()->back()->with("success","Password changed successfully !");
    }

}
