<?php

namespace App\Http\Controllers;

use App\Models\Memo;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MemoController extends Controller
{
    /**
     * Display a listing of the memos (Search & Filter).
     */
    public function index(Request $request)
    {
        $query = Memo::with('uploadedBy');

        // Apply filters
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        if ($request->filled('department')) {
            $query->byDepartment($request->department);
        }

        if ($request->filled('start_date') || $request->filled('end_date')) {
            $query->byDateRange($request->start_date, $request->end_date);
        }

        // Show soft-deleted memos to Admins if toggled
        if (Auth::user()->isAdmin() && $request->boolean('show_deleted')) {
            $query->onlyTrashed();
        }

        $memos = $query->latest('memo_date')->paginate(10)->withQueryString();

        // Unique lists for the filter dropdowns
        $categories = ['Academic', 'Finance', 'Registry', 'HR', 'Student Affairs', 'General'];
        
        $departments = Memo::select('from_department')
            ->distinct()
            ->pluck('from_department')
            ->merge(Memo::select('to_department')->distinct()->pluck('to_department'))
            ->unique()
            ->filter()
            ->values()
            ->toArray();

        // Default list of depts if empty
        if (empty($departments)) {
            $departments = ['Registry', 'Academic Affairs', 'Finance', 'Human Resources', 'Student Services', 'Management'];
        }

        return view('memos.index', compact('memos', 'categories', 'departments'));
    }

    /**
     * Show the form for creating a new memo.
     */
    public function create()
    {
        // Viewers cannot upload
        if (Auth::user()->isViewer()) {
            abort(403, 'Unauthorized access.');
        }

        $categories = ['Academic', 'Finance', 'Registry', 'HR', 'Student Affairs', 'General'];
        return view('memos.create', compact('categories'));
    }

    /**
     * Store a newly created memo in storage.
     */
    public function store(Request $request)
    {
        // Viewers cannot upload
        if (Auth::user()->isViewer()) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'subject' => 'required|string|max:255',
            'from_department' => 'required|string|max:255',
            'to_department' => 'required|string|max:255',
            'memo_date' => 'required|date',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file_upload' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240', // Max 10MB
            'scanned_file' => 'nullable|string', // Base64 data from camera scan
        ]);

        $filePath = null;

        // 1. Process standard file upload
        if ($request->hasFile('file_upload')) {
            $file = $request->file('file_upload');
            $filename = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('memos', $filename, 'local');
        } 
        // 2. Process camera-scanned PDF (Base64 URL)
        elseif ($request->filled('scanned_file')) {
            $scannedData = $request->input('scanned_file');
            
            // Expected format: data:application/pdf;base64,xxxxx or data:application/pdf;filename=xxxx.pdf;base64,xxxxx
            if (preg_match('/^data:application\/pdf;(?:filename=[^;]+;)?base64,(.*)$/is', $scannedData, $matches)) {
                $pdfData = base64_decode($matches[1]);
                $filename = 'scan_' . time() . '_' . Str::random(8) . '.pdf';
                $filePath = 'memos/' . $filename;
                Storage::disk('local')->put($filePath, $pdfData);
            } else {
                return back()->withErrors(['scanned_file' => 'Invalid scanned document format.'])->withInput();
            }
        }

        if (!$filePath) {
            return back()->withErrors(['file_upload' => 'Please upload a file or scan a document.'])->withInput();
        }

        // 3. Auto-generate custom Memo Number (e.g. REG/2026/001)
        $year = date('Y', strtotime($request->memo_date));
        $prefix = strtoupper(substr($request->from_department, 0, 3));
        if (strlen($prefix) < 3) {
            $prefix = 'MEM';
        }
        
        // Count memos for this year to make a sequential serial number
        $countThisYear = Memo::withTrashed()
            ->whereYear('memo_date', $year)
            ->count();
        $serial = str_pad($countThisYear + 1, 3, '0', STR_PAD_LEFT);
        
        $memoNumber = "{$prefix}/{$year}/{$serial}";

        // 4. Create the Memo record
        $memo = Memo::create([
            'subject' => $request->subject,
            'memo_number' => $memoNumber,
            'from_department' => $request->from_department,
            'to_department' => $request->to_department,
            'memo_date' => $request->memo_date,
            'category' => $request->category,
            'description' => $request->description,
            'file_path' => $filePath,
            'uploaded_by' => Auth::id(),
        ]);

        // 5. Log audit trail
        AuditLog::log('upload_memo', $memo->id, "Uploaded memo '{$memo->subject}' with number '{$memo->memo_number}'");

        return redirect()->route('memos.index')
            ->with('success', "Memo uploaded successfully! Assigned Number: {$memoNumber}");
    }

    /**
     * Show memo details.
     */
    public function show(Memo $memo)
    {
        // Check if memo is soft deleted
        if ($memo->trashed() && !Auth::user()->isAdmin()) {
            abort(404, 'Memo not found.');
        }

        return response()->json([
            'id' => $memo->id,
            'subject' => $memo->subject,
            'memo_number' => $memo->memo_number,
            'from_department' => $memo->from_department,
            'to_department' => $memo->to_department,
            'memo_date' => $memo->memo_date->format('Y-m-d'),
            'category' => $memo->category,
            'description' => $memo->description ?? 'No description provided.',
            'uploaded_by' => $memo->uploadedBy->name,
            'created_at' => $memo->created_at->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Show the edit form.
     */
    public function edit(Memo $memo)
    {
        $user = Auth::user();
        if ($user->isViewer()) {
            abort(403, 'Unauthorized access.');
        }

        // Staff can only edit their own memos
        if ($user->isStaff() && $memo->uploaded_by !== $user->id) {
            abort(403, 'Unauthorized access. You can only edit memos you uploaded.');
        }

        $categories = ['Academic', 'Finance', 'Registry', 'HR', 'Student Affairs', 'General'];
        return view('memos.edit', compact('memo', 'categories'));
    }

    /**
     * Update the memo.
     */
    public function update(Request $request, Memo $memo)
    {
        $user = Auth::user();
        if ($user->isViewer()) {
            abort(403, 'Unauthorized access.');
        }

        // Staff can only edit their own memos
        if ($user->isStaff() && $memo->uploaded_by !== $user->id) {
            abort(403, 'Unauthorized access. You can only edit memos you uploaded.');
        }

        $request->validate([
            'subject' => 'required|string|max:255',
            'from_department' => 'required|string|max:255',
            'to_department' => 'required|string|max:255',
            'memo_date' => 'required|date',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $changes = [];
        $original = $memo->getRawOriginal();

        $memo->update($request->only([
            'subject', 'from_department', 'to_department', 'memo_date', 'category', 'description'
        ]));

        // Detect changes for details logging
        foreach ($memo->getChanges() as $key => $value) {
            if ($key !== 'updated_at') {
                $changes[] = "{$key} from '{$original[$key]}' to '{$value}'";
            }
        }

        $details = count($changes) > 0 
            ? 'Updated: ' . implode(', ', $changes) 
            : 'No fields changed.';

        AuditLog::log('edit_memo', $memo->id, $details);

        return redirect()->route('memos.index')
            ->with('success', "Memo details updated successfully!");
    }

    /**
     * Download the memo file.
     */
    public function download(Memo $memo)
    {
        // Verify file exists
        if (!Storage::disk('local')->exists($memo->file_path)) {
            abort(404, 'The physical file does not exist on the server.');
        }

        // Audit download activity
        AuditLog::log('download_memo', $memo->id, "Downloaded file for memo '{$memo->subject}'");

        return Storage::disk('local')->download($memo->file_path, basename($memo->file_path));
    }

    /**
     * Stream preview the memo file directly (securely checks permissions).
     */
    public function preview(Memo $memo)
    {
        // Allow loading soft-deleted memo for admins
        if ($memo->trashed() && !Auth::user()->isAdmin()) {
            abort(404, 'Memo not found.');
        }

        if (!Storage::disk('local')->exists($memo->file_path)) {
            abort(404, 'File not found.');
        }

        $file = Storage::disk('local')->get($memo->file_path);
        $mimeType = Storage::disk('local')->mimeType($memo->file_path);

        return response($file, 200)->header('Content-Type', $mimeType);
    }

    /**
     * Remove the specified memo (Soft Delete).
     */
    public function destroy(Memo $memo)
    {
        // Only Admin can delete
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access. Only administrators can delete archived records.');
        }

        $memo->delete();

        AuditLog::log('delete_memo', $memo->id, "Soft-deleted memo '{$memo->subject}'");

        return redirect()->route('memos.index')
            ->with('success', "Memo archived/soft-deleted successfully.");
    }

    /**
     * Restore a soft-deleted memo.
     */
    public function restore($id)
    {
        // Only Admin can restore
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $memo = Memo::onlyTrashed()->findOrFail($id);
        $memo->restore();

        AuditLog::log('restore_memo', $memo->id, "Restored soft-deleted memo '{$memo->subject}'");

        return redirect()->route('memos.index')
            ->with('success', "Memo restored successfully.");
    }
}
