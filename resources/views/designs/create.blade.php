@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">🎨 Dizaina veidotājs — {{ $product->name }}</h2>

    <div class="row g-4">

        {{-- KREISĀ PUSE — priekšskatījums --}}
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header fw-bold">Priekšskatījums</div>
                <div class="card-body text-center">
                    <div id="preview-wrapper" style="
                        position: relative;
                        display: inline-block;
                        width: 300px;
                        height: 350px;
                        background-color: #ffffff;
                        border: 2px dashed #ccc;
                        border-radius: 8px;
                        overflow: hidden;
                    ">
                        {{-- Apģērba pamatattēls --}}
                        <img src="{{ asset('storage/' . $product->image) }}"
                             alt="{{ $product->name }}"
                             style="width:100%; height:100%; object-fit:cover; position:absolute; top:0; left:0;"
                             id="product-base-img">

                        {{-- Krāsas pārklājums --}}
                        <div id="color-overlay" style="
                            position: absolute; top:0; left:0;
                            width:100%; height:100%;
                            opacity: 0.3;
                            pointer-events: none;
                            background-color: #ffffff;
                            mix-blend-mode: multiply;
                        "></div>

                        {{-- Dizaina uzlikums --}}
                        <img id="design-overlay"
                             src=""
                             alt="dizains"
                             style="
                                position: absolute;
                                display: none;
                                width: 80px;
                                height: 80px;
                                object-fit: contain;
                                top: 50%; left: 50%;
                                transform: translate(-50%, -50%);
                                pointer-events: none;
                             ">
                    </div>
                </div>
            </div>
        </div>

        {{-- LABĀ PUSE — iestatījumi --}}
        <div class="col-md-6">
            <form action="{{ route('design.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                {{-- 1. Krāsas izvēle --}}
                <div class="card shadow-sm mb-3">
                    <div class="card-header fw-bold">1. Izvēlies krāsu</div>
                    <div class="card-body d-flex flex-wrap gap-2">
                        @foreach($colors as $hex => $name)
                            <label class="color-swatch" title="{{ $name }}" style="cursor:pointer;">
                                <input type="radio" name="base_color"
                                       value="{{ $hex }}"
                                       class="d-none color-radio"
                                       {{ $hex === '#ffffff' ? 'checked' : '' }}>
                                <div class="color-circle"
                                     style="
                                        width:36px; height:36px;
                                        border-radius:50%;
                                        background:{{ $hex }};
                                        border: 3px solid {{ $hex === '#ffffff' ? '#0d6efd' : '#dee2e6' }};
                                     "
                                     data-hex="{{ $hex }}">
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- 2. Gatavā bibliotēka --}}
                <div class="card shadow-sm mb-3">
                    <div class="card-header fw-bold">2. Izvēlies gatavu drukas motīvu</div>
                    <div class="card-body d-flex flex-wrap gap-3">
                        @foreach($presets as $preset)
                            <label class="text-center preset-label" style="cursor:pointer; width:70px;">
                                <input type="radio" name="preset_design"
                                       value="{{ $preset['file'] }}"
                                       class="d-none preset-radio">
                                <img src="{{ asset('designs/presets/' . $preset['file']) }}"
                                     alt="{{ $preset['name'] }}"
                                     class="preset-img border rounded p-1"
                                     style="width:60px; height:60px; object-fit:contain;"
                                     data-file="{{ $preset['file'] }}">
                                <div class="small mt-1">{{ $preset['name'] }}</div>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- 3. Augšupielādēt pašam --}}
                <div class="card shadow-sm mb-3">
                    <div class="card-header fw-bold">3. Vai augšupielādē savu attēlu</div>
                    <div class="card-body">
                        <input type="file" name="design_image"
                               id="design_image_upload"
                               accept="image/*"
                               class="form-control">
                        <div class="form-text">PNG, JPG — maks. 2MB</div>
                    </div>
                </div>

                {{-- 4. Pozīcija un izmērs --}}
                <div class="card shadow-sm mb-3">
                    <div class="card-header fw-bold">4. Pozīcija un izmērs</div>
                    <div class="card-body row g-2">
                        <div class="col-6">
                            <label class="form-label">Pozīcija</label>
                            <select name="design_position" id="design_position" class="form-select">
                                <option value="left">Kreisā puse</option>
                                <option value="center" selected>Centrs</option>
                                <option value="right">Labā puse</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Izmērs</label>
                            <select name="design_size" id="design_size" class="form-select">
                                <option value="small">Mazs (50px)</option>
                                <option value="medium" selected>Vidējs (80px)</option>
                                <option value="large">Liels (120px)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2">
                    🛒 Pievienot grozam ar šo dizainu
                </button>
            </form>
        </div>
    </div>
</div>

{{-- JavaScript priekšskatījumam --}}
<script>
// Krāsas izvēle
document.querySelectorAll('.color-radio').forEach(radio => {
    radio.addEventListener('change', function() {
        const hex = this.value;
        document.getElementById('color-overlay').style.backgroundColor = hex;
        document.querySelectorAll('.color-circle').forEach(c => {
            c.style.border = '3px solid #dee2e6';
        });
        this.parentElement.querySelector('.color-circle').style.border = '3px solid #0d6efd';
    });
});

// Gatavā bibliotēka izvēle
document.querySelectorAll('.preset-radio').forEach(radio => {
    radio.addEventListener('change', function() {
        const file = this.value;
        const overlay = document.getElementById('design-overlay');
        overlay.src = '/designs/presets/' + file;
        overlay.style.display = 'block';
        updatePosition();
        document.querySelectorAll('.preset-img').forEach(img => {
            img.classList.remove('border-primary');
            img.style.border = '1px solid #dee2e6';
        });
        this.parentElement.querySelector('.preset-img').style.border = '2px solid #0d6efd';
    });
});

// Augšupielāde
document.getElementById('design_image_upload').addEventListener('change', function() {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(e) {
        const overlay = document.getElementById('design-overlay');
        overlay.src = e.target.result;
        overlay.style.display = 'block';
        // Noņem preset izvēli
        document.querySelectorAll('.preset-radio').forEach(r => r.checked = false);
        document.querySelectorAll('.preset-img').forEach(img => {
            img.style.border = '1px solid #dee2e6';
        });
        updatePosition();
    };
    reader.readAsDataURL(file);
});

// Pozīcija
document.getElementById('design_position').addEventListener('change', updatePosition);
document.getElementById('design_size').addEventListener('change', updatePosition);

function updatePosition() {
    const overlay = document.getElementById('design-overlay');
    const pos = document.getElementById('design_position').value;
    const size = document.getElementById('design_size').value;

    const sizes = { small: '50px', medium: '80px', large: '120px' };
    overlay.style.width  = sizes[size];
    overlay.style.height = sizes[size];

    overlay.style.top  = '50%';
    overlay.style.transform = 'translateY(-50%)';

    if (pos === 'left') {
        overlay.style.left      = '20px';
        overlay.style.transform = 'translateY(-50%)';
    } else if (pos === 'center') {
        overlay.style.left      = '50%';
        overlay.style.transform = 'translate(-50%, -50%)';
    } else {
        overlay.style.left      = 'auto';
        overlay.style.right     = '20px';
        overlay.style.transform = 'translateY(-50%)';
    }
}
</script>
@endsection