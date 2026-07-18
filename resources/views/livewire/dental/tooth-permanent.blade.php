<div wire:click="selectTooth({{ $tooth }})"
    class="tooth-permanent-container {{ $selected ? 'selected' : '' }} 
        {{ $status['missing'] ?? false ? 'missing' : '' }}
        {{ $status['filled'] ?? false ? 'filled' : '' }}
        {{ $status['crowned'] ?? false ? 'crowned' : '' }}
        {{ $status['abscess'] ?? false ? 'abscess' : '' }}
        {{ $status['implant'] ?? false ? 'implant' : '' }}
        {{ $status['extracted'] ?? false ? 'extracted' : '' }}"
    data-tooth="{{ $tooth }}"
    title="السن رقم {{ $tooth }} - 
        @if($status['missing'] ?? false) مفقودة 
        @elseif($status['filled'] ?? false) محشوة 
        @elseif($status['crowned'] ?? false) تاج 
        @elseif($status['abscess'] ?? false) خراج 
        @elseif($status['implant'] ?? false) زراعة 
        @elseif($status['extracted'] ?? false) مخلوعة 
        @else سليمة 
        @endif">
    
    <div class="tooth-visual">
        <x-tooth-svg :tooth="$tooth" class="tooth-svg" />
        
        @if($status['missing'] ?? false)
            <div class="status-mark missing-mark">✖</div>
        @endif
        
        @if($status['filled'] ?? false)
            <div class="status-mark filling-mark"></div>
        @endif
        
        @if($status['abscess'] ?? false)
            <div class="status-mark abscess-mark"></div>
        @endif
        
        <span class="tooth-number">{{ $tooth }}</span>
    </div>
    
    <div class="status-badges">
        @if($status['crowned'] ?? false)
            <span class="badge crown-badge" title="تاج">
                <i class="fas fa-crown"></i>
            </span>
        @endif
        
        @if($status['implant'] ?? false)
            <span class="badge implant-badge" title="زراعة">
                <i class="fas fa-bolt"></i>
            </span>
        @endif
    </div>
</div>

<style>
.tooth-permanent-container {
    --tooth-size: 70px;
    --tooth-margin: 8px;
    
    position: relative;
    width: var(--tooth-size);
    height: var(--tooth-size);
    margin: var(--tooth-margin);
    cursor: pointer;
    transition: all 0.3s ease;
}

.tooth-visual {
    position: relative;
    width: 100%;
    height: 100%;
}

.tooth-svg {
    width: 100%;
    height: 100%;
}

.tooth-number {
    position: absolute;
    bottom: -18px;
    left: 50%;
    transform: translateX(-50%);
    background: #17a2b8;
    color: white;
    width: 26px;
    height: 26px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 12px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    z-index: 2;
}

/* علامات الحالة */
.status-mark {
    position: absolute;
    z-index: 1;
}

.missing-mark {
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 32px;
    color: #dc3545;
    opacity: 0.7;
}

.filling-mark {
    width: 80%;
    height: 30%;
    top: 20%;
    left: 10%;
    background: rgba(23, 162, 184, 0.4);
    border-radius: 5px;
}

.abscess-mark {
    width: 16px;
    height: 16px;
    background: #dc3545;
    border-radius: 50%;
    top: 10px;
    right: 10px;
    animation: pulse 1.5s infinite;
}

/* شارات الحالة */
.status-badges {
    position: absolute;
    top: -8px;
    right: -8px;
    display: flex;
    flex-direction: column;
    gap: 5px;
    z-index: 3;
}

.badge {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    color: white;
    box-shadow: 0 2px 3px rgba(0,0,0,0.2);
}

.crown-badge {
    background: #ffc107;
}

.implant-badge {
    background: #28a745;
}

/* تأثيرات الحالات */
.tooth-permanent-container.missing .tooth-svg {
    opacity: 0.3;
    filter: grayscale(100%);
}

.tooth-permanent-container.filled .tooth-svg {
    fill: #fbc02d;
}

.tooth-permanent-container.crowned .tooth-svg {
    fill: #90caf9;
}

.tooth-permanent-container.abscess .tooth-svg {
    fill: #ef5350;
}

.tooth-permanent-container.implant .tooth-svg {
    fill: #a1887f;
}

.tooth-permanent-container.extracted .tooth-svg {
    fill: #9e9e9e;
    opacity: 0.5;
}

/* تأثيرات الحركة */
@keyframes pulse {
    0% { transform: scale(1); opacity: 0.8; }
    50% { transform: scale(1.1); opacity: 1; }
    100% { transform: scale(1); opacity: 0.8; }
}

.tooth-permanent-container:hover {
    transform: translateY(-3px) scale(1.05);
}

.tooth-permanent-container.selected {
    transform: scale(1.15);
    z-index: 10;
}

/* التكيف مع الشاشات الصغيرة */
@media (max-width: 768px) {
    .tooth-permanent-container {
        --tooth-size: 60px;
        --tooth-margin: 6px;
    }
    
    .tooth-number {
        width: 22px;
        height: 22px;
        font-size: 11px;
        bottom: -15px;
    }
    
    .badge {
        width: 18px;
        height: 18px;
    }
    
    .missing-mark {
        font-size: 28px;
    }
}
</style>