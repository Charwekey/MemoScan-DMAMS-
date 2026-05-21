@extends('layouts.app')

@section('header_title', 'Archive New Memo')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 animate-fade-in">
    
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left: Upload Method Selection and Document Scopes Form -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Upload/Scan Panel -->
            <div class="glass-card rounded-2xl p-6 shadow-lg">
                <h3 class="text-sm font-semibold text-slate-200 mb-4 uppercase tracking-wider">1. Select Document Source</h3>
                
                <!-- Tab Headers -->
                <div class="flex border-b border-slate-800 mb-6">
                    <button type="button" id="tab-upload-btn" onclick="switchTab('upload')"
                            class="flex-1 pb-3 text-xs font-bold uppercase tracking-wider text-indigo-400 border-b-2 border-indigo-500 focus:outline-none transition-all">
                        Upload File / Photo
                    </button>
                    <button type="button" id="tab-scan-btn" onclick="switchTab('scan')"
                            class="flex-1 pb-3 text-xs font-bold uppercase tracking-wider text-slate-500 border-b-2 border-transparent focus:outline-none hover:text-slate-300 transition-all">
                        Scan Document directly
                    </button>
                </div>

                <form id="memo-form" action="{{ route('memos.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Hidden field to store client-side compiled scanned PDF -->
                    <input type="hidden" name="scanned_file" id="scanned_file_input">

                    <!-- Tab 1: Upload File Container -->
                    <div id="tab-upload-container" class="space-y-4">
                        <div class="border-2 border-dashed border-slate-800 hover:border-slate-700 rounded-2xl p-8 flex flex-col items-center justify-center relative cursor-pointer group transition-all">
                            <input type="file" name="file_upload" id="file_upload" 
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                   onchange="handleFileSelect(this)">
                            
                            <div class="w-12 h-12 rounded-xl bg-indigo-500/10 flex items-center justify-center text-indigo-400 border border-indigo-500/10 group-hover:border-indigo-500/30 transition-all mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                            </div>
                            <span id="upload-label" class="text-xs font-semibold text-slate-300">Click or Drag & Drop File here</span>
                            <span class="text-[10px] text-slate-500 mt-1">Supported formats: PDF, JPG, JPEG, PNG (Max 10MB)</span>
                        </div>
                    </div>

                    <!-- Tab 2: Scan Document Container -->
                    <div id="tab-scan-container" class="hidden space-y-4">
                        <div class="bg-slate-950 rounded-2xl overflow-hidden border border-slate-800 p-4 space-y-4 flex flex-col items-center">
                            
                            <!-- Camera Select Dropdown -->
                            <div class="w-full">
                                <label for="camera-select" class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Select Camera Device</label>
                                <select id="camera-select" class="block w-full px-3 py-2 bg-slate-900 border border-slate-800 rounded-xl text-slate-300 text-xs focus:outline-none focus:ring-1 focus:ring-indigo-500" onchange="startCamera()"></select>
                            </div>

                            <!-- Webcam Video Stream -->
                            <div class="relative w-full aspect-video bg-slate-900 rounded-xl overflow-hidden border border-slate-800 flex items-center justify-center">
                                <video id="webcam-preview" autoplay playsinline class="w-full h-full object-cover"></video>
                                <canvas id="webcam-capture" class="hidden"></canvas>
                                
                                <!-- Camera loading indicator -->
                                <div id="camera-loader" class="absolute hidden flex-col items-center justify-center space-y-2 text-slate-400">
                                    <div class="w-8 h-8 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
                                    <span class="text-[10px]">Accessing camera stream...</span>
                                </div>
                            </div>

                            <!-- Scanning Control Actions -->
                            <div class="flex w-full space-x-2">
                                <button type="button" onclick="capturePage()" class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-semibold tracking-wide shadow-md transition-all">
                                    Capture Frame / Page
                                </button>
                                <button type="button" onclick="toggleCameraState()" id="camera-toggle-btn" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-semibold border border-slate-700 transition-all">
                                    Stop Camera
                                </button>
                            </div>

                        </div>

                        <!-- Scanned Pages Preview List Tray -->
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Scanned Pages Tray (<span id="page-count">0</span>)</span>
                                <button type="button" onclick="clearScannedPages()" class="text-[10px] font-semibold text-rose-400 hover:text-rose-300 transition-colors">Clear All Pages</button>
                            </div>
                            <div id="pages-tray" class="flex space-x-3 overflow-x-auto py-2 pr-2 min-h-[100px] border border-slate-800/80 rounded-xl bg-slate-950/20 px-3 items-center">
                                <span class="text-xs text-slate-500 py-4 mx-auto block text-center">No captured pages. Tap "Capture" above.</span>
                            </div>
                        </div>

                    </div>

                    <!-- Form Input Fields (Subject, Category, Dept, etc.) -->
                    <div class="mt-6 pt-6 border-t border-slate-800/80 space-y-4">
                        <h3 class="text-sm font-semibold text-slate-200 mb-2 uppercase tracking-wider">2. Document Metadata</h3>
                        
                        <!-- Subject -->
                        <div>
                            <label for="subject" class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Memo Subject</label>
                            <input type="text" name="subject" id="subject" value="{{ old('subject') }}" required
                                   class="block w-full px-3.5 py-3 bg-slate-950/40 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs transition-all"
                                   placeholder="e.g. End of Semester Examination Timetable">
                        </div>

                        <!-- Department Origin & Destination -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="from_department" class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Originating Dept. (From)</label>
                                <input type="text" name="from_department" id="from_department" value="{{ old('from_department') }}" required
                                       class="block w-full px-3.5 py-3 bg-slate-950/40 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs transition-all"
                                       placeholder="e.g. Registry">
                            </div>
                            <div>
                                <label for="to_department" class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Recipient Dept. (To)</label>
                                <input type="text" name="to_department" id="to_department" value="{{ old('to_department') }}" required
                                       class="block w-full px-3.5 py-3 bg-slate-950/40 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs transition-all"
                                       placeholder="e.g. Academic Affairs">
                            </div>
                        </div>

                        <!-- Memo Date & Category -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="memo_date" class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Memo Date</label>
                                <input type="date" name="memo_date" id="memo_date" value="{{ old('memo_date', date('Y-m-d')) }}" required
                                       class="block w-full px-3.5 py-3 bg-slate-950/40 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs transition-all">
                            </div>
                            <div>
                                <label for="category" class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Document Category</label>
                                <select name="category" id="category" required
                                        class="block w-full px-3.5 py-3 bg-slate-950/40 border border-slate-800 rounded-xl text-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs transition-all">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category }}" {{ old('category') === $category ? 'selected' : '' }}>{{ $category }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Description / Summary (Optional)</label>
                            <textarea name="description" id="description" rows="3"
                                      class="block w-full px-3.5 py-3 bg-slate-950/40 border border-slate-800 rounded-xl text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs transition-all"
                                      placeholder="Brief summary of the memo content..."></textarea>
                        </div>

                    </div>

                    <!-- Form Actions -->
                    <div class="mt-6 pt-6 border-t border-slate-800/80 flex justify-end space-x-3">
                        <a href="{{ route('memos.index') }}" class="inline-flex items-center px-5 py-3 text-xs font-semibold text-slate-300 hover:text-white bg-slate-950 border border-slate-850 rounded-xl transition-all">
                            Cancel
                        </a>
                        <button type="submit" id="submit-btn" class="inline-flex items-center px-6 py-3 text-xs font-semibold text-white bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 rounded-xl shadow-lg transition-all transform active:scale-98">
                            Archive Document
                        </button>
                    </div>

                </form>

            </div>

        </div>

        <!-- Right Side: Scanning Help Info & Quick Guidelines -->
        <div class="space-y-6">
            <div class="glass-card rounded-2xl p-6 shadow-lg space-y-4">
                <h4 class="text-xs font-bold uppercase text-slate-200 tracking-wider">Archiving Guidelines</h4>
                <ul class="text-xs text-slate-400 space-y-2.5 list-disc pl-4">
                    <li><strong class="text-slate-300">File Naming</strong>: The system will automatically rename files and assign a unique tracking number based on originating department and year.</li>
                    <li><strong class="text-slate-300">PDF Generator</strong>: If you choose to scan, make sure your pages are clear. They will be automatically combined into an A4 PDF before transmission.</li>
                    <li><strong class="text-slate-300">Image uploads</strong>: Direct uploads of images (JPG, PNG) will remain as image formats. PDFs are recommended for multi-page documents.</li>
                </ul>
            </div>
        </div>

    </div>

