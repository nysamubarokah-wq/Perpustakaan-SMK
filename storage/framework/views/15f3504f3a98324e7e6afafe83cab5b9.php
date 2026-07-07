<div id="cropModal" class="modal fade" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="cropModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius:16px;overflow:visible;">
            <div class="modal-header" style="background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;padding:16px 24px;border-radius:16px 16px 0 0;">
                <h5 class="modal-title" id="cropModalLabel" style="font-weight:700;margin:0;">
                    <i class="bi bi-crop"></i> Atur Foto Profil
                </h5>
                <button type="button" class="btn-close btn-close-white" onclick="closeCropModal()" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="cropModalBody" style="padding:20px;background:#f8f9fa;">
                <div id="cropContainerWrapper" style="display:grid;grid-template-columns:1fr 160px;gap:20px;align-items:start;">
                    <div style="background:white;border-radius:12px;padding:15px;box-shadow:0 2px 10px rgba(0,0,0,0.05);">
                        <div id="cropContainer" style="position:relative;width:100%;height:350px;background:#f0f0f0;border-radius:8px;overflow:hidden;pointer-events:auto;">
                            <img id="cropImage" style="display:block;max-width:100%;max-height:100%;width:auto;height:auto;object-fit:contain;" />
                        </div>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:15px;">
                        <div style="background:white;border-radius:12px;padding:15px;box-shadow:0 2px 10px rgba(0,0,0,0.05);text-align:center;">
                            <p style="font-size:11px;color:#888;margin:0 0 8px;text-transform:uppercase;letter-spacing:0.5px;">Preview</p>
                            <div style="width:120px;height:120px;margin:0 auto;border-radius:50%;overflow:hidden;border:4px solid #1a6e35;box-shadow:0 4px 15px rgba(0,0,0,0.15);background:#eee;">
                                <img id="cropPreview" style="width:100%;height:100%;object-fit:cover;" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" />
                            </div>
                        </div>
                        <div style="background:white;border-radius:12px;padding:15px;box-shadow:0 2px 10px rgba(0,0,0,0.05);">
                            <p style="font-size:11px;color:#888;margin:0 0 10px;text-transform:uppercase;letter-spacing:0.5px;">Zoom</p>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <i class="bi bi-dash-circle" style="color:#888;font-size:18px;"></i>
                                <input type="range" id="zoomSlider" min="0.3" max="2" step="0.05" value="1" style="flex:1;accent-color:#1a6e35;cursor:pointer;">
                                <i class="bi bi-plus-circle" style="color:#1a6e35;font-size:20px;"></i>
                            </div>
                        </div>
                        <div style="background:#fff3cd;border-radius:12px;padding:12px;font-size:11px;color:#856404;line-height:1.5;">
                            <i class="bi bi-info-circle"></i> Drag gambar untuk memindahkan. Gunakan slider untuk zoom.
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="padding:16px 24px;background:white;border-top:1px solid #eee;display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" class="btn" id="btnBatal" onclick="closeCropModal()" style="background:#f1f1f1;color:#333;border-radius:10px;padding:10px 20px;font-weight:600;font-size:13px;border:none;cursor:pointer;">
                    <i class="bi bi-x-lg"></i> Batal
                </button>
                <button type="button" class="btn" id="btnReset" onclick="resetCrop()" style="background:#e8f5e9;color:#1a6e35;border-radius:10px;padding:10px 20px;font-weight:600;font-size:13px;border:2px solid #1a6e35;cursor:pointer;">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </button>
                <button type="button" class="btn" id="saveCropBtn" onclick="saveCrop()" style="background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border-radius:10px;padding:10px 24px;font-weight:600;font-size:13px;border:none;box-shadow:0 4px 12px rgba(26,110,53,0.3);cursor:pointer;">
                    <i class="bi bi-check-lg"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<div id="cropLoadingOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:10000;justify-content:center;align-items:center;flex-direction:column;gap:15px;">
    <div style="width:50px;height:50px;border:4px solid rgba(255,255,255,0.3);border-top-color:white;border-radius:50%;animation:spin 0.8s linear infinite;"></div>
    <span style="color:white;font-weight:600;font-size:14px;">Mengupload foto...</span>
</div>

