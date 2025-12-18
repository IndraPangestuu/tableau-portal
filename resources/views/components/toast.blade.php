{{-- Toast Notification Component --}}
<div id="toastContainer" class="toast-container"></div>

<style>
    .toast-container {
        position: fixed;
        top: 24px;
        right: 24px;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        gap: 12px;
        pointer-events: none;
    }
    
    .toast {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px 20px;
        border-radius: 14px;
        backdrop-filter: blur(20px);
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(255, 255, 255, 0.05);
        min-width: 320px;
        max-width: 420px;
        pointer-events: auto;
        animation: toastSlideIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    
    .toast::before {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        height: 3px;
        background: currentColor;
        opacity: 0.5;
        animation: toastProgress 5s linear forwards;
    }
    
    .toast.toast-success {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(16, 185, 129, 0.08));
        border: 1px solid rgba(16, 185, 129, 0.3);
        color: #34d399;
    }
    
    .toast.toast-error {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.15), rgba(239, 68, 68, 0.08));
        border: 1px solid rgba(239, 68, 68, 0.3);
        color: #f87171;
    }
    
    .toast.toast-warning {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.15), rgba(245, 158, 11, 0.08));
        border: 1px solid rgba(245, 158, 11, 0.3);
        color: #fbbf24;
    }
    
    .toast.toast-info {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.15), rgba(99, 102, 241, 0.08));
        border: 1px solid rgba(99, 102, 241, 0.3);
        color: #818cf8;
    }
    
    .toast-icon {
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }
    
    .toast-content {
        flex: 1;
    }
    
    .toast-title {
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 2px;
    }
    
    .toast-message {
        font-size: 13px;
        opacity: 0.9;
    }
    
    .toast-close {
        background: none;
        border: none;
        color: inherit;
        cursor: pointer;
        opacity: 0.6;
        transition: opacity 0.2s, transform 0.2s;
        padding: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .toast-close:hover {
        opacity: 1;
        transform: scale(1.1);
    }
    
    .toast.toast-hiding {
        animation: toastSlideOut 0.3s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }
    
    @keyframes toastSlideIn {
        from {
            opacity: 0;
            transform: translateX(100px) scale(0.9);
        }
        to {
            opacity: 1;
            transform: translateX(0) scale(1);
        }
    }
    
    @keyframes toastSlideOut {
        from {
            opacity: 1;
            transform: translateX(0) scale(1);
        }
        to {
            opacity: 0;
            transform: translateX(100px) scale(0.9);
        }
    }
    
    @keyframes toastProgress {
        from { width: 100%; }
        to { width: 0%; }
    }
    
    @media (max-width: 768px) {
        .toast-container {
            top: auto;
            bottom: 24px;
            left: 16px;
            right: 16px;
        }
        
        .toast {
            min-width: auto;
            max-width: none;
        }
    }
</style>

<script>
    const Toast = {
        container: null,
        
        init() {
            this.container = document.getElementById('toastContainer');
        },
        
        show(type, message, title = null, duration = 5000) {
            if (!this.container) this.init();
            
            const icons = {
                success: 'fa-check-circle',
                error: 'fa-exclamation-circle',
                warning: 'fa-exclamation-triangle',
                info: 'fa-info-circle'
            };
            
            const titles = {
                success: 'Berhasil',
                error: 'Error',
                warning: 'Peringatan',
                info: 'Info'
            };
            
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            toast.innerHTML = `
                <div class="toast-icon"><i class="fas ${icons[type]}"></i></div>
                <div class="toast-content">
                    <div class="toast-title">${title || titles[type]}</div>
                    <div class="toast-message">${message}</div>
                </div>
                <button class="toast-close" onclick="Toast.hide(this.parentElement)">
                    <i class="fas fa-times"></i>
                </button>
            `;
            
            this.container.appendChild(toast);
            
            // Auto hide
            if (duration > 0) {
                setTimeout(() => this.hide(toast), duration);
            }
            
            return toast;
        },
        
        hide(toast) {
            if (!toast || toast.classList.contains('toast-hiding')) return;
            toast.classList.add('toast-hiding');
            setTimeout(() => toast.remove(), 300);
        },
        
        success(message, title = null) {
            return this.show('success', message, title);
        },
        
        error(message, title = null) {
            return this.show('error', message, title);
        },
        
        warning(message, title = null) {
            return this.show('warning', message, title);
        },
        
        info(message, title = null) {
            return this.show('info', message, title);
        }
    };
    
    document.addEventListener('DOMContentLoaded', () => Toast.init());
</script>

{{-- Show session flash messages as toast --}}
@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', () => {
        Toast.success("{{ session('success') }}");
    });
</script>
@endif

@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', () => {
        Toast.error("{{ session('error') }}");
    });
</script>
@endif

@if(session('warning'))
<script>
    document.addEventListener('DOMContentLoaded', () => {
        Toast.warning("{{ session('warning') }}");
    });
</script>
@endif

@if(session('info'))
<script>
    document.addEventListener('DOMContentLoaded', () => {
        Toast.info("{{ session('info') }}");
    });
</script>
@endif
