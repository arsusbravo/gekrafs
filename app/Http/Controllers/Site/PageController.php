<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PageController extends Controller
{
    public function about(): View
    {
        return view('site.about');
    }

    public function contact(): View
    {
        return view('site.contact');
    }
}
