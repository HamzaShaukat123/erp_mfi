<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\modules;
use App\Models\role_access;

class CheckUserPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, $requestType, ...$permission)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect('/login'); // or wherever you want to redirect unauthenticated users
        }

        // Get permissions from db
        $parsedUrl = parse_url($request->url());
        $segments = explode('/', trim($parsedUrl['path'], '/'));
        $requestedSlug = !empty($segments) ? $segments[0] : null;

        $userAccess = session('user_access');

        // Find the module access based on the requested slug
        $moduleAccess = collect($userAccess)->firstWhere('slug', $requestedSlug);

        if ($moduleAccess) {
            // Check if the user has the requested permission (add, edit, delete, etc.)
            if (isset($moduleAccess[$requestType]) && $moduleAccess[$requestType] == 1) {
                return $next($request);
            }
        }

        // $module = modules::where('slug',$requestedSlug)->first();
        // $moduleID = $module['id'];

        // $checkaccess = role_access::where('role_id',session('user_role'))
        // ->where('module_id',$moduleID)
        // ->select($requestType)
        // ->first();
        
        // if($checkaccess[$requestType]==1){
        //     return $next($request);
        // }
        // else{
        //     return redirect('/unauthorized');
        // }

        return redirect('/unauthorized');
    }
}
