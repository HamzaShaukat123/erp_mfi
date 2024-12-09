<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\modules;
use App\Models\role_access;
use App\Models\roles;

class ModulesController extends Controller
{
    public function index()
    {
        $modules = modules::get();
        if(session('user_role')==1){
            return view('modules.index',compact('modules'));
        }
        else{
            return view('unauthorized');
        }
    }

    public function store(Request $request)
    {
        
        if(session('user_role')==1){
            $modules = new modules();
            // $item_group->created_by = session('user_id');
    
            if ($request->has('module_name') && $request->module_name) {
                $modules->name=$request->module_name;
            }
            if ($request->has('module_slug') && $request->module_slug OR empty($request->module_slug)) {
                $modules->slug=$request->module_slug;
            }

            $modules->save();

            $module_id = $modules->id;
            $role_ids = roles::pluck('id');
            
            foreach($role_ids as $role_id){
                $new_access = new role_access();
                $new_access->role_id=$role_id;
                $new_access->module_id= $module_id;
                $new_access->add= 0;
                $new_access->edit= 0;
                $new_access->delete= 0;
                $new_access->view= 0;
                $new_access->att_add= 0;
                $new_access->att_delete= 0;
                $new_access->print= 0;
                $new_access->report= 0;
                $new_access->created_by= session('user_id');
                $new_access->save();
            }

            return redirect()->route('all-modules');
        }
        else{
            return view('unauthorized');
        }
    }

    public function update(Request $request)
    {

        if(session('user_role')==1){

            $module_name= null;
            $module_slug=null;
       
            if ($request->has('update_module_name') && $request->update_module_name) {
                $module_name=$request->update_module_name;
            }
            if ($request->has('update_module_slug') && $request->update_module_slug) {
                $module_slug=$request->update_module_slug;
            }
        
            modules::where('id', $request->update_module_id)->update([
                'name'=>$module_name,
                'slug'=>$module_slug,
                'updated_by' => session('user_id'),
            ]);        
            
            return redirect()->route('all-modules');
        }
        else{
            return view('unauthorized');
        }

    }

    public function getModuleDetails(Request $request)
    {
        
        $modules = modules::where('id', $request->id)->get()->first();
        return $modules;
    }
}
