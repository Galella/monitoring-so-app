<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Area;
use App\Models\Wilayah;

class AreaController extends Controller
{
    public function index()
    {
        $areas = Area::with('wilayah')->paginate(10);
        return view('area.index', compact('areas'));
    }

    public function create()
    {
        $wilayahs = Wilayah::all();
        return view('area.create', compact('wilayahs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'wilayah_id' => 'required|exists:wilayahs,id',
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:20|unique:areas,code',
        ]);

        Area::create($request->all());

        return redirect()->route('areas.index')->with('success', 'Area created successfully.');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $area = Area::findOrFail($id);
        $wilayahs = Wilayah::all();
        return view('area.edit', compact('area', 'wilayahs'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'wilayah_id' => 'required|exists:wilayahs,id',
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:20|unique:areas,code,' . $id,
        ]);

        $area = Area::findOrFail($id);
        $area->update($request->all());

        return redirect()->route('areas.index')->with('success', 'Area updated successfully.');
    }

    public function destroy(string $id)
    {
        $area = Area::findOrFail($id);

        if ($area->users()->count() > 0) {
            return redirect()->route('areas.index')->with('error', 'Cannot delete Area that has Users assigned.');
        }

        $area->delete();

        return redirect()->route('areas.index')->with('success', 'Area deleted successfully.');
    }
}
