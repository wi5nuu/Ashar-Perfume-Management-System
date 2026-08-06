@extends('layouts.app')

@section('title', 'Buat Jurnal Baru')

@section('content')
<style>
:root { --primary:#FF6B35; --primary-dark:#E55A2B; --secondary:#2D3047; }

.page-header-bar {
    background:#fff; border-radius:14px; padding:1.2rem 1.6rem; margin-bottom:1.5rem;
    box-shadow:0 2px 12px rgba(0,0,0,.06);
    display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.8rem;
}
.page-header-bar h4 { font-weight:700; color:var(--secondary); margin:0; font-size:1.15rem; display:flex; align-items:center; gap:.5rem; }
.page-header-bar h4 i { color:var(--primary); }

/* Form cards */
.form-card {
    background:#fff; border-radius:14px; box-shadow:0 2px 14px rgba(0,0,0,.07);
    border:1px solid rgba(0,0,0,.04); overflow:hidden; margin-bottom:1.5rem;
}
.form-card-header {
    padding:1rem 1.5rem; border-bottom:1px solid #f5f5f5;
    display:flex; align-items:center; justify-content:space-between;
    background:linear-gradient(90deg, #fafafa, #fff);
}
.form-card-header h5 { font-size:.95rem; font-weight:700; color:var(--secondary); margin:0; display:flex; align-items:center; gap:.5rem; }
.form-card-header h5 i { color:var(--primary); }
.form-card-body { padding:1.4rem 1.5rem; }

/* Form controls */
.form-label-custom { font-size:.78rem; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:#888; margin-bottom:.35rem; display:block; }
.form-control-custom {
    width:100%; border-radius:9px; border:1.5px solid #e8e8e8; padding:.52rem .85rem;
    font-size:.88rem; color:var(--secondary); background:#fff;
    transition:border-color .2s, box-shadow .2s; outline:none;
}
.form-control-custom:focus { border-color:var(--primary); box-shadow:0 0 0 3px rgba(255,107,53,.12); }
.form-control-custom.is-invalid { border-color:#E74C3C; }

/* Journal number display */
.journal-num-display {
    background:#f8f9fb; border:1.5px solid #e8e8e8; border-radius:9px;
    padding:.52rem .85rem; font-size:.88rem; font-family:'Courier New',monospace;
    color:#888; display:flex; align-items:center; gap:.5rem;
}
.journal-num-display i { color:var(--primary); font-size:.8rem; }

/* Entries table */
.entries-wrapper { overflow-x:auto; }
.entries-table { width:100%; border-collapse:collapse; min-width:700px; }
.entries-table thead th {
    background:#f8f9fb; color:#666; font-size:.74rem; font-weight:700;
    text-transform:uppercase; letter-spacing:.4px; padding:.7rem 1rem;
    border:none; border-bottom:2px solid #eee; white-space:nowrap;
}
.entries-table tbody td { padding:.55rem .6rem; border:none; border-bottom:1px solid #f5f5f5; vertical-align:middle; }
.entries-table tbody tr:hover td { background:#fafafa; }
.entries-table tbody tr:last-child td { border-bottom:none; }

/* Entry row inputs */
.entry-select, .entry-input, .entry-memo {
    width:100%; border-radius:7px; border:1.5px solid #e8e8e8;
    padding:.4rem .65rem; font-size:.84rem; color:var(--secondary);
    transition:border-color .2s; background:#fff; outline:none;
}
.entry-select:focus, .entry-input:focus, .entry-memo:focus {
    border-color:var(--primary); box-shadow:0 0 0 2px rgba(255,107,53,.1);
}
.entry-input { text-align:right; font-variant-numeric:tabular-nums; }
.entry-input.debit-input:focus  { border-color:#27AE60; box-shadow:0 0 0 2px rgba(39,174,96,.12); }
.entry-input.credit-input:focus { border-color:#E74C3C; box-shadow:0 0 0 2px rgba(231,76,60,.12); }

/* Totals footer */
.totals-footer {
    background:linear-gradient(90deg, #f8f9fb, #fff);
    border-top:2px solid #eee; padding:.8rem 1rem;
}
.totals-grid { display:flex; gap:1.5rem; align-items:center; flex-wrap:wrap; }
.total-item { display:flex; flex-direction:column; min-width:180px; }
.total-label { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.4px; color:#aaa; margin-bottom:.2rem; }
.total-debit-val  { font-size:1.15rem; font-weight:800; color:#27AE60; font-variant-numeric:tabular-nums; }
.total-credit-val { font-size:1.15rem; font-weight:800; color:#E74C3C; font-variant-numeric:tabular-nums; }
.total-diff-val   { font-size:1.15rem; font-weight:800; font-variant-numeric:tabular-nums; }
.total-diff-balanced   { color:#27AE60; }
.total-diff-unbalanced { color:#E74C3C; }

/* Balance warning */
.balance-warning {
    background:rgba(231,76,60,.08); border:1.5px solid rgba(231,76,60,.2);
    border-radius:9px; padding:.7rem 1.1rem; margin-top:.8rem;
    font-size:.84rem; color:#c0392b; font-weight:600;
    display:flex; align-items:center; gap:.5rem;
}
.balance-ok {
    background:rgba(39,174,96,.08); border:1.5px solid rgba(39,174,96,.2);
    border-radius:9px; padding:.7rem 1.1rem; margin-top:.8rem;
    font-size:.84rem; color:#1a8a4a; font-weight:600;
    display:flex; align-items:center; gap:.5rem;
}

/* Buttons */
.btn-add-row {
    background:rgba(39,174,96,.1); color:#27AE60; border:1.5px solid rgba(39,174,96,.25);
    padding:.4rem .9rem; border-radius:8px; font-size:.82rem; font-weight:600;
    display:inline-flex; align-items:center; gap:.4rem; transition:all .2s; cursor:pointer;
}
.btn-add-row:hover { background:#27AE60; color:#fff; border-color:#27AE60; }
.btn-del-row {
    background:rgba(231,76,60,.08); color:#E74C3C; border:1.5px solid rgba(231,76,60,.2);
    padding:.3rem .55rem; border-radius:7px; font-size:.8rem; transition:all .2s; cursor:pointer;
}
.btn-del-row:hover { background:#E74C3C; color:#fff; border-color:#E74C3C; }
.btn-submit {
    background:var(--primary); color:#fff; border:none;
    padding:.6rem 2rem; border-radius:10px; font-weight:700; font-size:.92rem;
    display:inline-flex; align-items:center; gap:.5rem; transition:background .2s;
}
.btn-submit:hover { background:var(--primary-dark); }
.btn-submit:disabled { background:#ccc; cursor:not-allowed; }
.btn-cancel {
    background:transparent; color:#888; border:1.5px solid #e8e8e8;
    padding:.6rem 1.4rem; border-radius:10px; font-size:.88rem;
    display:inline-flex; align-items:center; gap:.4rem; transition:all .2s;
    text-decoration:none;
}
.btn-cancel:hover { border-color:var(--primary); color:var(--primary); text-decoration:none; }

/* Row number badge */
.row-num { width:28px; height:28px; border-radius:7px; background:rgba(45,48,71,.07); color:var(--secondary); display:flex; align-items:center; justify-content:center; font-size:.76rem; font-weight:700; flex-shrink:0; }
</style>

<div class="container-fluid">

    {{-- PAGE HEADER --}}
    <div class="page-header-bar">
        <h4><i class="fas fa-plus-circle"></i> Buat Jurnal Baru</h4>
        <a href="{{ route('accounting.journal.index') }}" class="btn-cancel">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <form method="POST" action="{{ route('accounting.journal.store') }}" id="journalForm">
        @csrf

        {{-- HEADER INFO --}}
        <div class="form-card">
            <div class="form-card-header">
                <h5><i class="fas fa-info-circle"></i> Informasi Jurnal</h5>
                <span style="font-size:.78rem;color:#aaa">Semua field wajib diisi</span>
            </div>
            <div class="form-card-body">
                <div class="row">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <label class="form-label-custom">No. Jurnal</label>
                        <div class="journal-num-display">
                            <i class="fas fa-hashtag"></i>
                            <span>Auto-generated</span>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <label class="form-label-custom">Periode <span style="color:#E74C3C">*</span></label>
                        <select name="period_id" class="form-control-custom" required>
                            <option value="">— Pilih Periode —</option>
                            @foreach($periods as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                        @error('period_id')
                        <div style="font-size:.78rem;color:#E74C3C;margin-top:.3rem"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <label class="form-label-custom">Tanggal <span style="color:#E74C3C">*</span></label>
                        <input type="date" name="date" class="form-control-custom" value="{{ date('Y-m-d') }}" required>
                        @error('date')
                        <div style="font-size:.78rem;color:#E74C3C;margin-top:.3rem"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <label class="form-label-custom">Tipe Jurnal</label>
                        <select name="journal_type" class="form-control-custom">
                            <option value="general">Jurnal Umum</option>
                            <option value="sales">Jurnal Penjualan</option>
                            <option value="purchase">Jurnal Pembelian</option>
                            <option value="cash">Jurnal Kas</option>
                            <option value="adjustment">Jurnal Penyesuaian</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <label class="form-label-custom">Deskripsi / Keterangan <span style="color:#E74C3C">*</span></label>
                        <textarea name="description" class="form-control-custom" rows="2" required placeholder="Masukkan keterangan transaksi..."></textarea>
                        @error('description')
                        <div style="font-size:.78rem;color:#E74C3C;margin-top:.3rem"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- JOURNAL ENTRIES --}}
        <div class="form-card">
            <div class="form-card-header">
                <h5><i class="fas fa-exchange-alt"></i> Entri Jurnal (Double Entry)</h5>
                <button type="button" class="btn-add-row" onclick="addLine()">
                    <i class="fas fa-plus"></i> Tambah Baris
                </button>
            </div>

            <div class="entries-wrapper">
                <table class="entries-table" id="entriesTable">
                    <thead>
                        <tr>
                            <th style="width:40px">#</th>
                            <th style="min-width:260px">Akun</th>
                            <th style="min-width:180px">Keterangan Baris</th>
                            <th style="width:160px;text-align:right">Debit (Rp)</th>
                            <th style="width:160px;text-align:right">Kredit (Rp)</th>
                            <th style="width:50px"></th>
                        </tr>
                    </thead>
                    <tbody id="entriesBody">
                        {{-- Row 0: Debit row --}}
                        <tr class="entry-row" id="row-0">
                            <td><div class="row-num">1</div></td>
                            <td>
                                <select name="entries[0][account_id]" class="entry-select" required>
                                    <option value="">— Pilih Akun —</option>
                                    @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->code }} — {{ $acc->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="text" name="entries[0][memo]" class="entry-memo" placeholder="Keterangan..."></td>
                            <td><input type="number" name="entries[0][debit]"  class="entry-input debit-input"  step="1" min="0" value="0" oninput="calcTotals()"></td>
                            <td><input type="number" name="entries[0][credit]" class="entry-input credit-input" step="1" min="0" value="0" oninput="calcTotals()"></td>
                            <td></td>
                        </tr>
                        {{-- Row 1: Credit row --}}
                        <tr class="entry-row" id="row-1">
                            <div class="row-num" style="display:none">2</div>
                            <td><div class="row-num">2</div></td>
                            <td>
                                <select name="entries[1][account_id]" class="entry-select" required>
                                    <option value="">— Pilih Akun —</option>
                                    @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->code }} — {{ $acc->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="text" name="entries[1][memo]" class="entry-memo" placeholder="Keterangan..."></td>
                            <td><input type="number" name="entries[1][debit]"  class="entry-input debit-input"  step="1" min="0" value="0" oninput="calcTotals()"></td>
                            <td><input type="number" name="entries[1][credit]" class="entry-input credit-input" step="1" min="0" value="0" oninput="calcTotals()"></td>
                            <td>
                                <button type="button" class="btn-del-row" onclick="removeRow(this)" title="Hapus baris">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- TOTALS FOOTER --}}
            <div class="totals-footer">
                <div class="totals-grid">
                    <div class="total-item">
                        <div class="total-label"><i class="fas fa-arrow-down mr-1" style="color:#27AE60"></i>Total Debit</div>
                        <div class="total-debit-val" id="totalDebitDisplay">Rp 0</div>
                    </div>
                    <div style="font-size:1.4rem;color:#ddd;font-weight:300">=</div>
                    <div class="total-item">
                        <div class="total-label"><i class="fas fa-arrow-up mr-1" style="color:#E74C3C"></i>Total Kredit</div>
                        <div class="total-credit-val" id="totalCreditDisplay">Rp 0</div>
                    </div>
                    <div style="font-size:1.4rem;color:#ddd;font-weight:300">|</div>
                    <div class="total-item">
                        <div class="total-label">Selisih</div>
                        <div class="total-diff-val total-diff-balanced" id="totalDiffDisplay">Rp 0 ✓</div>
                    </div>
                    <input type="hidden" id="totalDebit"  name="_total_debit">
                    <input type="hidden" id="totalCredit" name="_total_credit">
                </div>

                <div id="balanceWarning" class="balance-warning d-none">
                    <i class="fas fa-exclamation-triangle"></i>
                    Jurnal belum seimbang! Total Debit harus sama dengan Total Kredit sebelum bisa disimpan.
                </div>
                <div id="balanceOk" class="balance-ok d-none">
                    <i class="fas fa-check-circle"></i>
                    Jurnal seimbang. Siap untuk disimpan.
                </div>
            </div>
        </div>

        {{-- SUBMIT ACTIONS --}}
        <div class="form-card">
            <div class="form-card-body">
                <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap:.8rem">
                    <div style="font-size:.82rem;color:#aaa">
                        <i class="fas fa-info-circle mr-1" style="color:var(--primary)"></i>
                        Jurnal yang disimpan sebagai <strong>Draft</strong> bisa diedit sebelum diposting.
                        Setelah <strong>Posted</strong>, jurnal tidak dapat diubah.
                    </div>
                    <div class="d-flex" style="gap:.8rem">
                        <a href="{{ route('accounting.journal.index') }}" class="btn-cancel">
                            <i class="fas fa-times"></i> Batal
                        </a>
                        <button type="submit" name="action" value="draft" class="btn-submit" style="background:#F39C12" id="btnSaveDraft">
                            <i class="fas fa-save"></i> Simpan Draft
                        </button>
                        <button type="submit" name="action" value="post" class="btn-submit" id="btnPost">
                            <i class="fas fa-check-circle"></i> Simpan &amp; Post
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
// Track line index (starts at 2 since we already have 0 and 1)
let lineIndex = 2;

// Accounts data for dynamic rows
const accountOptions = `@foreach($accounts as $acc)<option value="{{ $acc->id }}">{{ $acc->code }} — {{ $acc->name }}</option>@endforeach`;

function addLine() {
    const idx = lineIndex++;
    const rowNum = document.querySelectorAll('#entriesBody .entry-row').length + 1;
    const html = `
        <tr class="entry-row" id="row-${idx}">
            <td><div class="row-num">${rowNum}</div></td>
            <td>
                <select name="entries[${idx}][account_id]" class="entry-select" required>
                    <option value="">— Pilih Akun —</option>
                    ${accountOptions}
                </select>
            </td>
            <td><input type="text" name="entries[${idx}][memo]" class="entry-memo" placeholder="Keterangan..."></td>
            <td><input type="number" name="entries[${idx}][debit]"  class="entry-input debit-input"  step="1" min="0" value="0" oninput="calcTotals()"></td>
            <td><input type="number" name="entries[${idx}][credit]" class="entry-input credit-input" step="1" min="0" value="0" oninput="calcTotals()"></td>
            <td>
                <button type="button" class="btn-del-row" onclick="removeRow(this)" title="Hapus baris">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>`;
    document.getElementById('entriesBody').insertAdjacentHTML('beforeend', html);
    renumberRows();
}

function removeRow(btn) {
    const rows = document.querySelectorAll('#entriesBody .entry-row');
    if (rows.length <= 2) {
        Swal.fire('Tidak Bisa Dihapus', 'Jurnal membutuhkan minimal 2 baris entri.', 'warning');
        return;
    }
    btn.closest('tr').remove();
    renumberRows();
    calcTotals();
}

function renumberRows() {
    document.querySelectorAll('#entriesBody .entry-row').forEach((row, idx) => {
        const numEl = row.querySelector('.row-num');
        if (numEl) numEl.textContent = idx + 1;
    });
}

function formatRupiah(val) {
    return 'Rp ' + Math.round(val).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

function calcTotals() {
    let debit  = 0;
    let credit = 0;

    document.querySelectorAll('.debit-input').forEach(i  => debit  += parseFloat(i.value)  || 0);
    document.querySelectorAll('.credit-input').forEach(i => credit += parseFloat(i.value) || 0);

    const diff = Math.abs(debit - credit);
    const balanced = diff < 1;

    document.getElementById('totalDebitDisplay').textContent  = formatRupiah(debit);
    document.getElementById('totalCreditDisplay').textContent = formatRupiah(credit);
    document.getElementById('totalDebit').value  = debit;
    document.getElementById('totalCredit').value = credit;

    const diffEl = document.getElementById('totalDiffDisplay');
    if (balanced) {
        diffEl.textContent = 'Rp 0 ✓';
        diffEl.className = 'total-diff-val total-diff-balanced';
    } else {
        diffEl.textContent = formatRupiah(diff) + ' ✗';
        diffEl.className = 'total-diff-val total-diff-unbalanced';
    }

    document.getElementById('balanceWarning').classList.toggle('d-none', balanced);
    document.getElementById('balanceOk').classList.toggle('d-none', !balanced);
    document.getElementById('btnPost').disabled = !balanced;
}

// Block form submit if unbalanced on "post" action
document.getElementById('journalForm').addEventListener('submit', function(e) {
    const action = e.submitter ? e.submitter.value : '';
    if (action === 'post') {
        let debit  = 0;
        let credit = 0;
        document.querySelectorAll('.debit-input').forEach(i  => debit  += parseFloat(i.value)  || 0);
        document.querySelectorAll('.credit-input').forEach(i => credit += parseFloat(i.value) || 0);
        if (Math.abs(debit - credit) >= 1) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Jurnal Tidak Seimbang',
                text: 'Total Debit harus sama dengan Total Kredit untuk memposting jurnal.',
                confirmButtonColor: '#FF6B35'
            });
        }
    }
});

// Init
calcTotals();
</script>
@endpush
