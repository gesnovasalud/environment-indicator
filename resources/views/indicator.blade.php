@if ($data)
    <div class="environment-indicator-bar" style="position:absolute;top:0;left:0;right:0;height:4px;background-color:{{ $data['color'] }};z-index:9999;"></div>

    <div class="environment-indicator-badge" title="{{ $data['title'] }}"
        style="position:absolute;top:0;right:0;padding:4px 10px 4px 0;background-color:{{ $data['color'] }};color:#fff;font-size:.7rem;font-weight:600;z-index:9998;box-shadow:0 2px 8px rgba(0,0,0,.15);display:flex;align-items:center;gap:3px;">
        <span>{{ $data['primary'] }}</span>
        @if ($data['secondary'])
            <span style="opacity:.85;">{{ $data['secondary'] }}</span>
        @endif
    </div>

    <style>
        .environment-indicator-badge::before {
            content: '';
            position: absolute;
            top: 0;
            left: -20px;
            width: 20px;
            height: 100%;
            background-color: {{ $data['color'] }};
            border-bottom-left-radius: 20px;
        }

        @media (max-width: 640px) {
            .environment-indicator-badge { display: none; }
        }
    </style>
@endif
