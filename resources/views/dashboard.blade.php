@extends('layouts.app')

@section('header_title', 'System Dashboard')

@section('content')
<div class="space-y-8">
    
    <!-- 1. Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Memos Card -->
        <div class="glass-card rounded-2xl p-6 flex items-center justify-between shadow-lg relative overflow-hidden group">
            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-indigo-500/10 rounded-full blur-2xl group-hover:bg-indigo-500/20 transition-all"></div>
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-1">Archived Memos</span>
                <span class="text-3xl font-extrabold text-white leading-none tracking-tight block">{{ $totalMemos }}</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-500/10 flex items-center justify-center border border-indigo-500/20 text-indigo-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
            </div>
        </div>

        <!-- Users Card -->
        <div class="glass-card rounded-2xl p-6 flex items-center justify-between shadow-lg relative overflow-hidden group">
            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-purple-500/10 rounded-full blur-2xl group-hover:bg-purple-500/20 transition-all"></div>
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-1">Active Users</span>
                <span class="text-3xl font-extrabold text-white leading-none tracking-tight block">{{ $totalUsers }}</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-purple-500/10 flex items-center justify-center border border-purple-500/20 text-purple-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
        </div>

        <!-- Audit Logs Card -->
        <div class="glass-card rounded-2xl p-6 flex items-center justify-between shadow-lg relative overflow-hidden group">
            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-emerald-500/10 rounded-full blur-2xl group-hover:bg-emerald-500/20 transition-all"></div>
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-1">Audit Entries</span>
                <span class="text-3xl font-extrabold text-white leading-none tracking-tight block">{{ $totalLogs }}</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-500/10 flex items-center justify-center border border-emerald-500/20 text-emerald-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
        </div>

    </div>

    <!-- 2. Charts and Statistics Breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Monthly Uploads Trend (SVG Chart) -->
        <div class="glass-card rounded-2xl p-6 lg:col-span-2 shadow-lg flex flex-col">
            <h3 class="text-sm font-semibold text-slate-200 mb-6 uppercase tracking-wider">Monthly Upload Trend</h3>
            
            <div class="flex-1 min-h-[220px] flex items-end justify-between relative px-2 pt-8 pb-4">
                @if(count($monthlyCounts) > 0)
                    @php
                        $maxVal = max($monthlyCounts) > 0 ? max($monthlyCounts) : 1;
                        $widthPercent = 100 / count($monthlyCounts);
                    @endphp
                    
                    <div class="absolute inset-0 flex flex-col justify-between py-8 px-2 text-[10px] text-slate-500 pointer-events-none">
                        <div class="border-b border-slate-800/80 w-full pb-1 text-right">{{ $maxVal }}</div>
                        <div class="border-b border-slate-800/80 w-full pb-1 text-right">{{ round($maxVal / 2) }}</div>
                        <div class="border-b border-slate-800/80 w-full pb-1 text-right">0</div>
                    </div>
                    
                    <div class="w-full h-full flex items-end justify-around z-10 relative">
                        @foreach($monthlyCounts as $index => $count)
                            @php
                                $heightPercent = ($count / $maxVal) * 80; // Scale to 80% max height
                            @endphp
                            <div class="flex flex-col items-center group w-full" style="height: 100%;">
                                <div class="w-2/3 max-w-[48px] bg-gradient-to-t from-indigo-600/80 to-purple-500/80 group-hover:from-indigo-500 group-hover:to-purple-400 rounded-t-lg transition-all relative flex flex-col justify-end" style="height: {{ max($heightPercent, 4) }}%;">
                                    <!-- Tooltip -->
                                    <div class="opacity-0 group-hover:opacity-100 transition-opacity absolute -top-8 left-1/2 transform -translate-x-1/2 bg-slate-950/90 text-white text-[10px] font-bold py-1 px-2 rounded-lg border border-slate-800 whitespace-nowrap pointer-events-none">
                                        {{ $count }} Memos
                                    </div>
                                </div>
                                <span class="text-[10px] text-slate-400 font-semibold mt-3 text-center truncate w-full px-1">
                                    {{ $monthlyLabels[$index] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="w-full h-full flex items-center justify-center text-slate-500 text-sm">
                        No recent upload activity.
                    </div>
                @endif
            </div>
        </div>

        <!-- Memos by Category -->
        <div class="glass-card rounded-2xl p-6 shadow-lg flex flex-col">
            <h3 class="text-sm font-semibold text-slate-200 mb-6 uppercase tracking-wider">Memos By Category</h3>
            
            <div class="flex-1 space-y-4 overflow-y-auto max-h-[220px] pr-1">
                @forelse($categoryStats as $stat)
                    @php
                        $percentage = $totalMemos > 0 ? round(($stat->count / $totalMemos) * 100) : 0;
                        $colorClass = match($stat->category) {
                            'Academic' => 'from-indigo-500 to-indigo-600',
                            'Finance' => 'from-emerald-500 to-emerald-600',
                            'Registry' => 'from-purple-500 to-purple-600',
                            'HR' => 'from-rose-500 to-rose-600',
                            'Student Affairs' => 'from-cyan-500 to-cyan-600',
                            default => 'from-slate-500 to-slate-600'
                        };
                    @endphp
                    <div>
                        <div class="flex items-center justify-between text-xs font-semibold text-slate-300 mb-1.5">
                            <span>{{ $stat->category }}</span>
                            <span>{{ $stat->count }} ({{ $percentage }}%)</span>
                        </div>
                        <div class="w-full h-2 bg-slate-800/80 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r {{ $colorClass }} rounded-full" style="width: {{ $percentage }}%;"></div>
                        </div>
                    </div>
                @empty
                    <div class="text-slate-500 text-xs py-8 text-center">No categories found.</div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- 3. Recent Uploads & Department Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Recent Uploads Table -->
        <div class="glass-card rounded-2xl p-6 lg:col-span-2 shadow-lg flex flex-col overflow-hidden">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-sm font-semibold text-slate-200 uppercase tracking-wider">Recently Uploaded Memos</h3>
                <a href="{{ route('memos.index') }}" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300 transition-colors">View All Archive &rarr;</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-800 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            <th class="pb-3 pr-2">Subject / No.</th>
                            <th class="pb-3 pr-2">From/To</th>
                            <th class="pb-3 pr-2">Date</th>
                            <th class="pb-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-xs text-slate-300">
                        @forelse($recentMemos as $memo)
                            <tr class="group hover:bg-slate-800/20 transition-all">
                                <td class="py-3.5 pr-2">
                                    <span class="font-bold text-slate-200 block truncate max-w-[200px]" title="{{ $memo->subject }}">{{ $memo->subject }}</span>
                                    <span class="text-[10px] text-slate-500">{{ $memo->memo_number }}</span>
                                </td>
                                <td class="py-3.5 pr-2">
                                    <span class="text-slate-200 block truncate max-w-[120px]">{{ $memo->from_department }}</span>
                                    <svg class="w-3 h-3 text-slate-500 inline-block my-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                    <span class="text-slate-400 truncate max-w-[120px]">{{ $memo->to_department }}</span>
                                </td>
                                <td class="py-3.5 pr-2">
                                    <span>{{ date('M d, Y', strtotime($memo->memo_date)) }}</span>
                                </td>
                                <td class="py-3.5 text-right space-x-1 whitespace-nowrap">
                                    <button onclick="previewMemo({{ $memo->id }})" class="p-1.5 rounded-lg bg-slate-800 text-indigo-400 hover:text-white hover:bg-indigo-600/80 border border-slate-700 transition-all inline-flex items-center justify-center" title="Preview Memo">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>
                                    <a href="{{ route('memos.download', $memo->id) }}" class="p-1.5 rounded-lg bg-slate-800 text-emerald-400 hover:text-white hover:bg-emerald-600/80 border border-slate-700 transition-all inline-flex items-center justify-center" title="Download File">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-slate-500">No memos archived yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Memos by Department -->
        <div class="glass-card rounded-2xl p-6 shadow-lg flex flex-col">
            <h3 class="text-sm font-semibold text-slate-200 mb-6 uppercase tracking-wider">Memos By Department</h3>
            
            <div class="flex-1 space-y-4 overflow-y-auto max-h-[300px] pr-1">
                @forelse($departmentStats as $stat)
                    @php
                        $percentage = $totalMemos > 0 ? round(($stat->count / $totalMemos) * 100) : 0;
                    @endphp
                    <div>
                        <div class="flex items-center justify-between text-xs font-semibold text-slate-300 mb-1.5">
                            <span>{{ $stat->from_department }}</span>
                            <span>{{ $stat->count }} Memos</span>
                        </div>
                        <div class="w-full h-1.5 bg-slate-800/80 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-purple-500 to-indigo-500 rounded-full" style="width: {{ $percentage }}%;"></div>
                        </div>
                    </div>
                @empty
                    <div class="text-slate-500 text-xs py-8 text-center">No departments recorded.</div>
                @endforelse
            </div>
        </div>

    </div>

</div>

<!-- Interactive Modal for Inline Document Preview -->
<div id="preview-modal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="w-full max-w-4xl glass-card rounded-3xl overflow-hidden flex flex-col h-[85vh] shadow-2xl relative animate-zoom-in">
        
        <!-- Modal Header -->
        <div class="flex items-center justify-between h-16 px-6 border-b border-slate-800">
            <div>
                <h4 id="modal-title" class="font-bold text-slate-200 text-sm leading-none mb-1">Memo Preview</h4>
                <span id="modal-memo-number" class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider">No Number</span>
            </div>
            <button onclick="closePreviewModal()" class="text-slate-400 hover:text-white p-1 hover:bg-slate-850 rounded-xl transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Modal Content Container -->
        <div class="flex-1 flex overflow-hidden">
            <!-- Left: Metadata Panel -->
            <div class="w-72 border-r border-slate-800 bg-slate-900/40 p-6 hidden md:flex flex-col space-y-4 overflow-y-auto">
                <div>
                    <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">Category</span>
                    <p id="modal-category" class="text-sm font-semibold text-slate-200 mt-1">General</p>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">Originating Department</span>
                    <p id="modal-from" class="text-sm font-semibold text-slate-200 mt-1">-</p>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">Target Department</span>
                    <p id="modal-to" class="text-sm font-semibold text-slate-200 mt-1">-</p>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">Memo Date</span>
                    <p id="modal-date" class="text-sm font-semibold text-slate-200 mt-1">-</p>
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">Uploaded By</span>
                    <p id="modal-uploader" class="text-sm font-semibold text-slate-200 mt-1">-</p>
                </div>
                <div class="flex-1"></div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider block mb-1">Details/Description</span>
                    <p id="modal-desc" class="text-xs text-slate-400 bg-slate-950/20 p-3 rounded-xl border border-slate-800/80 max-h-[150px] overflow-y-auto">-</p>
                </div>
            </div>

            <!-- Right: Interactive Stream Viewer (PDF or Image) -->
            <div class="flex-1 bg-slate-950 flex items-center justify-center relative">
                <iframe id="pdf-viewer" class="w-full h-full border-none hidden"></iframe>
                <div id="image-viewer-container" class="hidden w-full h-full overflow-auto p-4 flex items-center justify-center">
                    <img id="image-viewer" src="" class="max-w-full max-h-full object-contain rounded-lg">
                </div>
                <div id="loader" class="absolute flex flex-col items-center justify-center space-y-3">
                    <div class="w-10 h-10 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
                    <span class="text-xs text-slate-500">Loading document stream...</span>
                </div>
            </div>
        </div>

        <!-- Modal Footer Actions -->
        <div class="h-16 px-6 border-t border-slate-800/80 flex items-center justify-end space-x-3 bg-slate-900/30">
            <a id="modal-download" href="" class="inline-flex items-center px-4 py-2 text-xs font-semibold text-white bg-slate-800 hover:bg-slate-700 rounded-xl border border-slate-700 transition-all">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Download Original File
            </a>
            <button onclick="closePreviewModal()" class="inline-flex items-center px-4 py-2 text-xs font-semibold text-slate-300 hover:text-white bg-slate-950 hover:bg-slate-900 rounded-xl border border-slate-800 transition-all">
                Close Viewer
            </button>
        </div>

    </div>
</div>

<script>
    function previewMemo(id) {
        const modal = document.getElementById('preview-modal');
        const pdfViewer = document.getElementById('pdf-viewer');
        const imgViewer = document.getElementById('image-viewer');
        const imgContainer = document.getElementById('image-viewer-container');
        const loader = document.getElementById('loader');
        
        // Open modal and show loader
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        loader.classList.remove('hidden');
        pdfViewer.classList.add('hidden');
        imgContainer.classList.add('hidden');
        
        // Set download link
        document.getElementById('modal-download').href = `/memos/${id}/download`;

        // Fetch metadata
        fetch(`/memos/${id}/show`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('modal-title').textContent = data.subject;
                document.getElementById('modal-memo-number').textContent = data.memo_number;
                document.getElementById('modal-category').textContent = data.category;
                document.getElementById('modal-from').textContent = data.from_department;
                document.getElementById('modal-to').textContent = data.to_department;
                document.getElementById('modal-date').textContent = data.memo_date;
                document.getElementById('modal-uploader').textContent = data.uploaded_by;
                document.getElementById('modal-desc').textContent = data.description;
            });

        // Fetch secure stream URL to determine MIME type and display
        const previewUrl = `/memos/${id}/preview`;
        
        fetch(previewUrl)
            .then(response => {
                const contentType = response.headers.get('Content-Type');
                return response.blob().then(blob => ({ blob, contentType }));
            })
            .then(({ blob, contentType }) => {
                const objectUrl = URL.createObjectURL(blob);
                loader.classList.add('hidden');

                if (contentType && contentType.includes('pdf')) {
                    pdfViewer.src = objectUrl;
                    pdfViewer.classList.remove('hidden');
                } else if (contentType && contentType.includes('image')) {
                    imgViewer.src = objectUrl;
                    imgContainer.classList.remove('hidden');
                } else {
                    // Fallback to iframe
                    pdfViewer.src = objectUrl;
                    pdfViewer.classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error("Preview failed:", error);
                loader.innerHTML = '<span class="text-xs text-rose-500">Failed to render file preview.</span>';
            });
    }

    function closePreviewModal() {
        const modal = document.getElementById('preview-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        
        // Clean up object URLs to free memory
        document.getElementById('pdf-viewer').src = '';
        document.getElementById('image-viewer').src = '';
    }
</script>
@endsection
