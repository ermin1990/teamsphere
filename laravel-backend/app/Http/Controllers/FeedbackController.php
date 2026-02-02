<?php

namespace App\Http\Controllers;

use App\Models\BugReport;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    /**
     * Show the feedback form.
     */
    public function create()
    {
        return view('feedback.create');
    }

    /**
     * Store the feedback in database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:bug,feature',
            'subject' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'email' => 'nullable|email|max:255',
            'name' => 'nullable|string|max:255',
        ]);

        BugReport::create([
            'type' => $request->type,
            'subject' => $request->subject,
            'description' => $request->description,
            'name' => $request->name,
            'email' => $request->email,
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', __('Thank you for your feedback! We will review it and get back to you if needed.'));
    }

    /**
     * Show admin panel for bug reports.
     */
    public function index()
    {
        $this->authorize('viewAny', BugReport::class);

        $reports = BugReport::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total' => BugReport::count(),
            'pending' => BugReport::where('status', 'pending')->count(),
            'in_review' => BugReport::where('status', 'in_review')->count(),
            'resolved' => BugReport::whereIn('status', ['resolved', 'closed'])->count(),
        ];

        return view('admin.bug-reports.index', compact('reports', 'stats'));
    }

    /**
     * Show a specific bug report.
     */
    public function show(BugReport $bugReport)
    {
        $this->authorize('view', $bugReport);

        return view('admin.bug-reports.show', compact('bugReport'));
    }

    /**
     * Update bug report status.
     */
    public function update(Request $request, BugReport $bugReport)
    {
        $this->authorize('update', $bugReport);

        $request->validate([
            'status' => 'required|in:pending,in_review,resolved,closed',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $bugReport->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'resolved_at' => in_array($request->status, ['resolved', 'closed']) ? now() : null,
        ]);

        return back()->with('success', __('Bug report updated successfully.'));
    }
}
