<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MobilePersonsController extends Controller
{
    public function index()
    {
        $persons = Person::where('user_id', Auth::id())
            ->orderBy('fullname')
            ->get();

        return view('mobile.persons.index', compact('persons'));
    }

    public function create()
    {
        return view('mobile.persons.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fullname' => 'required|string|max:255',
        ]);

        Person::create([
            'user_id' => Auth::id(),
            'fullname' => $validated['fullname'],
        ]);

        return redirect()->route('mobile.persons.index')
            ->with('success', __('common.created_successfully'));
    }

    public function edit($id)
    {
        $person = Person::where('user_id', Auth::id())
            ->findOrFail($id);

        return view('mobile.persons.edit', compact('person'));
    }

    public function update(Request $request, $id)
    {
        $person = Person::where('user_id', Auth::id())
            ->findOrFail($id);

        $validated = $request->validate([
            'fullname' => 'required|string|max:255',
        ]);

        $person->update($validated);

        return redirect()->route('mobile.persons.index')
            ->with('success', __('common.updated_successfully'));
    }
}
