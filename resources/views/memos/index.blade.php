@extends('layouts.app')

@section('header_title', 'Memos Archive')

@section('content')
<div class="space-y-6 animate-fade-in">

    <!-- 1. Search and Filtering Accordion -->
    <div class="glass-card rounded-2xl p-6 shadow-lg">
        <form action="{{ route('memos.index') }}" method="GET" class="space-y-4">
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                
                <!-- Keyword search -->
                <div class="md:col-span-2">
                    <label for="search" class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Search Keywords</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                               class="block w-full pl-9 pr-4 py-2.5 bg-slate-950/40 border border-slate-800 rounded-xl text-slate-200 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs transition-all"
                               placeholder="Subject, memo number, description...">
                    </div>
                </div>

                <!-- Category -->
                <div>
                    <label for="category" class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Category</label>
                    <select name="category" id="category" 
                            class="block w-full px-3 py-2.5 bg-slate-950/40 border border-slate-800 rounded-xl text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs transition-all">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>{{ $category }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Department -->
                <div>
                    <label for="department" class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Department</label>
                    <select name="department" id="department" 
                            class="block w-full px-3 py-2.5 bg-slate-950/40 border border-slate-800 rounded-xl text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs transition-all">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept }}" {{ request('department') === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>

            </div>

            <!-- Date Range & Admin Trashed Filters -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 pt-2 border-t border-slate-800/60">
                
                <!-- Start Date -->
                <div>
                    <label for="start_date" class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Start Date</label>
                    <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}"
                           class="block w-full px-3 py-2.5 bg-slate-950/40 border border-slate-800 rounded-xl text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs transition-all">
                </div>

                <!-- End Date -->
                <div>
                    <label for="end_date" class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">End Date</label>
                    <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}"
                           class="block w-full px-3 py-2.5 bg-slate-950/40 border border-slate-800 rounded-xl text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs transition-all">
                </div>

                <!-- Admin Trashed Toggle -->
                <div class="flex items-center md:col-span-2 pt-5">
                    @if(Auth::user()->isAdmin())
                        <label class="inline-flex items-center cursor-pointer select-none">
                            <input type="checkbox" name="show_deleted" value="1" {{ request('show_deleted') ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-9 h-5 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-slate-400 after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-rose-600 peer-checked:after:bg-white relative"></div>
                            <span class="ml-2 text-xs font-semibold text-rose-400">Show deleted memos only</span>
                        </label>
                    @endif
                    
                    <div class="ml-auto space-x-2">
                        <a href="{{ route('memos.index') }}" class="inline-flex items-center px-4 py-2.5 text-xs font-semibold text-slate-400 hover:text-white bg-slate-950 hover:bg-slate-900 border border-slate-800 rounded-xl transition-all">
                            Clear Filters
                        </a>
                        <button type="submit" class="inline-flex items-center px-5 py-2.5 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-xl shadow-md transition-all">
                            Apply Search
                        </button>
                    </div>
                </div>

            </div>

        </form>
    </div>

    <!-- 2. Memos Table List -->
    <div class="glass-card rounded-2xl p-6 shadow-lg overflow-hidden flex flex-col">
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-800 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                        <th class="pb-3 pr-2">Document Details</th>
                        <th class="pb-3 pr-2">Originating</th>
                        <th class="pb-3 pr-2">Target</th>
                        <th class="pb-3 pr-2">Category</th>
                        <th class="pb-3 pr-2">Date</th>
                        <th class="pb-3 pr-2">Uploaded By</th>
                        <th class="pb-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80 text-xs text-slate-300">
                    @forelse($memos as $memo)
                        <tr class="group hover:bg-slate-800/10 transition-all {{ $memo->trashed() ? 'bg-rose-500/5' : '' }}">
                            
                            <!-- Document Details -->
                            <td class="py-3.5 pr-2">
                                <span class="font-bold text-slate-200 block truncate max-w-[220px]" title="{{ $memo->subject }}">{{ $memo->subject }}</span>
                                <span class="text-[10px] text-slate-500 font-semibold">{{ $memo->memo_number }}</span>
                                @if($memo->trashed())
                                    <span class="inline-flex items-center ml-2 px-1.5 py-0.5 rounded text-[9px] font-bold bg-rose-500/15 text-rose-400 uppercase leading-none border border-rose-500/20">Trashed</span>
                                @endif
                            </td>
                            
                            <!-- Originating -->
                            <td class="py-3.5 pr-2">
                                <span class="text-slate-200 block truncate max-w-[150px]">{{ $memo->from_department }}</span>
                            </td>
                            
                            <!-- Target -->
                            <td class="py-3.5 pr-2">
                                <span class="text-slate-400 block truncate max-w-[150px]">{{ $memo->to_department }}</span>
                            </td>
                            
                            <!-- Category -->
                            <td class="py-3.5 pr-2">
                                @php
                                    $pillColor = match($memo->category) {
                                        'Academic' => 'bg-indigo-500/15 text-indigo-400 border-indigo-500/10',
                                        'Finance' => 'bg-emerald-500/15 text-emerald-400 border-emerald-500/10',
                                        'Registry' => 'bg-purple-500/15 text-purple-400 border-purple-500/10',
                                        'HR' => 'bg-rose-500/15 text-rose-400 border-rose-500/10',
                                        'Student Affairs' => 'bg-cyan-500/15 text-cyan-400 border-cyan-500/10',
                                        default => 'bg-slate-500/15 text-slate-400 border-slate-500/10'
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium border {{ $pillColor }}">
                                    {{ $memo->category }}
                                </span>
                            </td>
                            
                            <!-- Date -->
                            <td class="py-3.5 pr-2 whitespace-nowrap">
                                <span>{{ date('M d, Y', strtotime($memo->memo_date)) }}</span>
                            </td>
                            
                            <!-- Uploaded By -->
                            <td class="py-3.5 pr-2 truncate max-w-[100px]" title="{{ $memo->uploadedBy->name }}">
                                <span>{{ $memo->uploadedBy->name }}</span>
                            </td>
                            
                            <!-- Actions -->
                            <td class="py-3.5 text-right space-x-1 whitespace-nowrap">
                                @if(!$memo->trashed())
                                    <!-- Preview -->
                                    <button onclick="previewMemo({{ $memo->id }})" class="p-1.5 rounded-lg bg-slate-800 text-indigo-400 hover:text-white hover:bg-indigo-600 border border-slate-700 transition-all inline-flex items-center justify-center" title="Preview Memo">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>

                                    <!-- Download -->
                                    <a href="{{ route('memos.download', $memo->id) }}" class="p-1.5 rounded-lg bg-slate-800 text-emerald-400 hover:text-white hover:bg-emerald-600 border border-slate-700 transition-all inline-flex items-center justify-center" title="Download Original">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    </a>

                                    <!-- Edit (Admin or creator) -->
                                    @if(Auth::user()->isAdmin() || (Auth::user()->isStaff() && $memo->uploaded_by === Auth::id()))
                                        <a href="{{ route('memos.edit', $memo->id) }}" class="p-1.5 rounded-lg bg-slate-800 text-amber-400 hover:text-white hover:bg-amber-600 border border-slate-700 transition-all inline-flex items-center justify-center" title="Edit Memo">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                    @endif

                                    <!-- Soft Delete (Admin only) -->
                                    @if(Auth::user()->isAdmin())
                                        <form action="{{ route('memos.destroy', $memo->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to soft-delete this memo? it will be moved to the archive trash bin.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg bg-slate-800 text-rose-400 hover:text-white hover:bg-rose-600 border border-slate-700 transition-all inline-flex items-center justify-center" title="Archive / Delete">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    <!-- Restore (Admin only for soft-deleted items) -->
                                    @if(Auth::user()->isAdmin())
                                        <form action="{{ route('memos.restore', $memo->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            <button type="submit" class="p-1.5 rounded-lg bg-slate-800 text-emerald-400 hover:text-white hover:bg-emerald-600 border border-slate-700 transition-all inline-flex items-center justify-center" title="Restore Memo">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 6H16"></path></svg>
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-500 font-medium">No memos archived under these filter criteria.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- 3. Pagination Controls -->
        <div class="mt-6 border-t border-slate-800 pt-4 flex justify-between items-center text-slate-400 text-xs">
            <div>
                Showing {{ $memos->firstItem() ?? 0 }} to {{ $memos->lastItem() ?? 0 }} of {{ $memos->total() }} results
            </div>
            <div class="flex space-x-1.5">
                {{ $memos->links('pagination::simple-tailwind') }}
            </div>
        </div>

    </div>

</div>

<!-- Interactive Modal for Inline Document Preview (same modal as Dashboard) -->
<div id="preview-modal" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="w-full max-w-4xl glass-card rounded-3xl overflow-hidden flex flex-col h-[85vh] shadow-2xl relative animate-zoom-in">
        
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

        <div class="flex-1 flex overflow-hidden">
            <!-- Left Metadata -->
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

            <!-- Right Viewer -->
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
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        loader.classList.remove('hidden');
        pdfViewer.classList.add('hidden');
        imgContainer.classList.add('hidden');
        
        document.getElementById('modal-download').href = `/memos/${id}/download`;

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
        
        document.getElementById('pdf-viewer').src = '';
        document.getElementById('image-viewer').src = '';
    }
</script>
@endsection
