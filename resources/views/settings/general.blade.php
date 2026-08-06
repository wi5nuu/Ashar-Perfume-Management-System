@extends('layouts.app')
@section('title', 'Pengaturan Umum - APMS')

@section('content')
<div class="container-fluid pt-3 pb-5">

<style>
:root{--primary:#FF6B35;--primary-dark:#E55A2B;--secondary:#2D3047;}
.pg-title{font-size:1.5rem;font-weight:700;color:var(--secondary);}
.pg-sub{font-size:.82rem;color:#8a94a6;margin:0;}
.bc{background:transparent;padding:0;font-size:.78rem;}
.bc a{color:var(--primary);}
.sec-card{border-radius:14px!important;border:1.5px solid #f1f3f6;box-shadow:0 2px 12px rgba(0,0,0,.05);margin-bottom:1.5rem;}
.sec-card .card-header{background:linear-gradient(135deg,#fff9f7,#fff);border-bottom:1px solid #f1f3f6;border-radius:14px 14px 0 0!important;padding:14px 20px;}
.sec-icon{width:34px;height:34px;border-radius:9px;display:inline-flex;align-items:center;justify-content:center;font-size:.9rem;flex-shrink:0;margin-right:12px;}
.lbl{font-size:.75rem;font-weight:600;color:#4B5563;text-transform:uppercase;letter-spacing:.04em;margin-bottom:4px;display:block;}
.fc{border:1.5px solid #e5e7eb;border-radius:9px;font-size:.87rem;padding:8px 12px;width:100%;transition:border-color .2s,box-shadow .2s;}
.fc:focus{border-color:var(--primary);box-shadow:0 0 0 3px rgba(255,107,53,.12);outline:none;}
.logo-zone{border:2px dashed #e5e7eb;border-radius:12px;padding:28px;text-align:center;cursor:pointer;transition:all .2s;background:#fafbfc;}
.logo-zone:hover{border-color:var(--primary);background:#fff9f7;}
.logo-zone img{max-height:80px;border-radius:8px;}
.color-dot{width:30px;height:30px;border-radius:50%;cursor:pointer;border:3px solid transparent;display:inline-block;transition:transform .15s;}
.color-dot.active,.color-dot:hover{border-color:var(--secondary);transform:scale(1.2);}
.inv-preview{background:#fff;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;font-size:.75rem;}
.inv-head{background:var(--primary);color:#fff;padding:14px 16px;text-align:center;}
.inv-head h6{color:#fff;font-size:.85rem;font-weight:700;margin:0;}
.inv-body{padding:14px 16px;}
.inv-row{display:flex;justify-content:space-between;padding:3px 0;border-bottom:1px dashed #f1f3f6;font-size:.72rem;}
.inv-total{display:flex;justify-content:space-between;padding:6px 0;font-weight:700;font-size:.82rem;color:var(--primary);border-top:2px solid var(--primary);margin-top:4px;}
.inv-footer{text-align:center;padding-top:8px;color:#8a94a6;font-size:.7rem;border-top:1px solid #f1f3f6;margin-top:8px;}
.btn-save{background:var(--primary);color:#fff;border:none;border-radius:9px;font-weight:600;padding:10px 28px;transition:all .2s;font-size:.9rem;}
.btn-save:hover{background:var(--primary-dark);transform:translateY(-1px);box-shadow:0 4px 14px rgba(255,107,53,.3);}
.btn-save:disabled{opacity:.65;transform:none;}
.sticky-save{position:sticky;bottom:0;background:rgba(255,255,255,.95);backdrop-filter:blur(8px);border-top:1px solid #e5e7eb;padding:14px 0;margin-top:24px;z-index:50;}
</style>

{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-start flex-wrap mb-4">
  <div>
    <nav aria-label="breadcrumb"><ol class="breadcrumb bc mb-1">
      <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i></a></li>
      <li class="breadcrumb-item"><a href="{{ route('settings.index') }}">Pengaturan</a></li>
      <li class="breadcrumb-item active">Pengaturan Umum</li>
    </ol></nav>
    <h3 class="pg-title mb-0">Pengaturan Umum</h3>
    <p class="pg-sub">Informasi toko, branding, regional, dan konfigurasi invoice</p>
  </div>
  <a href="{{ route('settings.index') }}" class="btn btn-outline-secondary btn-sm mt-2 mt-md-0"><i class="fas fa-arrow-left mr-1"></i>Kembali</a>
</div>

<form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data" id="form-general">
@csrf
<div class="row">

{{-- LEFT COLUMN --}}
<div class="col-lg-8">

{{-- SECTION 1: Info Toko --}}
<div class="card sec-card">
  <div class="card-header d-flex align-items-center">
    <span class="sec-icon" style="background:rgba(255,107,53,.12);"><i class="fas fa-store-alt" style="color:var(--primary)"></i></span>
    <div><h6 class="mb-0" style="font-size:.9rem;font-weight:700;color:var(--secondary);">Informasi Toko</h6><small class="text-muted" style="font-size:.73rem;">Identitas dan kontak resmi toko</small></div>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-md-6 mb-3"><label class="lbl">Nama Toko <span class="text-danger">*</span></label><input type="text" name="store_name" class="fc form-control" value="{{ old('store_name', config('app.store_name', 'Asghar Grosir Perfume')) }}" required id="inp-store-name"></div>
      <div class="col-md-6 mb-3"><label class="lbl">Telepon</label><input type="text" name="store_phone" class="fc form-control" value="{{ old('store_phone', '') }}" id="inp-phone"></div>
      <div class="col-12 mb-3"><label class="lbl">Alamat Lengkap</label><input type="text" name="store_address" class="fc form-control" value="{{ old('store_address', '') }}" id="inp-address"></div>
      <div class="col-md-4 mb-3"><label class="lbl">Kota</label><input type="text" name="store_city" class="fc form-control" value="{{ old('store_city', '') }}"></div>
      <div class="col-md-4 mb-3"><label class="lbl">Provinsi</label><input type="text" name="store_province" class="fc form-control" value="{{ old('store_province', '') }}"></div>
      <div class="col-md-4 mb-3"><label class="lbl">Kode Pos</label><input type="text" name="store_postal" class="fc form-control" value="{{ old('store_postal', '') }}"></div>
      <div class="col-md-6 mb-3"><label class="lbl">Email</label><input type="email" name="store_email" class="fc form-control" value="{{ old('store_email', '') }}" id="inp-email"></div>
      <div class="col-md-6 mb-3"><label class="lbl">Website</label><input type="url" name="store_website" class="fc form-control" value="{{ old('store_website', '') }}" placeholder="https://"></div>
    </div>
  </div>
</div>

{{-- SECTION 2: Logo & Branding --}}
<div class="card sec-card">
  <div class="card-header d-flex align-items-center">
    <span class="sec-icon" style="background:rgba(139,92,246,.12);"><i class="fas fa-palette" style="color:#8B5CF6;"></i></span>
    <div><h6 class="mb-0" style="font-size:.9rem;font-weight:700;color:var(--secondary);">Logo & Branding</h6><small class="text-muted" style="font-size:.73rem;">Upload logo, favicon, dan warna tema</small></div>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-md-6 mb-3">
        <label class="lbl">Logo Toko</label>
        <div class="logo-zone" id="logo-drop" onclick="document.getElementById('inp-logo').click()">
          <i class="fas fa-cloud-upload-alt fa-2x mb-2" style="color:var(--primary);opacity:.6;"></i>
          <p class="mb-0 text-muted" style="font-size:.8rem;">Klik atau drag & drop logo<br><small>PNG, JPG, SVG • Maks 2MB</small></p>
        </div>
        <input type="file" name="store_logo" id="inp-logo" accept="image/*" style="display:none">
      </div>
      <div class="col-md-6 mb-3">
        <label class="lbl">Favicon</label>
        <div class="logo-zone" style="padding:18px;" onclick="document.getElementById('inp-favicon').click()">
          <i class="fas fa-bookmark fa-2x mb-2" style="color:#8B5CF6;opacity:.6;"></i>
          <p class="mb-0 text-muted" style="font-size:.8rem;">Upload Favicon<br><small>ICO, PNG 32x32</small></p>
        </div>
        <input type="file" name="store_favicon" id="inp-favicon" accept="image/*,.ico" style="display:none">
      </div>
      <div class="col-12 mb-2">
        <label class="lbl">Warna Tema</label>
        <div class="d-flex align-items-center flex-wrap" style="gap:10px;">
          <div class="color-dot active" style="background:#FF6B35;" data-color="#FF6B35" title="Orange (Default)" onclick="setColor(this)"></div>
          <div class="color-dot" style="background:#2D3047;" data-color="#2D3047" title="Navy" onclick="setColor(this)"></div>
          <div class="color-dot" style="background:#10B981;" data-color="#10B981" title="Emerald" onclick="setColor(this)"></div>
          <div class="color-dot" style="background:#3B82F6;" data-color="#3B82F6" title="Blue" onclick="setColor(this)"></div>
          <div class="color-dot" style="background:#8B5CF6;" data-color="#8B5CF6" title="Purple" onclick="setColor(this)"></div>
          <div class="color-dot" style="background:#EF4444;" data-color="#EF4444" title="Red" onclick="setColor(this)"></div>
          <input type="color" name="theme_color" id="inp-theme-color" value="#FF6B35" style="width:30px;height:30px;border-radius:50%;border:3px solid #e5e7eb;cursor:pointer;padding:1px;" title="Custom color">
        </div>
        <input type="hidden" name="brand_color" id="brand-color-val" value="#FF6B35">
      </div>
    </div>
  </div>
</div>

{{-- SECTION 3: Regional --}}
<div class="card sec-card">
  <div class="card-header d-flex align-items-center">
    <span class="sec-icon" style="background:rgba(6,182,212,.12);"><i class="fas fa-globe-asia" style="color:#06B6D4;"></i></span>
    <div><h6 class="mb-0" style="font-size:.9rem;font-weight:700;color:var(--secondary);">Pengaturan Regional</h6><small class="text-muted" style="font-size:.73rem;">Zona waktu, format tanggal, mata uang, bahasa</small></div>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-md-6 mb-3">
        <label class="lbl">Zona Waktu</label>
        <select name="timezone" class="fc form-control">
          <option value="Asia/Jakarta" {{ old('timezone', config('app.timezone', 'Asia/Jakarta')) == 'Asia/Jakarta' ? 'selected' : '' }}>Asia/Jakarta (WIB)</option>
          <option value="Asia/Makassar" {{ old('timezone') == 'Asia/Makassar' ? 'selected' : '' }}>Asia/Makassar (WITA)</option>
          <option value="Asia/Jayapura" {{ old('timezone') == 'Asia/Jayapura' ? 'selected' : '' }}>Asia/Jayapura (WIT)</option>
        </select>
      </div>
      <div class="col-md-6 mb-3">
        <label class="lbl">Bahasa</label>
        <select name="locale" class="fc form-control">
          <option value="id" {{ old('locale', 'id') == 'id' ? 'selected' : '' }}>Bahasa Indonesia</option>
          <option value="en" {{ old('locale') == 'en' ? 'selected' : '' }}>English</option>
        </select>
      </div>
      <div class="col-md-6 mb-3">
        <label class="lbl">Format Tanggal</label>
        <select name="date_format" class="fc form-control">
          <option value="d/m/Y">DD/MM/YYYY (31/12/2024)</option>
          <option value="Y-m-d">YYYY-MM-DD (2024-12-31)</option>
          <option value="d M Y">DD Mon YYYY (31 Des 2024)</option>
        </select>
      </div>
      <div class="col-md-6 mb-3">
        <label class="lbl">Format Mata Uang</label>
        <select name="currency_format" class="fc form-control">
          <option value="Rp">Rp (Rupiah)</option>
          <option value="IDR">IDR</option>
        </select>
      </div>
    </div>
  </div>
</div>

{{-- SECTION 4: Invoice --}}
<div class="card sec-card">
  <div class="card-header d-flex align-items-center">
    <span class="sec-icon" style="background:rgba(16,185,129,.12);"><i class="fas fa-file-invoice" style="color:#10B981;"></i></span>
    <div><h6 class="mb-0" style="font-size:.9rem;font-weight:700;color:var(--secondary);">Konfigurasi Invoice</h6><small class="text-muted" style="font-size:.73rem;">Prefix, nomor awal, footer, dan pesan terima kasih</small></div>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-md-4 mb-3"><label class="lbl">Prefix Invoice</label><input type="text" name="invoice_prefix" class="fc form-control" value="{{ old('invoice_prefix', 'INV') }}" maxlength="6" id="inp-inv-prefix"></div>
      <div class="col-md-4 mb-3"><label class="lbl">Nomor Awal</label><input type="number" name="invoice_start" class="fc form-control" value="{{ old('invoice_start', 1) }}" min="1" id="inp-inv-start"></div>
      <div class="col-md-4 mb-3"><label class="lbl">Separator</label><select name="invoice_sep" class="fc form-control" id="inp-inv-sep"><option value="-">Tanda Minus (-)</option><option value="/">/</option><option value="">Tanpa Separator</option></select></div>
      <div class="col-12 mb-3"><label class="lbl">Footer Invoice</label><textarea name="invoice_footer" class="fc form-control" rows="2" id="inp-inv-footer">{{ old('invoice_footer', 'Terima kasih telah berbelanja di Asghar Grosir Perfume.') }}</textarea></div>
      <div class="col-12 mb-3"><label class="lbl">Pesan Terima Kasih</label><input type="text" name="invoice_thanks" class="fc form-control" value="{{ old('invoice_thanks', 'Terima kasih atas kepercayaan Anda!') }}" id="inp-inv-thanks"></div>
    </div>
  </div>
</div>

{{-- SECTION 5: Harga Tier --}}
<div class="card sec-card">
  <div class="card-header d-flex align-items-center">
    <span class="sec-icon" style="background:rgba(255,107,53,.12);"><i class="fas fa-tags" style="color:#FF6B35;"></i></span>
    <div>
      <h6 class="mb-0" style="font-size:.9rem;font-weight:700;color:var(--secondary);">Harga Tetap Per Tier & Ukuran</h6>
      <small class="text-muted" style="font-size:.73rem;">Harga berlaku di POS saat kasir memilih kualitas parfum</small>
    </div>
  </div>
  <div class="card-body pb-2">

    {{-- Header kolom --}}
    <div class="row mb-1">
      <div class="col-md-3"></div>
      <div class="col-md-3 text-center"><span style="font-size:.78rem;font-weight:700;color:#FFB300;">Premium Original</span></div>
      <div class="col-md-3 text-center"><span style="font-size:.78rem;font-weight:700;color:#78909C;">Sedang</span></div>
      <div class="col-md-3 text-center"><span style="font-size:.78rem;font-weight:700;color:#66BB6A;">Standar</span></div>
    </div>

    {{-- 30ml --}}
    <div class="row mb-2 align-items-center">
      <div class="col-md-3"><label class="lbl mb-0">30 ml</label></div>
      <div class="col-md-3">
        <div class="input-group input-group-sm">
          <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
          <input type="number" name="tier_price_30ml_premium" class="fc form-control"
                 value="{{ old('tier_price_30ml_premium', \App\Models\Setting::getValue('tier_price_30ml_premium', 63000)) }}"
                 min="0" step="500">
        </div>
      </div>
      <div class="col-md-3">
        <div class="input-group input-group-sm">
          <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
          <input type="number" name="tier_price_30ml_sedang" class="fc form-control"
                 value="{{ old('tier_price_30ml_sedang', \App\Models\Setting::getValue('tier_price_30ml_sedang', 50000)) }}"
                 min="0" step="500">
        </div>
      </div>
      <div class="col-md-3">
        <div class="input-group input-group-sm">
          <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
          <input type="number" name="tier_price_30ml_biasa" class="fc form-control"
                 value="{{ old('tier_price_30ml_biasa', \App\Models\Setting::getValue('tier_price_30ml_biasa', 35000)) }}"
                 min="0" step="500">
        </div>
      </div>
    </div>

    {{-- 50ml --}}
    <div class="row mb-2 align-items-center">
      <div class="col-md-3"><label class="lbl mb-0">50 ml</label></div>
      <div class="col-md-3">
        <div class="input-group input-group-sm">
          <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
          <input type="number" name="tier_price_50ml_premium" class="fc form-control"
                 value="{{ old('tier_price_50ml_premium', \App\Models\Setting::getValue('tier_price_50ml_premium', 125000)) }}"
                 min="0" step="500">
        </div>
      </div>
      <div class="col-md-3">
        <div class="input-group input-group-sm">
          <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
          <input type="number" name="tier_price_50ml_sedang" class="fc form-control"
                 value="{{ old('tier_price_50ml_sedang', \App\Models\Setting::getValue('tier_price_50ml_sedang', 100000)) }}"
                 min="0" step="500">
        </div>
      </div>
      <div class="col-md-3">
        <div class="input-group input-group-sm">
          <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
          <input type="number" name="tier_price_50ml_biasa" class="fc form-control"
                 value="{{ old('tier_price_50ml_biasa', \App\Models\Setting::getValue('tier_price_50ml_biasa', 70000)) }}"
                 min="0" step="500">
        </div>
      </div>
    </div>

    {{-- 100ml --}}
    <div class="row mb-2 align-items-center">
      <div class="col-md-3"><label class="lbl mb-0">100 ml</label></div>
      <div class="col-md-3">
        <div class="input-group input-group-sm">
          <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
          <input type="number" name="tier_price_100ml_premium" class="fc form-control"
                 value="{{ old('tier_price_100ml_premium', \App\Models\Setting::getValue('tier_price_100ml_premium', 250000)) }}"
                 min="0" step="500">
        </div>
      </div>
      <div class="col-md-3">
        <div class="input-group input-group-sm">
          <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
          <input type="number" name="tier_price_100ml_sedang" class="fc form-control"
                 value="{{ old('tier_price_100ml_sedang', \App\Models\Setting::getValue('tier_price_100ml_sedang', 200000)) }}"
                 min="0" step="500">
        </div>
      </div>
      <div class="col-md-3">
        <div class="input-group input-group-sm">
          <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
          <input type="number" name="tier_price_100ml_biasa" class="fc form-control"
                 value="{{ old('tier_price_100ml_biasa', \App\Models\Setting::getValue('tier_price_100ml_biasa', 140000)) }}"
                 min="0" step="500">
        </div>
      </div>
    </div>

    <div class="alert alert-info py-2 mb-0 mt-1" style="font-size:.78rem;border-radius:8px;">
      <i class="fas fa-info-circle mr-1"></i>
      Harga otomatis menyesuaikan ukuran produk saat kasir memilih kualitas di POS.
    </div>
  </div>
</div>

{{-- Sticky Save Bar --}}
<div class="sticky-save">
  <button type="submit" class="btn-save" id="btn-save"><i class="fas fa-save mr-2"></i>Simpan Pengaturan</button>
  <span class="text-muted ml-3" style="font-size:.8rem;" id="save-status"></span>
</div>

</div>{{-- end col-lg-8 --}}

{{-- RIGHT COLUMN: Invoice Preview --}}
<div class="col-lg-4">
  <div style="position:sticky;top:80px;">
    <div class="card" style="border-radius:14px;border:1.5px solid #f1f3f6;box-shadow:0 2px 12px rgba(0,0,0,.05);">
      <div class="card-header" style="background:linear-gradient(135deg,#fff9f7,#fff);border-bottom:1px solid #f1f3f6;border-radius:14px 14px 0 0;">
        <h6 class="mb-0" style="font-size:.85rem;font-weight:700;color:var(--secondary);"><i class="fas fa-eye mr-2 text-primary"></i>Preview Invoice</h6>
      </div>
      <div class="card-body p-3">
        <div class="inv-preview">
          <div class="inv-head"><h6 id="prev-store-name">Asghar Grosir Perfume</h6><div style="font-size:.7rem;opacity:.85;" id="prev-phone"></div></div>
          <div class="inv-body">
            <div style="text-align:center;margin-bottom:8px;">
              <div style="font-size:.8rem;font-weight:700;color:var(--secondary);" id="prev-inv-no">INV-0001</div>
              <div style="font-size:.68rem;color:#8a94a6;">{{ now()->format('d/m/Y H:i') }}</div>
            </div>
            <div class="inv-row"><span>Parfum Rose Gold 100ml</span><span>Rp 150.000</span></div>
            <div class="inv-row"><span>Parfum Oud King 50ml</span><span>Rp 85.000</span></div>
            <div class="inv-row"><span>Subtotal</span><span>Rp 235.000</span></div>
            <div class="inv-row"><span>Diskon</span><span style="color:#EF4444;">- Rp 0</span></div>
            <div class="inv-total"><span>TOTAL</span><span>Rp 235.000</span></div>
          </div>
          <div class="inv-footer" id="prev-footer">Terima kasih telah berbelanja!</div>
        </div>
        <div class="mt-3 p-2" style="background:#f8f9fa;border-radius:8px;font-size:.72rem;color:#6B7280;">
          <i class="fas fa-info-circle mr-1 text-primary"></i>Preview diperbarui otomatis saat Anda mengisi formulir
        </div>
      </div>
    </div>
  </div>
</div>

</div>{{-- end row --}}
</form>
</div>{{-- end container --}}
@endsection

@push('scripts')
<script>
$(function(){
  // Live preview update
  function updatePreview(){
    var name = $('#inp-store-name').val() || 'Nama Toko';
    var phone = $('#inp-phone').val() || ''';
    var prefix = $('#inp-inv-prefix').val() || 'INV';
    var start = $('#inp-inv-start').val() || 1;
    var sep = $('#inp-inv-sep').val();
    var thanks = $('#inp-inv-thanks').val() || 'Terima kasih!';
    var footer = $('#inp-inv-footer').val() || '';
    var num = String(parseInt(start)).padStart(4,'0');
    $('#prev-store-name').text(name);
    $('#prev-phone').text(phone);
    $('#prev-inv-no').text(prefix + sep + num);
    $('#prev-footer').text(thanks + (footer ? ' ' + footer : ''));
  }
  $('#inp-store-name,#inp-phone,#inp-inv-prefix,#inp-inv-start,#inp-inv-sep,#inp-inv-thanks,#inp-inv-footer').on('input change', updatePreview);
  updatePreview();

  // Logo preview
  $('#inp-logo').on('change', function(){
    if(this.files[0]){
      var reader = new FileReader();
      reader.onload = function(e){
        $('#logo-drop').html('<img src="' + e.target.result + '" style="max-height:80px;border-radius:8px;">');
      };
      reader.readAsDataURL(this.files[0]);
    }
  });

  // Color swatches
  window.setColor = function(el){
    $('.color-dot').removeClass('active');
    $(el).addClass('active');
    var color = $(el).data('color');
    $('#brand-color-val').val(color);
    $('#inp-theme-color').val(color);
    document.documentElement.style.setProperty('--primary', color);
  };
  $('#inp-theme-color').on('input', function(){
    var color = $(this).val();
    $('#brand-color-val').val(color);
    $('.color-dot').removeClass('active');
    document.documentElement.style.setProperty('--primary', color);
  });

  // Save with loading state
  $('#form-general').on('submit', function(e){
    var $btn = $('#btn-save');
    $btn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...').prop('disabled', true);
    $('#save-status').text('');
  });

  @if(session('success'))
  Swal.fire({icon:'success',title:'Tersimpan!',text:@json(session('success')),confirmButtonColor:'#FF6B35',timer:3000,timerProgressBar:true});
  @endif
  @if(session('error'))
  Swal.fire({icon:'error',title:'Gagal',text:@json(session('error')),confirmButtonColor:'#FF6B35'});
  @endif
  @if($errors->any())
  Swal.fire({icon:'warning',title:'Ada Kesalahan',html:@json(implode('<br>', $errors->all())),confirmButtonColor:'#FF6B35'});
  @endif
});
</script>
@endpush
