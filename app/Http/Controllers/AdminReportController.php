<?php

namespace App\Http\Controllers;

use App\Models\UserReport;
use App\Models\User;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    public function __construct()
    {
        // Middleware is applied in routes group (auth, admin)
    }

    /**
     * Display a listing of user reports with filters.
     */
    public function index(Request $request)
    {
        $query = UserReport::with(['reporter', 'reportedUser', 'reviewer'])
            ->orderBy('created_at', 'desc');

        // Filters
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('reporter', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('reportedUser', function ($q3) use ($search) {
                    $q3->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                })->orWhere('reason', 'like', "%{$search}%");
            });
        }

        $reports = $query->paginate(20);

        // Stats
        $stats = [
            'pending' => UserReport::where('status', 'pending')->count(),
            'reviewed' => UserReport::where('status', 'reviewed')->count(),
            'resolved' => UserReport::where('status', 'resolved')->count(),
            'dismissed' => UserReport::where('status', 'dismissed')->count(),
            'total' => UserReport::count(),
        ];

        return view('admin.reports.index', compact('reports', 'stats'));
    }

    /**
     * Show a specific report.
     */
    public function show(UserReport $report)
    {
        $report->load(['reporter', 'reportedUser', 'reviewer']);
        return view('admin.reports.show', compact('report'));
    }

    /**
     * Update report status and admin notes.
     */
    public function updateStatus(Request $request, UserReport $report)
    {
        $request->validate([
            'status' => 'required|in:pending,reviewed,resolved,dismissed',
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        $data = [
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
        ];

        if ($request->status !== 'pending') {
            $data['reviewed_by'] = auth()->id();
            $data['reviewed_at'] = now();
        }

        $report->update($data);

        return redirect()->back()->with('success', 'Denúncia atualizada com sucesso.');
    }

    /**
     * Deactivate the reported user's account.
     */
    public function deactivateReportedUser(UserReport $report)
    {
        $user = $report->reportedUser;
        if ($user && $user->is_active) {
            $user->update(['is_active' => false]);
        }

        return redirect()->back()->with('success', 'Conta do usuário desativada.');
    }

    /**
     * Reactivate the reported user's account.
     */
    public function activateReportedUser(UserReport $report)
    {
        $user = $report->reportedUser;
        if ($user && !$user->is_active) {
            $user->update(['is_active' => true]);
        }

        return redirect()->back()->with('success', 'Conta do usuário reativada.');
    }
}
