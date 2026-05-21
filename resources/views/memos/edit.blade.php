@extends('layouts.app')

@section('header_title', 'Edit Memo Details')

@section('content')
<div class="max-w-2xl mx-auto space-y-6 animate-fade-in">
    
    <div class="flex items-center justify-between">
        <a href="{{ route('memos.index') }}" class="inline-flex items-center text-xs font-semibold text-slate-400 hover:text-white transition-colors">
            &larr; Back to Archive List
        </a>
    </div>

    <!-- Error/Validation Banner -->
    @if ($errors->any())
        <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs space-y-1">
            <span class="font-bold block">Validation Errors:</span>
            <ul class="list-disc pl-4 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Edit Form Card -->
    <div class="glass-card rounded-2xl p-6 shadow-lg">
        <h3 class="text-sm font-semibold text-slate-200 mb-6 uppercase tracking-wider">Modify Memo Information</h3>

        <!-- Immutable Details Banner -->
        <div class="p-4 rounded-xl bg-slate-900/60 border border-slate-800 text-xs space-y-2 mb-6 text-slate-400">
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider block">Memo Tracking Number</span>
                    <strong class="text-indigo-400 font-semibold">{{ $memo->memo_number }}</strong>
                </div>
                <div>
                    <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider block">Uploaded By</span>
                    <strong class="text-slate-300 font-semibold">{{ $memo->uploadedBy->name }}</strong>
                </div>
            </div>
            <div class="pt-2 border-t border-slate-800/80">
                <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider block">File Attachment</span>
                <a href="{{ route('memos.download', $memo->id) }}" class="inline-flex items-center text-emerald-400 hover:text-emerald-300 font-semibold mt-0.5">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Download Associated Document ({{ basename($memo->file_path) }})
                </a>
            </div>
        </div>

        <form action="{{ route('memos.update', $memo->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-4">
                
                <!-- Subject -->
                <div>
                    <label for="subject" class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Memo Subject</label>
                    <input type="text" name="subject" id="subject" value="{{ old('subject', $memo->subject) }}" required
                           class="block w-full px-3.5 py-3 bg-slate-950/40 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs transition-all">
                </div>

                <!-- Department Origin & Destination -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="from_department" class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Originating Dept. (From)</label>
                        <input type="text" name="from_department" id="from_department" value="{{ old('from_department', $memo->from_department) }}" required
                               class="block w-full px-3.5 py-3 bg-slate-950/40 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs transition-all">
                    </div>
                    <div>
                        <label for="to_department" class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Recipient Dept. (To)</label>
                        <input type="text" name="to_department" id="to_department" value="{{ old('to_department', $memo->to_department) }}" required
                               class="block w-full px-3.5 py-3 bg-slate-950/40 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs transition-all">
                    </div>
                </div>

                <!-- Memo Date & Category -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="memo_date" class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Memo Date</label>
                        <input type="date" name="memo_date" id="memo_date" value="{{ old('memo_date', $memo->memo_date->format('Y-m-d')) }}" required
                               class="block w-full px-3.5 py-3 bg-slate-950/40 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs transition-all">
                    </div>
                    <div>
                        <label for="category" class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Document Category</label>
                        <select name="category" id="category" required
                                class="block w-full px-3.5 py-3 bg-slate-950/40 border border-slate-800 rounded-xl text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs transition-all">
                            @foreach($categories as $category)
                                <option value="{{ $category }}" {{ old('category', $memo->category) === $category ? 'selected' : '' }}>{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Description / Summary (Optional)</label>
                    <textarea name="description" id="description" rows="4"
                              class="block w-full px-3.5 py-3 bg-slate-950/40 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs transition-all">{{ old('description', $memo->description) }}</textarea>
                </div>

            </div>

            <!-- Form Actions -->
            <div class="mt-6 pt-6 border-t border-slate-800/80 flex justify-end space-x-3">
                <a href="{{ route('memos.index') }}" class="inline-flex items-center px-5 py-3 text-xs font-semibold text-slate-300 hover:text-white bg-slate-950 border border-slate-850 rounded-xl transition-all">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center px-6 py-3 text-xs font-semibold text-white bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 rounded-xl shadow-lg transition-all transform active:scale-98">
                    Save Changes
                </button>
            </div>

        </form>
    </div>

</div>
@endsection
