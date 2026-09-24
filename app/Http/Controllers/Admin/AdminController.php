<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Serve the Vue single page application shell.
     */
    public function __invoke(): View
    {
        return view('admin.app');
    }
}
