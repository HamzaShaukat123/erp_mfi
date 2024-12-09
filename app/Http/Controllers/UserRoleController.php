<?php

namespace App\Http\Controllers;

use App\Models\modules;
use App\Models\roles;
use App\Models\role_access;
use App\Models\user_roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class UserRoleController extends Controller
{
    
    public function index(){

        $roles = roles::leftJoin('user_roles', 'user_roles.role_id', '=', 'roles.id')
        ->select('roles.id','roles.name','roles.shortcode', \DB::raw('COUNT(user_roles.user_id) as user_count'))
        ->groupBy('roles.id','roles.name','roles.shortcode') 
        ->get();

        return view('users.roles',compact('roles'));
    }

    public function create(){
        $modules = modules::get();
        $modulesCount = count($modules); // Count the length of the array

        return view('users.create-role',compact('modules','modulesCount'));
    }

    public function store(Request $request)
    {
        // Create a new role
        $roles = new roles();
        $roles->name = $request->role_name;
        $roles->shortcode = $request->shortcode ?? null; // Use null if shortcode is not present
        $roles->created_by = session('user_id');
        $roles->save();

        // Retrieve the newly created role ID
        $role_id = $roles->id; // Use the ID directly from the newly created role

        // Loop through the modules and create role access
        for ($i = 1; $i <= $request->modules_count; $i++) {
            $role_access = new role_access();
            $role_access->created_by = session('user_id');
            $role_access->module_id = $request->module[$i];
            $role_access->role_id = $role_id;

            // Set permissions based on checkbox values
            $role_access->add = $request->has("create.$i") ? ($request["create"][$i] === "on" ? 1 : 0) : 0;
            $role_access->view = $request->has("view.$i") ? ($request["view"][$i] === "on" ? 1 : 0) : 0;
            $role_access->edit = $request->has("update.$i") ? ($request["update"][$i] === "on" ? 1 : 0) : 0;
            $role_access->delete = $request->has("delete.$i") ? ($request["delete"][$i] === "on" ? 1 : 0) : 0;
            $role_access->att_add = $request->has("att_add.$i") ? ($request["att_add"][$i] === "on" ? 1 : 0) : 0;
            $role_access->att_delete = $request->has("att_delete.$i") ? ($request["att_delete"][$i] === "on" ? 1 : 0) : 0;
            $role_access->print = $request->has("print.$i") ? ($request["print"][$i] === "on" ? 1 : 0) : 0;
            $role_access->report = $request->has("report.$i") ? ($request["report"][$i] === "on" ? 1 : 0) : 0;
            $role_access->save();
        }

        return redirect()->route('all-roles');
    }

    public function edit($id){

        $role = roles::where('id',$id)->first();
        $role_access = role_access::where('role_access.role_id', $id)
        ->join('modules', 'modules.id', '=', 'role_access.module_id')
        ->select('role_access.*', 'modules.name as module_name')
        ->get();

        $count = count($role_access);

        return view('users.edit-role',compact('role','role_access','count'));
    }

    public function update(Request $request)
    {
        $role_id = $request->role_id;

        roles::where('id', $request->role_id)->update([
            'name'=> $request->role_name,
            'shortcode'=> $request->shortcode,
            'updated_by' => session('user_id'),
        ]);


        role_access::where('role_id', $request->role_id)->delete();

        // Loop through the modules and create role access
        for ($i = 1; $i <= $request->modules_count; $i++) {
            $role_access = new role_access();
            $role_access->created_by = session('user_id');
            $role_access->module_id = $request->module[$i];
            $role_access->role_id = $role_id;

            // Set permissions based on checkbox values
            $role_access->add = $request->has("create.$i") ? ($request["create"][$i] === "on" ? 1 : 0) : 0;
            $role_access->view = $request->has("view.$i") ? ($request["view"][$i] === "on" ? 1 : 0) : 0;
            $role_access->edit = $request->has("update.$i") ? ($request["update"][$i] === "on" ? 1 : 0) : 0;
            $role_access->delete = $request->has("delete.$i") ? ($request["delete"][$i] === "on" ? 1 : 0) : 0;
            $role_access->att_add = $request->has("att_add.$i") ? ($request["att_add"][$i] === "on" ? 1 : 0) : 0;
            $role_access->att_delete = $request->has("att_delete.$i") ? ($request["att_delete"][$i] === "on" ? 1 : 0) : 0;
            $role_access->print = $request->has("print.$i") ? ($request["print"][$i] === "on" ? 1 : 0) : 0;
            $role_access->report = $request->has("report.$i") ? ($request["report"][$i] === "on" ? 1 : 0) : 0;
            $role_access->save();
        }

        return redirect()->route('all-roles');
    }
}
