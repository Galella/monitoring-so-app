<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coin;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CoinImport;
use App\Exports\CoinExport;
use App\Exports\CoinTemplateExport;
use Maatwebsite\Excel\Validators\ValidationException;

class CoinController extends Controller
{
    public function downloadTemplate()
    {
        return Excel::download(new CoinTemplateExport, 'coin_import_template.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            Excel::import(new CoinImport, $request->file('file'));
            return redirect()->route('coins.index')->with('success', 'Coin Data imported successfully.');
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

    public function export(Request $request)
    {
        return Excel::download(new CoinExport($request->search), 'coin_data_' . date('Y-m-d') . '.xlsx');
    }

    public function index(Request $request)
    {
        $query = Coin::forUser(auth()->user())->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('container', 'like', "%{$search}%")
                  ->orWhere('customer', 'like', "%{$search}%")
                  ->orWhere('so', 'like', "%{$search}%")
                  ->orWhere('po', 'like', "%{$search}%");
            });
        }

        $coins = $query->paginate(10);
        return view('coin.index', compact('coins'));
    }

    public function show(Coin $coin)
    {
        return view('coin.show', compact('coin'));
    }

    public function edit(Coin $coin)
    {
        return view('coin.edit', compact('coin'));
    }

    public function update(Request $request, Coin $coin)
    {
        $request->validate([
            'order_number' => 'required|integer|unique:coins,order_number,' . $coin->id,
            'container' => 'required|string',
            'seal' => 'required|string',
             // ... minimal validation for critical fields
        ]);

        $data = $request->except(['_token', '_method']);
        $coin->update($data);

        return redirect()->route('coins.index')->with('success', 'Coin Data updated successfully.');
    }

    public function destroy(Coin $coin)
    {
        $coin->delete();
        return redirect()->route('coins.index')->with('success', 'Coin Data deleted successfully.');
    }
}
