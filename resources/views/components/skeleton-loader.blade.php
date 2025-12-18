{{-- Skeleton Loader Component --}}
@props(['type' => 'card', 'count' => 1])

<style>
    .skeleton {
        background: linear-gradient(90deg, 
            rgba(255, 255, 255, 0.03) 25%, 
            rgba(255, 255, 255, 0.08) 50%, 
            rgba(255, 255, 255, 0.03) 75%
        );
        background-size: 200% 100%;
        animation: skeletonShimmer 1.5s infinite;
        border-radius: 8px;
    }
    
    @keyframes skeletonShimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
    
    .skeleton-card {
        background: linear-gradient(135deg, rgba(20, 20, 40, 0.9), rgba(15, 15, 30, 0.95));
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        padding: 28px;
        margin-bottom: 20px;
    }
    
    .skeleton-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }
    
    .skeleton-title {
        height: 24px;
        width: 200px;
    }
    
    .skeleton-btn {
        height: 40px;
        width: 120px;
        border-radius: 10px;
    }
    
    .skeleton-table {
        width: 100%;
    }
    
    .skeleton-row {
        display: flex;
        gap: 16px;
        padding: 16px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    .skeleton-cell {
        height: 20px;
        flex: 1;
    }
    
    .skeleton-cell-sm {
        width: 80px;
        flex: none;
    }
    
    .skeleton-avatar {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        flex: none;
    }
    
    .skeleton-text {
        height: 16px;
        margin-bottom: 8px;
    }
    
    .skeleton-text-sm {
        height: 12px;
        width: 60%;
    }
    
    .skeleton-form-group {
        margin-bottom: 24px;
    }
    
    .skeleton-label {
        height: 14px;
        width: 100px;
        margin-bottom: 10px;
    }
    
    .skeleton-input {
        height: 48px;
        width: 100%;
        border-radius: 10px;
    }
    
    .skeleton-embed {
        height: calc(100vh - 200px);
        border-radius: 16px;
    }
</style>

@for($i = 0; $i < $count; $i++)
    @if($type === 'card')
    <div class="skeleton-card">
        <div class="skeleton-header">
            <div class="skeleton skeleton-title"></div>
            <div class="skeleton skeleton-btn"></div>
        </div>
        <div class="skeleton-table">
            @for($j = 0; $j < 5; $j++)
            <div class="skeleton-row">
                <div class="skeleton skeleton-avatar"></div>
                <div class="skeleton skeleton-cell"></div>
                <div class="skeleton skeleton-cell"></div>
                <div class="skeleton skeleton-cell-sm"></div>
            </div>
            @endfor
        </div>
    </div>
    @elseif($type === 'form')
    <div class="skeleton-card">
        <div class="skeleton skeleton-title" style="margin-bottom: 32px;"></div>
        @for($j = 0; $j < 4; $j++)
        <div class="skeleton-form-group">
            <div class="skeleton skeleton-label"></div>
            <div class="skeleton skeleton-input"></div>
        </div>
        @endfor
        <div style="display: flex; gap: 14px; margin-top: 32px;">
            <div class="skeleton skeleton-btn"></div>
            <div class="skeleton skeleton-btn" style="width: 100px;"></div>
        </div>
    </div>
    @elseif($type === 'embed')
    <div class="skeleton skeleton-embed"></div>
    @elseif($type === 'text')
    <div class="skeleton skeleton-text" style="width: {{ rand(60, 100) }}%;"></div>
    <div class="skeleton skeleton-text" style="width: {{ rand(40, 80) }}%;"></div>
    <div class="skeleton skeleton-text-sm"></div>
    @endif
@endfor
