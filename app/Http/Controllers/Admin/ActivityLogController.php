<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = Activity::with('causer')
            ->when($request->event, fn($q,$v) => $q->where('event', $v))
            ->when($request->subject_type, fn($q,$v) => $q->where('subject_type', $v))
            ->when($request->from, fn($q,$v) => $q->whereDate('created_at','>=',$v))
            ->when($request->to, fn($q,$v) => $q->whereDate('created_at','<=',$v))
            ->latest()->paginate(50);

        return view('admin.activity-logs.index', [
            'logs' => $logs,
            'events' => Activity::select('event')->distinct()->pluck('event'),
            'subjectTypes' => Activity::select('subject_type')->distinct()->pluck('subject_type'),
        ]);
    }

    public function show(Activity $log)
    {
        $log->load('causer');
        return view('admin.activity-logs.show', compact('log'));
    }
}
