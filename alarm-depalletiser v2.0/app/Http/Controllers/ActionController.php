<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ActionController extends Controller
{
    public function store(Request $request, Alarm $alarm)
{
    $request->validate([
        'action_text' => 'required|string'
    ]);

    $alarm->actions()->create([
        'action_text' => $request->action_text
    ]);

    return back()->with('success','Action ditambahkan');
}

}
