<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Wilayah;

class WilayahController extends Controller
{
    public function index()
    {
        $wilayahs = Wilayah::paginate(10);
        return view('wilayah.index', compact('wilayahs'));
    }

    public function create()
    {
        return view('wilayah.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:20|unique:wilayahs,code',
        ]);

        Wilayah::create($request->all());

        return redirect()->route('wilayahs.index')->with('success', 'Wilayah created successfully.');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $wilayah = Wilayah::findOrFail($id);
        return view('wilayah.edit', compact('wilayah'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:20|unique:wilayahs,code,' . $id,
        ]);

        $wilayah = Wilayah::findOrFail($id);
        $wilayah->update($request->all());

        return redirect()->route('wilayahs.index')->with('success', 'Wilayah updated successfully.');
    }

    public function destroy(string $id)
    {
        $wilayah = Wilayah::findOrFail($id);
        
        if ($wilayah->areas()->count() > 0 || $wilayah->users()->count() > 0) {
            return redirect()->route('wilayahs.index')->with('error', 'Cannot delete Wilayah that has Areas or Users assigned.');
        }

        $wilayah->delete();

        return redirect()->route('wilayahs.index')->with('success', 'Wilayah deleted successfully.');
    }
}
