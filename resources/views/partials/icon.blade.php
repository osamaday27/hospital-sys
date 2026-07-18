{{-- resources/views/partials/icon.blade.php --}}
@php
    /**
     * المتوقّع:
     * $cards = [
     *   ['icon' => 'mdi mdi-eye', 'text' => '...', 'link' => 'route-or-url'],
     *   ['icon' => 'something.json', 'text' => '...', 'link' => 'route-or-url'],
     *   ...
     * ]
     * $title (اختياري)
     */
@endphp

<div class="soft-section mb-3">
    @isset($title)
        <div class="section-title">
            <span class="dot"></span>
            <span>{{ $title }}</span>
        </div>
    @endisset

    <div class="row g-3">
        @foreach ($cards as $card)
            @php
                $icon    = $card['icon'] ?? 'mdi mdi-stethoscope';
                $text    = $card['text'] ?? '';
                $link    = $card['link'] ?? '#';
                $isLottie = is_string($icon) && \Illuminate\Support\Str::endsWith($icon, '.json');
                $id      = 'lottie_'.md5($icon.$text.$link);
            @endphp

            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <a href="{{ url($link) }}" class="icon-card d-block text-decoration-none" title="{{ $text }}">
                    <div class="icon-card__inner soft-card">
                        <div class="icon-card__badge" aria-hidden="true"></div>

                        <div class="icon-wrap">
                            @if ($isLottie)
                                <div class="lottie-box" id="{{ $id }}" aria-hidden="true"></div>
                            @else
                                <i class="{{ $icon }} icon-mdi" aria-hidden="true"></i>
                            @endif
                        </div>

                        <h5 class="icon-title mb-0">{{ $text }}</h5>
                        <span class="ripple"></span>
                    </div>
                </a>

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                var el = document.getElementById(@json($id));
                                function init(){
                                    if (el && window.lottie) {
                                        window.lottie.loadAnimation({
                                            container: el,
                                            renderer: 'svg',
                                            loop: true,
                                            autoplay: true,
                                            path: @json(asset('lotties/'.$icon))
                                        });
                                    } else {
                                        // لو لسه المكتبة متحملتشي جرّب تاني بعد شوية
                                        setTimeout(init, 120);
                                    }
                                }
                                init();
                            });
                        </script>
     
            </div>
        @endforeach
    </div>
</div>

<style>
    /* ===== العناوين ===== */
    .section-title{
        display:flex;align-items:center;gap:.5rem;
        font-weight:800;font-size:1.06rem;color:#0d6efd;margin-bottom:.75rem
    }
    .section-title .dot{width:10px;height:10px;border-radius:50%;background:#0d6efd;opacity:.2}

    /* ===== الكارت الناعم ===== */
    .soft-card{
        border:1px solid rgba(13,110,253,.08);
        border-radius: 20px;
        background: linear-gradient(180deg, rgba(255,255,255,.9), rgba(255,255,255,.78));
        box-shadow:0 6px 20px rgba(16,24,40,.08);
        transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
        position:relative; overflow:hidden; backdrop-filter: blur(6px);
    }
    .soft-card:hover{
        transform: translateY(-3px);
        box-shadow:0 14px 34px rgba(16,24,40,.16);
        border-color: rgba(13,110,253,.18);
    }

    .icon-card__inner{padding:18px 16px; text-align:center}
    .icon-wrap{
        width:92px;height:92px;margin:6px auto 12px;
        border-radius:24px; display:flex;align-items:center;justify-content:center;
        background:
           radial-gradient(120px 60px at 50% 0%, rgba(13,110,253,.08), transparent 60%),
           linear-gradient(135deg, rgba(13,110,253,.10), rgba(25,135,84,.08));
        border: 1px solid rgba(13,110,253,.12);
        box-shadow: inset 0 2px 8px rgba(13,110,253,.08);
    }
    .icon-mdi{
        font-size: 46px; color:#0b4a86; text-shadow:0 1px 0 rgba(255,255,255,.6);
        opacity:.95; transition: transform .25s ease, opacity .25s ease;
    }
    .soft-card:hover .icon-mdi{ transform: scale(1.05); opacity:1 }

    .lottie-box{ width:72px; height:72px }

    .icon-title{ font-weight:800; color:#0b3f71; letter-spacing:.2px }

    /* تأثير Ripple */
    .icon-card{ position:relative }
    .icon-card .ripple{
        position:absolute; inset:0; pointer-events:none; border-radius:inherit;
        background: radial-gradient(140px 140px at var(--rx,50%) var(--ry,50%), rgba(13,110,253,.12), transparent 42%);
        opacity:0; transition:opacity .35s ease;
    }
    .icon-card:hover .ripple{ opacity:1 }

    /* Badge اختياري */
    .icon-card__badge{
        position:absolute; top:10px; inset-inline-end:10px;
        width:10px;height:10px;border-radius:50%;
        background:linear-gradient(180deg, #34c759, #2ca74a);
        box-shadow:0 0 0 3px rgba(52,199,89,.12);
        opacity:.0;
    }

    /* Dark Mode + RTL */
    [data-bs-theme="dark"] .soft-card{
        background: linear-gradient(180deg, rgba(24,28,34,.92), rgba(24,28,34,.84));
        border-color: rgba(255,255,255,.06);
        box-shadow:0 8px 28px rgba(0,0,0,.34);
    }
    [data-bs-theme="dark"] .icon-title{ color:#d1e6ff }
    [data-bs-theme="dark"] .icon-mdi{ color:#cfe4ff }
    [dir="rtl"] .section-title .dot{margin-inline-start:.25rem}
</style>

<script>
document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.icon-card').forEach(function(card){
        card.addEventListener('mousemove', function(e){
            const r = card.querySelector('.ripple'); if(!r) return;
            const rect = card.getBoundingClientRect();
            r.style.setProperty('--rx', (e.clientX - rect.left) + 'px');
            r.style.setProperty('--ry', (e.clientY - rect.top) + 'px');
        });
    });
});
</script>
