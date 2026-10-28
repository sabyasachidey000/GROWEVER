@extends('layouts.app')
@section('title', 'Face Recognition Management - MES')
@section('page-title', 'Face Recognition Management')

@push('styles')
<style>
    .camera-box {
        background: #111827;
        border-radius: 8px;
        overflow: hidden;
        position: relative;
        aspect-ratio: 16 / 9;
    }

    .camera-box video {
        height: 100%;
        object-fit: cover;
        width: 100%;
    }
</style>
@endpush

@section('content')
<div class="card mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-person-bounding-box me-2"></i>Enroll Face Descriptor</h5>
        <a href="{{ route('face-recognition.attendance') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-camera-video me-1"></i>Attendance
        </a>
    </div>
    <div class="card-body">
        <div class="row g-4">
            <div class="col-lg-5">
                <div class="camera-box mb-3">
                    <video id="enrollVideo" autoplay muted playsinline></video>
                </div>
                <div class="alert alert-info mb-0" id="cameraStatus">
                    Loading face recognition models...
                </div>
            </div>
            <div class="col-lg-7">
                <form id="enrollForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Operator</label>
                        <select name="employee_id" id="employeeId" class="form-select" required>
                            <option value="">Select Operator</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">
                                    {{ $employee->name }} ({{ $employee->employee_code }})
                                    @if($employee->face_descriptor) - Already Enrolled @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Assigned Machine</label>
                        <select name="machine_id" id="machineId" class="form-select" required>
                            <option value="">Select Machine</option>
                            @foreach($machines as $machine)
                                <option value="{{ $machine->id }}">{{ $machine->machine_code }} - {{ $machine->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="card border-0 bg-light mb-3">
                        <div class="card-body">
                            <h6 class="mb-2">Guided Registration</h6>
                            <p class="text-muted small mb-3">
                                Capture 5 samples with different face angles and expressions for better operator login accuracy.
                            </p>
                            <div class="alert alert-secondary mb-3" id="captureInstruction">
                                Keep a neutral expression and look straight into the camera.
                            </div>
                            <div class="small text-muted mb-2" id="captureProgress">0 of 5 samples captured</div>
                            <ul class="list-group list-group-flush small" id="sampleChecklist"></ul>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary" id="captureSampleButton" disabled>
                            <i class="bi bi-camera me-1"></i>Capture Sample
                        </button>
                        <button type="submit" class="btn btn-success" id="enrollButton" disabled>
                            <i class="bi bi-check-lg me-1"></i>Save Enrollment
                        </button>
                        <button type="button" class="btn btn-outline-warning" id="resetSamples">
                            <i class="bi bi-eraser me-1"></i>Reset Samples
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="restartCamera">
                            <i class="bi bi-arrow-clockwise me-1"></i>Restart Camera
                        </button>
                    </div>
                </form>

                <div class="alert mt-3 d-none" id="enrollResult"></div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white"><h5 class="mb-0"><i class="bi bi-table me-2"></i>Face Enrollment Status</h5></div>
    <div class="card-body p-0">
        <table class="table table-hover datatable mb-0">
            <thead><tr><th>Operator</th><th>Department</th><th>Designation</th><th>Assigned Machine</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($employees as $employee)
                <tr>
                    <td><strong>{{ $employee->name }}</strong> <small class="text-muted">({{ $employee->employee_code }})</small></td>
                    <td>{{ $employee->department?->name ?? '-' }}</td>
                    <td>{{ $employee->designation?->name ?? '-' }}</td>
                    <td>{{ $employee->faceMachine ? $employee->faceMachine->machine_code . ' - ' . $employee->faceMachine->name : '-' }}</td>
                    <td>
                        @if($employee->face_descriptor)
                            <span class="badge bg-success">Enrolled</span>
                        @else
                            <span class="badge bg-secondary">Not Enrolled</span>
                        @endif
                    </td>
                    <td>
                        @if($employee->face_descriptor)
                            <form action="{{ route('face-recognition.unassign', $employee) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Remove face enrollment for {{ $employee->name }}?')">
                                @csrf
                                <button class="btn btn-sm btn-outline-danger" title="Remove enrollment">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                            </form>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted">No operators found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script>
    const modelUrl = 'https://justadudewhohacks.github.io/face-api.js/models';
    const video = document.getElementById('enrollVideo');
    const statusBox = document.getElementById('cameraStatus');
    const resultBox = document.getElementById('enrollResult');
    const enrollButton = document.getElementById('enrollButton');
    const captureSampleButton = document.getElementById('captureSampleButton');
    const captureInstruction = document.getElementById('captureInstruction');
    const captureProgress = document.getElementById('captureProgress');
    const sampleChecklist = document.getElementById('sampleChecklist');
    const samplePrompts = [
        'Look straight with a neutral expression.',
        'Turn your face slightly to the left.',
        'Turn your face slightly to the right.',
        'Lift your chin slightly or look a little upward.',
        'Smile naturally or change your expression slightly.',
    ];
    let capturedDescriptors = [];

    function setStatus(message, type = 'info') {
        statusBox.className = `alert alert-${type} mb-0`;
        statusBox.textContent = message;
    }

    function setResult(message, type) {
        resultBox.className = `alert alert-${type} mt-3`;
        resultBox.textContent = message;
    }

    function renderSampleChecklist() {
        sampleChecklist.innerHTML = samplePrompts.map((prompt, index) => `
            <li class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent">
                <span>${index + 1}. ${prompt}</span>
                <span class="badge ${capturedDescriptors[index] ? 'bg-success' : 'bg-secondary'}">
                    ${capturedDescriptors[index] ? 'Captured' : 'Pending'}
                </span>
            </li>
        `).join('');

        const nextPrompt = samplePrompts[capturedDescriptors.length] ?? 'All samples captured. Save enrollment now.';
        captureInstruction.textContent = nextPrompt;
        captureProgress.textContent = `${capturedDescriptors.length} of ${samplePrompts.length} samples captured`;
        enrollButton.disabled = capturedDescriptors.length !== samplePrompts.length;
        captureSampleButton.disabled = capturedDescriptors.length >= samplePrompts.length || !video.srcObject;
    }

    async function loadModels() {
        await Promise.all([
            faceapi.nets.tinyFaceDetector.loadFromUri(modelUrl),
            faceapi.nets.faceLandmark68Net.loadFromUri(modelUrl),
            faceapi.nets.faceRecognitionNet.loadFromUri(modelUrl),
        ]);
    }

    async function startCamera() {
        if (video.srcObject) {
            video.srcObject.getTracks().forEach(track => track.stop());
        }

        const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false });
        video.srcObject = stream;
        setStatus('Camera ready. Keep one face centered in the frame and capture all guided samples.', 'success');
        renderSampleChecklist();
    }

    async function getDescriptor() {
        const detection = await faceapi
            .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions())
            .withFaceLandmarks()
            .withFaceDescriptor();

        if (!detection) {
            throw new Error('No face detected. Move closer and keep your face centered.');
        }

        return Array.from(detection.descriptor);
    }

    async function boot() {
        try {
            setStatus('Loading face recognition models...');
            await loadModels();
            await startCamera();
        } catch (error) {
            setStatus(error.message || 'Camera or model loading failed.', 'danger');
        }
    }

    function resetSamples() {
        capturedDescriptors = [];
        setResult('Captured samples cleared. Start registration again.', 'secondary');
        renderSampleChecklist();
    }

    document.getElementById('restartCamera').addEventListener('click', startCamera);
    document.getElementById('resetSamples').addEventListener('click', resetSamples);

    document.getElementById('captureSampleButton').addEventListener('click', async function() {
        captureSampleButton.disabled = true;
        setStatus(`Capturing sample ${capturedDescriptors.length + 1} of ${samplePrompts.length}...`);

        try {
            const descriptor = await getDescriptor();
            capturedDescriptors.push(descriptor);
            setResult(`Sample ${capturedDescriptors.length} captured successfully.`, 'success');
            setStatus('Sample saved. Continue with the next angle or expression.', 'success');
            renderSampleChecklist();
        } catch (error) {
            setResult(error.message, 'danger');
            setStatus('Please adjust your face and try again.', 'warning');
            renderSampleChecklist();
        }
    });

    document.getElementById('enrollForm').addEventListener('submit', async function(event) {
        event.preventDefault();
        if (capturedDescriptors.length !== samplePrompts.length) {
            setResult('Please capture all 5 guided samples before saving.', 'warning');
            setStatus('Complete the remaining face angles and expressions.', 'warning');
            renderSampleChecklist();
            return;
        }

        enrollButton.disabled = true;
        setStatus('Saving guided face enrollment...');

        try {
            const response = await fetch('{{ route('face-recognition.enroll') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    employee_id: document.getElementById('employeeId').value,
                    machine_id: document.getElementById('machineId').value,
                    descriptors: capturedDescriptors,
                }),
            });

            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.message || 'Enrollment failed.');
            }

            setResult(data.message, 'success');
            setStatus('Enrollment saved. Refreshing...', 'success');
            setTimeout(() => window.location.reload(), 1000);
        } catch (error) {
            setResult(error.message, 'danger');
            setStatus('Complete all samples and try saving again.', 'warning');
            renderSampleChecklist();
        }
    });

    renderSampleChecklist();
    boot();
</script>
@endpush