<style>
@keyframes spin { to { transform: rotate(360deg); } }
#cropModal .modal-dialog { max-width: 720px; }
#cropModal .modal-content { overflow: visible; }
#cropModal { z-index: 99999 !important; }
#cropModal .modal { z-index: 99999 !important; }
#cropModal.show { overflow: visible !important; }
#cropContainer { pointer-events: auto !important; }
#cropModal .cropper-container,
#cropModal .cropper-canvas,
#cropModal .cropper-crop-box,
#cropModal .cropper-view-box { pointer-events: auto !important; }
#cropModal .cropper-line,
#cropModal .cropper-point,
#cropModal .cropper-face { pointer-events: auto !important; }
#cropModalBody { pointer-events: auto !important; }
#cropModal .modal-content { pointer-events: auto !important; }
#cropModal .modal-dialog { pointer-events: auto !important; }
#cropModalBody > div { pointer-events: auto !important; }
#cropModal .modal-footer { pointer-events: auto !important; }
#cropModal .modal-footer .btn { pointer-events: auto !important; }
#cropModal .modal-header .btn-close { pointer-events: auto !important; }
@media (max-width: 600px) {
    #cropContainerWrapper { grid-template-columns: 1fr !important; }
    #cropContainer { height: 280px !important; }
    #cropContainerWrapper > div:last-child { display: flex; flex-wrap: wrap; gap: 10px; }
}
</style>

<script>
var cropperInstance = null;

function openCropModal(imageSrc) {
    var img = document.getElementById('cropImage');
    img.style.display = 'block';
    img.onload = function() {
        if (cropperInstance) {
            cropperInstance.destroy();
        }
        cropperInstance = new Cropper(img, {
            aspectRatio: 1,
            viewMode: 1,
            dragMode: 'move',
            cropBoxMovable: true,
            cropBoxResizable: true,
            minCropBoxWidth: 80,
            minCropBoxHeight: 80,
            autoCropArea: 0.9,
            guides: true,
            center: true,
            ready: function() { updatePreview(); }
        });
    };
    img.src = imageSrc;
    var modalEl = document.getElementById('cropModal');
    var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();
}

function closeCropModal() {
    if (cropperInstance) {
        cropperInstance.destroy();
        cropperInstance = null;
    }
    var modal = bootstrap.Modal.getInstance(document.getElementById('cropModal'));
    if (modal) modal.hide();
    var uploadInput = document.getElementById('uploadFoto');
    if (uploadInput) uploadInput.value = '';
}

function resetCrop() {
    if (cropperInstance) {
        cropperInstance.reset();
        updatePreview();
    }
}

function updateZoom(value) {
    if (cropperInstance) {
        cropperInstance.zoomTo(parseFloat(value));
    }
}

function updatePreview() {
    if (cropperInstance) {
        var canvas = cropperInstance.getCroppedCanvas({ width: 300, height: 300 });
        if (canvas) {
            document.getElementById('cropPreview').src = canvas.toDataURL('image/jpeg', 0.9);
        }
    }
}

function saveCrop() {
    if (!cropperInstance) return;
    var btn = document.getElementById('saveCropBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

    var canvas = cropperInstance.getCroppedCanvas({ width: 400, height: 400, maxWidth: 400, maxHeight: 400 });
    var dataUrl = canvas.toDataURL('image/jpeg', 0.85);

    var overlay = document.getElementById('cropLoadingOverlay');
    overlay.style.display = 'flex';

    var csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '';

    fetch('/profil/foto', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'foto=' + encodeURIComponent(dataUrl)
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        overlay.style.display = 'none';
        if (data.success) {
            var modal = bootstrap.Modal.getInstance(document.getElementById('cropModal'));
            if (modal) modal.hide();
            document.querySelectorAll('.profil-avatar').forEach(function(el) { el.src = data.foto_url; });
            alert('Foto profil berhasil diupdate!');
        } else {
            alert(data.message || 'Gagal mengupload foto!');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-lg"></i> Simpan';
        }
    })
    .catch(function(error) {
        overlay.style.display = 'none';
        alert('Terjadi kesalahan saat mengupload foto!');
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-lg"></i> Simpan';
    });
}

document.getElementById('zoomSlider').addEventListener('input', function(e) {
    updateZoom(e.target.value);
});

document.getElementById('cropModal').addEventListener('hidden.bs.modal', function() {
    if (cropperInstance) {
        cropperInstance.destroy();
        cropperInstance = null;
    }
    var uploadInput = document.getElementById('uploadFoto');
    if (uploadInput) uploadInput.value = '';
});
</script>
<?php /**PATH C:\laragon\www\PerpustakaanDigital\resources\views/components/crop-modal.blade.php ENDPATH**/ ?>