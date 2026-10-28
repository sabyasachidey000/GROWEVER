<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use Illuminate\Http\Request;

class DesignationController extends Controller
{
    public function index()
    {
        $designations = Designation::orderBy('code')->paginate(25);
        return view('designations.index', compact('designations'));
    }

    public function create()
    {
        return view('designations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:designations',
            'name' => 'required|string|max:100',
            'level' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $designation = Designation::create($validated);

        $this->activityLog()->log(
            "Designation {$designation->name} ({$designation->code}) created",
            $designation,
            'created',
            'designations'
        );

        return redirect()->route('designations.index')
            ->with('success', 'Designation created successfully.');
    }

    public function show(Designation $designation)
    {
        $designation->load('employees');
        return view('designations.show', compact('designation'));
    }

    public function edit(Designation $designation)
    {
        return view('designations.edit', compact('designation'));
    }

    public function update(Request $request, Designation $designation)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:designations,code,' . $designation->id,
            'name' => 'required|string|max:100',
            'level' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $designation->update($validated);

        $this->activityLog()->log(
            "Designation {$designation->name} updated",
            $designation,
            'updated',
            'designations'
        );

        return redirect()->route('designations.index')
            ->with('success', 'Designation updated successfully.');
    }

    public function destroy(Designation $designation)
    {
        $designation->delete();

        $this->activityLog()->log(
            "Designation {$designation->name} deleted",
            $designation,
            'deleted',
            'designations'
        );

        return redirect()->route('designations.index')
            ->with('success', 'Designation deleted successfully.');
    }
}
