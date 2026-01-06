<?php

namespace App\Http\Controllers;

use App\Models\PoTimeline;
use Illuminate\Http\Request;

class PoTimelineController extends Controller
{
    public function index(Request $request)
    {
        $po = $request->get('po');
        if (!$po) {
            return response()->json([]);
        }

        $timelines = PoTimeline::where('po_number', $po)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($timeline) {
                return [
                    'id' => $timeline->id,
                    'description' => $timeline->description,
                    'user_name' => $timeline->user->name ?? 'Unknown',
                    'created_at_formatted' => $timeline->created_at->format('d-M-Y H:i'),
                    'time_ago' => $timeline->created_at->diffForHumans()
                ];
            });

        return response()->json($timelines);
    }

    public function store(Request $request)
    {
        $request->validate([
            'po_number' => 'required|string',
            'description' => 'required|string',
        ]);

        $timeline = PoTimeline::create([
            'po_number' => $request->po_number,
            'description' => $request->description,
            'user_id' => auth()->id(),
        ]);

        // Return the formatted object immediately for the UI
        return response()->json([
            'success' => true,
            'timeline' => [
                'id' => $timeline->id,
                'description' => $timeline->description,
                'user_name' => auth()->user()->name,
                'created_at_formatted' => $timeline->created_at->format('d-M-Y H:i'),
                'time_ago' => $timeline->created_at->diffForHumans()
            ]
        ]);
    }
}
