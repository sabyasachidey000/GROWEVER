@extends('layouts.app')
@section('title', 'Face Recognition Attendance - MES')
@section('page-title', 'Face Recognition Attendance')

@push('styles')
<style>
    .camera-panel {
        background: #111827;
        border-radius: 8px;
        overflow: hidden;
        position: relative;
        aspect-ratio: 16 / 9;
    }

    .camera-panel video {
        height: 100%;
        object-fit: cover;
        width: 100%;
    }

    .result-panel {
        min-height: 74px;
    }
</style>
@endpush

@section('content')
<div class="card mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-camera-video me-2"></i>Browser-Based Face Recognition</h5>
        <a href="{{ route('face-recognition.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-person-bounding-box me-1"></i>Enroll Operators
        </a>
    </div>
    <div class="card-body">
        <div class="row g-4">
            <!-- <div class="col-lg-6">
                <div class="camera-panel mb-3">
                    <video id="attendanceVideo" autoplay muted playsinline></video>
                </div>
                <div class="alert alert-info mb-0" id="cameraStatus">Loading face recognition models...</div>
            </div> -->
            <!-- <div class="col-lg-6">
                <form id="attendanceForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Machine</label>
                        <select name="machine_id" id="machineId" class="form-select" required>
                            <option value="">Select Machine</option>
                            @foreach($machines as $machine)
                                <option value="{{ $machine->id }}">{{ $machine->machine_code }} - {{ $machine->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Action</label>
                        <select name="action" id="attendanceAction" class="form-select" required>
                            <option value="face_in">Face In (Login)</option>
                            <option value="face_out">Face Out (Logout)</option>
                        </select>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" id="processButton" disabled>
                            <i class="bi bi-check-lg me-1"></i>Recognize & Mark Attendance
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="restartCamera">
                            <i class="bi bi-arrow-clockwise me-1"></i>Restart Camera
                        </button>
                    </div>
                </form>
                <div class="alert alert-secondary mt-3 result-panel" id="attendanceResult">
                    Select a machine, face the camera, then mark attendance.
                </div>
            </div> -->
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-white"><h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Active Sessions</h5></div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr><th>Machine</th><th>Operator</th><th>Shift</th><th>Login Time</th><th>Duration</th></tr></thead>
            <tbody>
                @forelse($activeSessions as $session)
                <tr>
                    <td><strong>{{ $session->machine->machine_code }}</strong> - {{ $session->machine->name }}</td>
                    <td>{{ $session->employee->name }} <small class="text-muted">({{ $session->employee->employee_code }})</small></td>
                    <td>{{ $session->shift?->name ?? '-' }}</td>
                    <td>{{ $session->login_time->format('H:i:s') }}</td>
                    <td>{{ $session->login_time->diffForHumans() }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted">No active sessions</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white"><h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Face Attendance Logs</h5></div>
    <div class="card-body p-0">
        <table class="table table-sm table-hover mb-0">
            <thead><tr><th>Time</th><th>Employee</th><th>Machine</th><th>Action</th><th>Confidence</th></tr></thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td><small>{{ $log->created_at->format('d M Y, H:i:s') }}</small></td>
                    <td>{{ $log->employee?->name ?? 'Unknown Face' }}</td>
                    <td>{{ $log->machine?->name ?? '-' }}</td>
                    <td>
                        <span class="badge bg-{{ in_array($log->action, ['face_in', 'face_out']) ? 'success' : 'secondary' }}">
                            {{ str_replace('_', ' ', $log->action) }}
                        </span>
                    </td>
                    <td>{{ $log->confidence !== null ? number_format((float) $log->confidence * 100, 1) . '%' : '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted">No face attendance logs</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-2 px-3">{{ $logs->links() }}</div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
<script>
    const modelUrl = 'https://justadudewhohacks.github.io/face-api.js/models';
    const video = document.getElementById('attendanceVideo');
    const statusBox = document.getElementById('cameraStatus');
    const resultBox = document.getElementById('attendanceResult');
    const processButton = document.getElementById('processButton');

    function setStatus(message, type = 'info') {
        statusBox.className = `alert alert-${type} mb-0`;
        statusBox.textContent = message;
    }

    function setResult(message, type = 'secondary') {
        resultBox.className = `alert alert-${type} mt-3 result-panel`;
        resultBox.textContent = message;
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
        processButton.disabled = false;
        setStatus('Camera ready. Keep one face centered in the frame.', 'success');
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

    document.getElementById('restartCamera').addEventListener('click', startCamera);

    document.getElementById('attendanceForm').addEventListener('submit', async function(event) {
        event.preventDefault();
        processButton.disabled = true;
        setStatus('Generating face descriptor...');
        setResult('Matching face with enrolled operators...');

        try {
            const descriptor = await getDescriptor();
            const response = await fetch('{{ route('face-recognition.attendance.process') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    machine_id: document.getElementById('machineId').value,
                    action: document.getElementById('attendanceAction').value,
                    descriptor,
                }),
            });

            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.message || 'Attendance failed.');
            }

            const confidence = data.data?.confidence ? ` Confidence: ${(data.data.confidence * 100).toFixed(1)}%.` : '';
            setResult(`${data.message}.${confidence}`, 'success');
            setStatus('Attendance marked. Refreshing sessions...', 'success');
            setTimeout(() => window.location.reload(), 1200);
        } catch (error) {
            setResult(error.message, 'danger');
            setStatus('Ready for another attempt.', 'warning');
            processButton.disabled = false;
        }
    });

    boot();
</script>
@endpush
