@extends('admin.layouts.admin')

@section('title', 'Dashboard Analytics')

@section('content')
@php
    // Stored in page_settings (analytics.*); an empty or invalid height means auto.
    $iframeUrl = $settings->get('analytics.looker_url');
    $iframeHeight = max(0, (int) $settings->get('analytics.looker_height'));
@endphp

<div class="container-fluid admin-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold text-custom-yellow">Dashboard Analytics</h2>
            <p class="mb-0 text-custom-yellow analytics-subtitle">Monitoring performa website secara real-time.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-custom-yellow-outline btn-sm fw-bold" onclick="openEditIframeModal()">
                <i class="fa-solid fa-pen-to-square me-1"></i> Edit Iframe
            </button>
            <button class="btn btn-custom-yellow btn-sm fw-bold" onclick="location.reload();">
                <i class="fa-solid fa-arrows-rotate me-1"></i> Refresh Data
            </button>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 analytics-card">
        <div class="card-body p-0">
            <div class="looker-wrapper" id="looker-wrapper">
                <iframe
                    id="looker-iframe"
                    src="{{ $iframeUrl }}"
                    frameborder="0"
                    allowfullscreen
                    sandbox="allow-storage-access-by-user-activation allow-scripts allow-same-origin allow-popups allow-popups-to-escape-sandbox">
                </iframe>
            </div>
        </div>
    </div>
</div>

<!-- Edit Iframe Modal -->
<div class="modal fade analytics-modal" id="editIframeModal" tabindex="-1" aria-labelledby="editIframeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold" id="editIframeModalLabel">
                    <i class="fa-solid fa-code me-2"></i>Edit Looker Studio Iframe
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="iframeUrl" class="form-label">Looker Studio Embed URL</label>
                    <input type="url" class="form-control" id="iframeUrl"
                           value="{{ $iframeUrl }}"
                           placeholder="https://lookerstudio.google.com/embed/reporting/...">
                    <div class="form-text">
                        <i class="fa-solid fa-info-circle me-1"></i>
                        Paste URL embed dari Google Looker Studio. Pastikan URL mengandung "/embed/" untuk tampilan embedded.
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="iframeHeight" class="form-label">
                        <i class="fa-solid fa-arrows-up-down me-1"></i> Tinggi Iframe (opsional)
                    </label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="iframeHeight"
                               placeholder="Auto"
                               min="400" max="5000" step="50">
                        <span class="input-group-text">px</span>
                        <button type="button" class="btn btn-outline-secondary" onclick="resetIframeHeight()" title="Reset ke Auto">
                            <i class="fa-solid fa-rotate-left"></i>
                        </button>
                    </div>
                    <div class="form-text">
                        Kosongkan untuk auto-height berdasarkan viewport. Set manual jika report terlalu panjang.
                    </div>
                </div>
                
                <div class="alert analytics-tips">
                    <strong><i class="fa-solid fa-lightbulb me-1"></i> Tips:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Buka report di Looker Studio</li>
                        <li>Klik File → Embed report</li>
                        <li>Copy URL dari kolom "Embed URL"</li>
                        <li>Jika report terpotong, set tinggi manual (contoh: 1500px untuk report panjang)</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn fw-bold analytics-save-btn" id="saveIframeBtn" onclick="saveIframeUrl()">
                    <i class="fa-solid fa-save me-1"></i> Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let editIframeModal;

    document.addEventListener('DOMContentLoaded', function() {
        editIframeModal = new bootstrap.Modal(document.getElementById('editIframeModal'));
        
        // Initialize iframe height
        adjustIframeHeight();
        
        // Adjust on window resize
        window.addEventListener('resize', debounce(adjustIframeHeight, 150));
    });

    // Debounce function to limit resize calls
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Custom height from database (empty = auto)
    let customIframeHeight = @json($iframeHeight ? (string) $iframeHeight : '');
    
    // Calculate and set optimal iframe height
    function adjustIframeHeight(forceHeight = null) {
        const iframe = document.getElementById('looker-iframe');
        const wrapper = document.getElementById('looker-wrapper');
        
        if (!iframe || !wrapper) return;
        
        let optimalHeight;
        
        // Use forced height, custom height from DB, or auto-calculate
        if (forceHeight && forceHeight > 0) {
            optimalHeight = parseInt(forceHeight);
        } else if (customIframeHeight && parseInt(customIframeHeight) > 0) {
            optimalHeight = parseInt(customIframeHeight);
        } else {
            // Auto-calculate based on viewport
            const wrapperRect = wrapper.getBoundingClientRect();
            const wrapperTop = wrapperRect.top;
            const viewportHeight = window.innerHeight;
            const padding = 30;
            const availableHeight = viewportHeight - wrapperTop - padding;
            
            const minHeight = 800;
            const maxHeight = 2500;
            
            optimalHeight = Math.max(availableHeight, minHeight);
            optimalHeight = Math.min(optimalHeight, maxHeight);
        }
        
        // Apply height
        iframe.style.height = optimalHeight + 'px';
        wrapper.style.height = optimalHeight + 'px';
        
        console.log('Iframe height adjusted to:', optimalHeight + 'px', customIframeHeight ? '(custom)' : '(auto)');
    }
    
    // Reset height to auto
    function resetIframeHeight() {
        document.getElementById('iframeHeight').value = '';
        customIframeHeight = '';
        adjustIframeHeight();
        showToast('Tinggi iframe di-reset ke auto', 'success');
    }

    function openEditIframeModal() {
        // Populate height field with current value
        document.getElementById('iframeHeight').value = customIframeHeight || '';
        editIframeModal.show();
    }

    function saveIframeUrl() {
        const url = document.getElementById('iframeUrl').value.trim();
        const height = document.getElementById('iframeHeight').value.trim();
        const saveBtn = document.getElementById('saveIframeBtn');

        if (!url) {
            alert('Please enter a valid URL');
            return;
        }

        if (!url.includes('lookerstudio.google.com')) {
            if (!confirm('This URL does not appear to be from Google Looker Studio. Continue anyway?')) {
                return;
            }
        }

        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Saving...';

        // Both values are page settings (PATCH /admin/api/settings/{key}).
        const saveSetting = (key, value) => {
            const body = new FormData();
            body.append('_method', 'PATCH');
            body.append('value', value);

            return fetch(@json(route('admin.api.settings.update', ['key' => '__key__'])).replace('__key__', key), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body
            }).then(r => r.json().then(json => {
                if (!r.ok) {
                    throw new Error(json.message || 'HTTP ' + r.status);
                }
                return json;
            }));
        };

        Promise.all([
            saveSetting('analytics.looker_url', url),
            saveSetting('analytics.looker_height', height || '')
        ])
            .then(() => {
                document.getElementById('looker-iframe').src = url;
                customIframeHeight = height;
                adjustIframeHeight(height);
                editIframeModal.hide();
                showToast('Settings updated successfully!', 'success');
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Failed to save: ' + error.message, 'error');
            })
            .finally(() => {
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<i class="fa-solid fa-save me-1"></i> Save Changes';
            });
    }

    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `analytics-toast analytics-toast-${type === 'success' ? 'success' : 'error'}`;
        toast.innerHTML = `<i class="fa-solid ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i><span></span>`;
        toast.querySelector('span').textContent = message;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.classList.add('analytics-toast-hidden');
            setTimeout(() => toast.remove(), 500);
        }, 3000);
    }
</script>
@endsection
