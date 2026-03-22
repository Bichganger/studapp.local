<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            if (!$user || !$user->isAdmin()) {
                abort(403, 'Доступ запрещен');
            }
            return $next($request);
        });
    }

    public function index()
    {
        return view('admin.html');
    }
}
