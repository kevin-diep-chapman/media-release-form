(function () {
    let canvas = null;
    let ctx = null;
    let drawing = false;
    let hasSignature = false;
    let initialized = false;

    function getPoint(event) {
        const rect = canvas.getBoundingClientRect();
        const clientX = event.touches ? event.touches[0].clientX : event.clientX;
        const clientY = event.touches ? event.touches[0].clientY : event.clientY;

        return {
            x: ((clientX - rect.left) / rect.width) * canvas.width,
            y: ((clientY - rect.top) / rect.height) * canvas.height,
        };
    }

    function startDrawing(event) {
        event.preventDefault();
        drawing = true;
        const point = getPoint(event);
        ctx.beginPath();
        ctx.moveTo(point.x, point.y);
    }

    function draw(event) {
        if (!drawing) {
            return;
        }

        event.preventDefault();
        const point = getPoint(event);
        ctx.lineTo(point.x, point.y);
        ctx.stroke();
        hasSignature = true;
    }

    function stopDrawing() {
        drawing = false;
        syncSignatureInput();
    }

    function syncSignatureInput() {
        const input = document.getElementById('signature_path');
        if (!input || !canvas) {
            return;
        }

        input.value = hasSignature ? canvas.toDataURL('image/png') : '';
    }

    function resizeCanvas() {
        if (!canvas || !canvas.parentElement) {
            return;
        }

        const ratio = window.devicePixelRatio || 1;
        const width = canvas.parentElement.clientWidth;
        const height = 160;

        canvas.width = width * ratio;
        canvas.height = height * ratio;
        canvas.style.width = `${width}px`;
        canvas.style.height = `${height}px`;

        ctx.setTransform(1, 0, 0, 1, 0, 0);
        ctx.scale(ratio, ratio);
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
        ctx.strokeStyle = '#231f20';
    }

    window.initSignaturePad = function () {
        canvas = document.getElementById('signaturePad');
        if (!canvas) {
            return;
        }

        ctx = canvas.getContext('2d');

        if (!initialized) {
            canvas.addEventListener('mousedown', startDrawing);
            canvas.addEventListener('mousemove', draw);
            canvas.addEventListener('mouseup', stopDrawing);
            canvas.addEventListener('mouseleave', stopDrawing);
            canvas.addEventListener('touchstart', startDrawing, { passive: false });
            canvas.addEventListener('touchmove', draw, { passive: false });
            canvas.addEventListener('touchend', stopDrawing);

            document.getElementById('clearSignatureBtn')?.addEventListener('click', () => {
                window.clearSignaturePad();
            });

            window.addEventListener('resize', resizeCanvas);
            initialized = true;
        }

        resizeCanvas();
        window.clearSignaturePad();
    };

    window.clearSignaturePad = function () {
        if (!canvas || !ctx) {
            return;
        }

        ctx.clearRect(0, 0, canvas.width, canvas.height);
        hasSignature = false;
        syncSignatureInput();
    };

    window.hasSignaturePadValue = function () {
        return hasSignature;
    };

    document.addEventListener('DOMContentLoaded', () => {
        initSignaturePad();
    });
})();
