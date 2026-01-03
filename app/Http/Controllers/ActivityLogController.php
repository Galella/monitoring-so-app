<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('description', 'like', "%{$search}%")
                  ->orWhere('event', 'like', "%{$search}%")
                  ->orWhere('log_name', 'like', "%{$search}%")
                  ->orWhereHas('causer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
        }

        if ($request->filled('model')) {
            $query->where('subject_type', 'like', "%{$request->model}%");
        }

        $activities = $query->paginate(20);

        return view('activity_log.index', compact('activities'));
    }
}
