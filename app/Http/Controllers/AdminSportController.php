<?php
namespace App\Http\Controllers;

use App\Models\Sport;
use Illuminate\Http\Request;

class AdminSportController extends Controller
{
    public function index()
    {
        $sports = Sport::all();
        return view('admin.sports.index', compact('sports'));
    }

    public function toggle(Request $request, Sport $sport)
    {
        $sport->active = !$sport->active;
        $sport->save();
        return redirect()->route('admin.sports.index')->with('status', 'Sport status updated!');
    }
}
