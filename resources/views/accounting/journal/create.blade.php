@extends('layouts.app')
@section('content')
<div class="container-fluid">
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Buat Jurnal Baru</h1>
  </div>
  <form method="POST" action="{{ route('accounting.journal.store') }}" id="journalForm">
    @csrf
    <div class="card shadow mb-4">
      <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Informasi Jurnal</h6></div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-4">
            <div class="form-group"><label>Periode</label>
              <select name="period_id" class="form-control" required>
                @foreach($periods as $p) <option value="{{ $p->id }}">{{ $p->name }}</option> @endforeach
              </select>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group"><label>Tanggal</label><input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required></div>
          </div>
        </div>
        <div class="form-group"><label>Deskripsi</label><textarea name="description" class="form-control" rows="2" required></textarea></div>
      </div>
    </div>
    <div class="card shadow mb-4">
      <div class="card-header py-3 d-flex justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Entri Jurnal</h6>
        <button type="button" class="btn btn-success btn-sm" onclick="addLine()"><i class="fas fa-plus"></i> Baris</button>
      </div>
      <div class="card-body">
        <table class="table table-bordered" id="entriesTable">
          <thead><tr><th>Akun</th><th>Debit</th><th>Kredit</th><th>Memo</th><th></th></tr></thead>
          <tbody id="entriesBody">
            <tr class="entry-row">
              <td><select name="entries[0][account_id]" class="form-control form-control-sm" required>
                <option value="">-- Pilih --</option>
                @foreach($accounts as $acc) <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option> @endforeach
              </select></td>
              <td><input type="number" name="entries[0][debit]" class="form-control form-control-sm debit" step="0.01" min="0" value="0" oninput="calcTotals()"></td>
              <td><input type="number" name="entries[0][credit]" class="form-control form-control-sm credit" step="0.01" min="0" value="0" oninput="calcTotals()"></td>
              <td><input type="text" name="entries[0][memo]" class="form-control form-control-sm"></td>
              <td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove();calcTotals()"><i class="fas fa-trash"></i></button></td>
            </tr>
            <tr class="entry-row">
              <td><select name="entries[1][account_id]" class="form-control form-control-sm" required>
                <option value="">-- Pilih --</option>
                @foreach($accounts as $acc) <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option> @endforeach
              </select></td>
              <td><input type="number" name="entries[1][debit]" class="form-control form-control-sm debit" step="0.01" min="0" value="0" oninput="calcTotals()"></td>
              <td><input type="number" name="entries[1][credit]" class="form-control form-control-sm credit" step="0.01" min="0" value="0" oninput="calcTotals()"></td>
              <td><input type="text" name="entries[1][memo]" class="form-control form-control-sm"></td>
              <td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove();calcTotals()"><i class="fas fa-trash"></i></button></td>
            </tr>
          </tbody>
          <tfoot><tr class="font-weight-bold"><td class="text-right">TOTAL</td>
            <td class="text-right" id="totalDebit">0</td><td class="text-right" id="totalCredit">0</td><td></td><td></td></tr></tfoot>
        </table>
        <div id="balanceWarning" class="alert alert-danger d-none">Debit != Kredit!</div>
      </div>
    </div>
    <button type="submit" class="btn btn-primary">Simpan</button>
    <a href="{{ route('accounting.journal.index') }}" class="btn btn-secondary">Batal</a>
  </form>
</div>
<script>
let li=2;
function addLine(){const h=`<tr class="entry-row"><td><select name="entries[${li}][account_id]" class="form-control form-control-sm" required><option value="">-- Pilih --</option>@foreach($accounts as $acc)<option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>@endforeach</select></td><td><input type="number" name="entries[${li}][debit]" class="form-control form-control-sm debit" step="0.01" min="0" value="0" oninput="calcTotals()"></td><td><input type="number" name="entries[${li}][credit]" class="form-control form-control-sm credit" step="0.01" min="0" value="0" oninput="calcTotals()"></td><td><input type="text" name="entries[${li}][memo]" class="form-control form-control-sm"></td><td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove();calcTotals()"><i class="fas fa-trash"></i></button></td></tr>`;document.getElementById('entriesBody').insertAdjacentHTML('beforeend',h);li++;}
function calcTotals(){let d=0,c=0;document.querySelectorAll('.debit').forEach(i=>d+=parseFloat(i.value)||0);document.querySelectorAll('.credit').forEach(i=>c+=parseFloat(i.value)||0);document.getElementById('totalDebit').textContent=d.toLocaleString('id-ID',{minimumFractionDigits:2});document.getElementById('totalCredit').textContent=c.toLocaleString('id-ID',{minimumFractionDigits:2});const w=document.getElementById('balanceWarning');Math.abs(d-c)>0.01?w.classList.remove('d-none'):w.classList.add('d-none');}
calcTotals();
</script>
@endsection
