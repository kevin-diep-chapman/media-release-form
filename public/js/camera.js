let video = null;
let canvas = null;
let photoPreview = null;
let captureBtn = null;
let retakeBtn = null;
let takePhotoBtn = null;
let stream = null;

function setTakePhotoEnabled(enabled) {
    takePhotoBtn = takePhotoBtn || document.getElementById('takePhotoBtn');
    const photoPanel = document.querySelector('.media-release-photo-panel');

    if (takePhotoBtn) {
        takePhotoBtn.disabled = !enabled;
        takePhotoBtn.setAttribute('aria-disabled', enabled ? 'false' : 'true');
    }

    photoPanel?.classList.toggle('camera-active', !enabled);
}

window.startCamera = async function() {
    video = document.getElementById('video');
    canvas = document.getElementById('canvas');
    photoPreview = document.getElementById('photoPreview');
    captureBtn = document.getElementById('captureBtn');
    retakeBtn = document.getElementById('retakeBtn');
    takePhotoBtn = document.getElementById('takePhotoBtn');

    try {
        stream = await navigator.mediaDevices.getUserMedia({ video: true });
        video.srcObject = stream;
        video.style.display = 'block';
        captureBtn.style.display = 'inline-block';
        setTakePhotoEnabled(false);
        video.play();
    } catch (err) {
        alert('Camera not accessible: ' + err.message);
    }
};

window.capturePhoto = function() {
    canvas = document.getElementById('canvas');
    video = document.getElementById('video');
    photoPreview = document.getElementById('photoPreview');
    captureBtn = document.getElementById('captureBtn');
    retakeBtn = document.getElementById('retakeBtn');

    canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
    video.style.display = 'none';
    canvas.style.display = 'none';

    // Get image data and show preview
    const dataUrl = canvas.toDataURL('image/jpeg', 0.92);
    photoPreview.src = dataUrl;
    photoPreview.style.display = 'block';
    captureBtn.style.display = 'none';
    retakeBtn.style.display = 'inline-block';

    // Set to hidden input (so it can be handled in backend as base64 or via AJAX)
    document.getElementById('photo_path').value = dataUrl;
};

window.retakePhoto = function() {
    video = document.getElementById('video');
    photoPreview = document.getElementById('photoPreview');
    captureBtn = document.getElementById('captureBtn');
    retakeBtn = document.getElementById('retakeBtn');
    document.getElementById('photo_path').value = '';
    photoPreview.style.display = 'none';
    captureBtn.style.display = 'inline-block';
    retakeBtn.style.display = 'none';
    video.style.display = 'block';
};

window.stopCamera = function() {
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
        stream = null;
    }
    if (video) video.style.display = 'none';
    setTakePhotoEnabled(true);
};