</div>

<!-- Loading jsPDF library via CDN for compilation -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
    let activeTab = 'upload';
    let localStream = null;
    let scannedPages = []; // Holds base64 page images

    function switchTab(tab) {
        activeTab = tab;
        
        const tabUploadBtn = document.getElementById('tab-upload-btn');
        const tabScanBtn = document.getElementById('tab-scan-btn');
        const uploadContainer = document.getElementById('tab-upload-container');
        const scanContainer = document.getElementById('tab-scan-container');

        if (tab === 'upload') {
            tabUploadBtn.className = "flex-1 pb-3 text-xs font-bold uppercase tracking-wider text-indigo-400 border-b-2 border-indigo-500 focus:outline-none transition-all";
            tabScanBtn.className = "flex-1 pb-3 text-xs font-bold uppercase tracking-wider text-slate-500 border-b-2 border-transparent focus:outline-none hover:text-slate-300 transition-all";
            uploadContainer.classList.remove('hidden');
            scanContainer.classList.add('hidden');
            
            stopCamera();
        } else {
            tabScanBtn.className = "flex-1 pb-3 text-xs font-bold uppercase tracking-wider text-indigo-400 border-b-2 border-indigo-500 focus:outline-none transition-all";
            tabUploadBtn.className = "flex-1 pb-3 text-xs font-bold uppercase tracking-wider text-slate-500 border-b-2 border-transparent focus:outline-none hover:text-slate-300 transition-all";
            scanContainer.classList.remove('hidden');
            uploadContainer.classList.add('hidden');
            
            initCameraSelection();
        }
    }

    function handleFileSelect(input) {
        const label = document.getElementById('upload-label');
        if (input.files && input.files[0]) {
            label.textContent = "Selected: " + input.files[0].name;
            label.classList.add('text-indigo-400');
        } else {
            label.textContent = "Click or Drag & Drop File here";
            label.classList.remove('text-indigo-400');
        }
    }

    // Interactive HTML5 Camera Code
    function initCameraSelection() {
        const select = document.getElementById('camera-select');
        select.innerHTML = '';

        if (!navigator.mediaDevices || !navigator.mediaDevices.enumerateDevices) {
            alert("Your browser does not support MediaDevices API or cameras.");
            return;
        }

        navigator.mediaDevices.enumerateDevices()
            .then(devices => {
                const videoDevices = devices.filter(device => device.kind === 'videoinput');
                
                if (videoDevices.length === 0) {
                    select.innerHTML = '<option value="">No Camera Found</option>';
                    return;
                }

                videoDevices.forEach((device, index) => {
                    const option = document.createElement('option');
                    option.value = device.deviceId;
                    option.textContent = device.label || `Camera ${index + 1}`;
                    select.appendChild(option);
                });

                startCamera();
            })
            .catch(err => {
                console.error("Device listing failed:", err);
                alert("Permission denied or camera error.");
            });
    }

    function startCamera() {
        stopCamera();

        const video = document.getElementById('webcam-preview');
        const select = document.getElementById('camera-select');
        const loader = document.getElementById('camera-loader');
        
        loader.classList.remove('hidden');

        const constraints = {
            video: {
                deviceId: select.value ? { exact: select.value } : undefined,
                width: { ideal: 1280 },
                height: { ideal: 720 }
            }
        };

        navigator.mediaDevices.getUserMedia(constraints)
            .then(stream => {
                localStream = stream;
                video.srcObject = stream;
                video.onloadedmetadata = () => {
                    video.play();
                    loader.classList.add('hidden');
                };
                document.getElementById('camera-toggle-btn').textContent = "Stop Camera";
            })
            .catch(err => {
                console.error("Camera access failed:", err);
                loader.classList.add('hidden');
                alert("Could not start camera. Check permissions.");
            });
    }

    function stopCamera() {
        const video = document.getElementById('webcam-preview');
        if (localStream) {
            localStream.getTracks().forEach(track => track.stop());
            localStream = null;
        }
        video.srcObject = null;
        document.getElementById('camera-toggle-btn').textContent = "Start Camera";
    }

    function toggleCameraState() {
        if (localStream) {
            stopCamera();
        } else {
            startCamera();
        }
    }

    function capturePage() {
        const video = document.getElementById('webcam-preview');
        const canvas = document.getElementById('webcam-capture');
        
        if (!localStream) {
            alert("Please start the camera first.");
            return;
        }

        const ctx = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        
        // Draw video frame to canvas
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        // Get base64 JPG image
        const imgData = canvas.toDataURL('image/jpeg', 0.85);
        
        scannedPages.push(imgData);
        renderPagesTray();
    }

    function renderPagesTray() {
        const tray = document.getElementById('pages-tray');
        const countSpan = document.getElementById('page-count');
        countSpan.textContent = scannedPages.length;

        if (scannedPages.length === 0) {
            tray.innerHTML = '<span class="text-xs text-slate-500 py-4 mx-auto block text-center">No captured pages. Tap "Capture" above.</span>';
            return;
        }

        tray.innerHTML = '';
        scannedPages.forEach((pageData, index) => {
            const wrapper = document.createElement('div');
            wrapper.className = "relative shrink-0 w-20 h-28 bg-slate-900 border border-slate-700 rounded-lg overflow-hidden group";
            
            const img = document.createElement('img');
            img.src = pageData;
            img.className = "w-full h-full object-cover";
            wrapper.appendChild(img);

            // Page label
            const label = document.createElement('span');
            label.className = "absolute bottom-1 left-1 bg-slate-950/80 text-[9px] font-bold text-white px-1.5 py-0.5 rounded";
            label.textContent = `Page ${index + 1}`;
            wrapper.appendChild(label);

            // Delete action button
            const delBtn = document.createElement('button');
            delBtn.type = "button";
            delBtn.className = "absolute top-1 right-1 bg-rose-600/90 text-white rounded p-0.5 opacity-0 group-hover:opacity-100 transition-opacity hover:bg-rose-500";
            delBtn.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
            delBtn.onclick = () => removePage(index);
            wrapper.appendChild(delBtn);

            tray.appendChild(wrapper);
        });
    }

    function removePage(index) {
        scannedPages.splice(index, 1);
        renderPagesTray();
    }

    function clearScannedPages() {
        scannedPages = [];
        renderPagesTray();
    }

    // Intercept form submit to assemble PDF if scanned tab is active
    document.getElementById('memo-form').addEventListener('submit', function(e) {
        if (activeTab === 'scan') {
            if (scannedPages.length === 0) {
                e.preventDefault();
                alert("Please capture at least one frame/page to upload scanned memo.");
                return;
            }

            e.preventDefault(); // Stop standard submit to compile

            const submitBtn = document.getElementById('submit-btn');
            submitBtn.disabled = true;
            submitBtn.textContent = "Compiling PDF Scanner...";

            // Stop camera to release hardware
            stopCamera();

            // Run compiler in a short timeout to prevent UI lockup
            setTimeout(() => {
                try {
                    const { jsPDF } = window.jspdf;
                    
                    // Setup A4 layout: 210 x 297 mm
                    const doc = new jsPDF('p', 'mm', 'a4');
                    
                    scannedPages.forEach((pageData, index) => {
                        if (index > 0) {
                            doc.addPage();
                        }
                        doc.addImage(pageData, 'JPEG', 0, 0, 210, 297);
                    });

                    // Get base64 PDF representation
                    const base64Pdf = doc.output('datauristring');
                    
                    // Assign to hidden input field
                    document.getElementById('scanned_file_input').value = base64Pdf;

                    // Submit form programmatically
                    document.getElementById('memo-form').submit();
                } catch (error) {
                    console.error("PDF Assembly failed:", error);
                    alert("An error occurred during PDF compilation. Please try again.");
                    submitBtn.disabled = false;
                    submitBtn.textContent = "Archive Document";
                }
            }, 100);
        }
    });
</script>
@endsection
