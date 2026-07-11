<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;

class DashboardController extends Controller
{
    public function index()
    {
        $categories = Kategori::all();

        return view('pages.admin.dashboard', compact('categories'));
    }
}
