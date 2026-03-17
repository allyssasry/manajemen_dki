{{-- Base Styles: Styles yang digunakan di seluruh aplikasi --}}
<style>
    /* Matikan transisi/animasi saat load */
    *, *::before, *::after { 
        transition: none !important; 
        animation: none !important; 
        scroll-behavior: auto !important; 
    }

    html { scrollbar-gutter: stable; }
    body { overflow-x: hidden; }

    /* Scrollbar custom */
    .scroll-thin::-webkit-scrollbar { width: 6px; height: 6px; }
    .scroll-thin::-webkit-scrollbar-thumb { background: #c89898; border-radius: 9999px; }
    .scroll-thin::-webkit-scrollbar-track { background: transparent; }

    /* Kartu maroon theme */
    .card-maroon { background: #7A1C1C; color: #fff; border-color: #7A1C1C; }
    .chip-soft { background: #FFF2F2; color: #7A1C1C; }

    /* Grafik area (fallback style) */
    .chart-grid line { stroke: #f1d6d6; }
    .chart-axis text { fill: #7c7c7c; font-size: 11px; }

    /* Utility no-transition */
    .no-transition, .no-transition * { transition: none !important; }

    /* Ring theme untuk focus */
    .ring-theme { box-shadow: 0 0 0 2px #7A1C1C inset; }

    /* Readonly state */
    .readonly .editable-field { background: #F5EAEA; cursor: not-allowed; }
    .readonly #avatarLabel { pointer-events: none; opacity: .6; }
</style>
