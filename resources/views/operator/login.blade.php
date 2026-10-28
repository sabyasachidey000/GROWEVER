<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Operator Face Login - MTS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: #eef2f7;
            min-height: 100vh;
        }

        .login-shell {
            min-height: 100vh;
        }

        .camera-panel {
    background: #111827;
    border-radius: 10px;
    overflow: hidden;
    aspect-ratio: 4 / 3;
    max-width: 420px;
    margin: 0 auto;
}

.camera-panel video {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Tablet */
@media (max-width: 768px) {

    .card-header h4{
        font-size:1.1rem;
    }

    .card-header small{
        font-size:.75rem;
    }

    .camera-panel{
        max-width:300px;
        max-height:220px;
    }

    #cameraStatus,
    #loginResult{
        font-size:.85rem;
        padding:.45rem .6rem;
        margin-bottom:.5rem;
    }

    .login-actions{
        display:flex;
        flex-direction:column;
        gap:.5rem;
    }

    .login-actions .btn{
        width:100%;
        padding:.55rem;
        font-size:.9rem;
    }
}

/* Small Mobile */
@media (max-width:480px){

    .camera-panel{
        max-width:240px;
        max-height:180px;
    }

    .card-body{
        padding:.75rem;
    }

    .card-header{
        padding:.75rem;
    }

    #cameraStatus,
    #loginResult{
        font-size:.8rem;
    }

    .login-actions .btn{
        font-size:.85rem;
        padding:.5rem;
    }

    #openCameraButton{
        width:100%;
    }
}
    </style>
</head>
<body>
    <main class="login-shell d-flex align-items-center py-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-8 col-lg-9">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-0"><i class="bi bi-person-video3 me-2"></i>Operator Login</h4>
                                <small class="text-muted">Face recognition will open your assigned machine dashboard.</small>
                            </div>
                            
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif
                            @if(session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif

                            <div class="text-center py-4" id="cameraGate">
                                <div class="mb-3">
                                    <i class="bi bi-camera-video" style="font-size: 3rem;"></i>
                                </div>
                                <h5>Face Login</h5>
                                <p class="text-muted mb-3">Camera access starts only after you open the login camera.</p>
                                <button type="button" class="btn btn-primary" id="openCameraButton">
                                    <i class="bi bi-camera-video me-1"></i>Open Camera
                                </button>
                            </div>

                            <div id="cameraModule" class="d-none">
                                <div class="camera-panel mb-3 mx-auto">
                                    <video id="loginVideo" autoplay muted playsinline></video>
                                </div>
                                <div class="alert alert-info" id="cameraStatus">Camera not started.</div>
                                <div class="alert alert-secondary" id="loginResult">Look into the camera and press login.</div>

                                <div class="d-flex gap-2 login-actions">
                                    <button type="button" class="btn btn-primary" id="loginButton" disabled>
                                        <i class="bi bi-box-arrow-in-right me-1"></i>Login with Face
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" id="restartCamera">
                                        <i class="bi bi-arrow-clockwise me-1"></i>Restart Camera
                                    </button>
                                    <button type="button" class="btn btn-outline-danger" id="closeCameraButton">
                                        <i class="bi bi-camera-video-off me-1"></i>Close Camera
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    @include('partials.kiosk-keyboard')

    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <script>
        const modelUrl = 'https://justadudewhohacks.github.io/face-api.js/models';
        const cameraGate = document.getElementById('cameraGate');
        const cameraModule = document.getElementById('cameraModule');
        const video = document.getElementById('loginVideo');
        const statusBox = document.getElementById('cameraStatus');
        const resultBox = document.getElementById('loginResult');
        const loginButton = document.getElementById('loginButton');
        let modelsLoaded = false;

        function setStatus(message, type = 'info') {
            statusBox.className = `alert alert-${type}`;
            statusBox.textContent = message;
        }

        function setResult(message, type = 'secondary') {
            resultBox.className = `alert alert-${type}`;
            resultBox.textContent = message;
        }

        async function loadModels() {
            if (modelsLoaded) {
                return;
            }

            await Promise.all([
                faceapi.nets.tinyFaceDetector.loadFromUri(modelUrl),
                faceapi.nets.faceLandmark68Net.loadFromUri(modelUrl),
                faceapi.nets.faceRecognitionNet.loadFromUri(modelUrl),
            ]);
            modelsLoaded = true;
        }

        async function startCamera() {
            if (video.srcObject) {
                video.srcObject.getTracks().forEach(track => track.stop());
            }

            const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false });
            video.srcObject = stream;
            loginButton.disabled = false;
            setStatus('Camera ready. Keep your face centered.', 'success');
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

        function stopCamera() {
            if (video.srcObject) {
                video.srcObject.getTracks().forEach(track => track.stop());
                video.srcObject = null;
            }

            loginButton.disabled = true;
        }

        async function openCameraModule() {
            try {
                cameraGate.classList.add('d-none');
                cameraModule.classList.remove('d-none');
                setStatus('Loading face recognition models...');
                await loadModels();
                await startCamera();
            } catch (error) {
                setStatus(error.message || 'Camera or model loading failed.', 'danger');
                loginButton.disabled = true;
            }
        }

        document.getElementById('openCameraButton').addEventListener('click', openCameraModule);
        document.getElementById('restartCamera').addEventListener('click', startCamera);
        document.getElementById('closeCameraButton').addEventListener('click', function() {
            stopCamera();
            cameraModule.classList.add('d-none');
            cameraGate.classList.remove('d-none');
            setResult('Look into the camera and press login.');
        });

        loginButton.addEventListener('click', async function() {
            loginButton.disabled = true;
            setStatus('Capturing face descriptor...');
            setResult('Checking registration...');

            try {
                const descriptor = await getDescriptor();
                const response = await fetch('{{ route('operator.authenticate') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ descriptor }),
                });

                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.message || 'Face login failed.');
                }

                setResult(data.message, 'success');
                window.location.href = data.redirect;
            } catch (error) {
                setResult(error.message, 'danger');
                setStatus('Ready for another attempt.', 'warning');
                loginButton.disabled = false;
            }
        });

        window.addEventListener('beforeunload', stopCamera);
    </script>
</body>
</html>
