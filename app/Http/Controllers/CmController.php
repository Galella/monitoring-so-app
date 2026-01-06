<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cm;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CmImport;
use App\Exports\CmTemplateExport;
use Maatwebsite\Excel\Validators\ValidationException;

class CmController extends Controller
{
    public function downloadTemplate()
    {
        return Excel::download(new CmTemplateExport, 'cm_import_template.xlsx');
    }

    public function export(Request $request)
    {
        return Excel::download(new \App\Exports\CmExport($request->search), 'cm_data_' . date('Y-m-d') . '.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            Excel::import(new CmImport, $request->file('file'));
            return redirect()->route('cms.index')->with('success', 'CM Data imported successfully.');
        } catch (ValidationException $e) {
             $failures = $e->failures();
             $messages = [];
             foreach ($failures as $failure) {
                 $messages[] = "Baris ke-{$failure->row()}: " . implode(', ', $failure->errors());
             }
             $errorMessage = implode("\n", array_slice($messages, 0, 5));
             if (count($messages) > 5) {
                 $errorMessage .= "\n... dan " . (count($messages) - 5) . " kesalahan lainnya.";
             }
             return redirect()->back()->with('error', $errorMessage);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengimpor data: ' . $e->getMessage());
        }
    }

    public function index(Request $request)
    {
        $query = Cm::forUser(auth()->user())->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ppcw', 'like', "%{$search}%")
                  ->orWhere('container', 'like', "%{$search}%")
                  ->orWhere('shipper', 'like', "%{$search}%")
                  ->orWhere('consignee', 'like', "%{$search}%")
                  ->orWhere('cm', 'like', "%{$search}%");
            });
        }

        $cms = $query->paginate(10);
        return view('cm.index', compact('cms'));
    }

    public function create()
    {
        return view('cm.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'ppcw' => 'required|string|max:255',
            'container' => 'required|string|max:255',
            'seal' => 'nullable|string|max:255',
            'shipper' => 'required|string|max:255',
            'consignee' => 'required|string|max:255',
            'status' => 'required|string|max:255',
            'commodity' => 'nullable|string|max:255',
            'size' => 'required|string|max:255',
            'berat' => 'nullable|integer',
            'keterangan' => 'nullable|string',
            'cm' => 'required|string|max:255',
            'atd' => 'nullable|date',
        ]);

        // Auto-assign location based on creator
        if (auth()->user()->role->name !== 'Super Admin') {
            $validatedData['wilayah_id'] = auth()->user()->wilayah_id;
            $validatedData['area_id'] = auth()->user()->area_id;
        }

        Cm::create($validatedData);

        return redirect()->route('cms.index')->with('success', 'CM Data created successfully.');
    }

    public function show(Cm $cm)
    {
        return view('cm.show', compact('cm'));
    }

    public function edit(Cm $cm)
    {
        return view('cm.edit', compact('cm'));
    }

    public function update(Request $request, Cm $cm)
    {
        $validatedData = $request->validate([
            'ppcw' => 'required|string|max:255',
            'container' => 'required|string|max:255',
            'seal' => 'nullable|string|max:255',
            'shipper' => 'required|string|max:255',
            'consignee' => 'required|string|max:255',
            'status' => 'required|string|max:255',
            'commodity' => 'nullable|string|max:255',
            'size' => 'required|string|max:255',
            'berat' => 'nullable|integer',
            'keterangan' => 'nullable|string',
            'cm' => 'required|string|max:255',
            'atd' => 'nullable|date',
        ]);

        $cm->update($validatedData);

        return redirect()->route('cms.index')->with('success', 'CM Data updated successfully.');
    }

    public function destroy(Cm $cm)
    {
        $cm->delete();
        return redirect()->route('cms.index')->with('success', 'CM Data deleted successfully.');
    }
}
