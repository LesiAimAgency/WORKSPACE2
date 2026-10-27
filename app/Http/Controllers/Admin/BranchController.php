<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::all();

        return view('cms.branches.index', compact('branches'));
    }

    public function create()
    {
        return view('cms.branches.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|array',
            'address' => 'nullable|string',
            'map_embed' => 'nullable|string',
        ]);

        Branch::create($validated);

        return redirect()->route('cms.branches.index')->with('success', 'Branch created');
    }

    public function edit(Branch $branch)
    {
        return view('cms.branches.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'name' => 'required|array',
            'address' => 'nullable|string',
            'map_embed' => 'nullable|string',
        ]);

        $branch->update($validated);

        return redirect()->route('cms.branches.index')->with('success', 'Branch updated');
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();

        return redirect()->route('cms.branches.index')->with('success', 'Branch deleted');
    }
}
