@extends('layouts.app')
@section('content')
<div class="container-fluid">
  <h1 class="h3 mb-4 text-gray-800">Activity Logs</h1>
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <form method="GET" class="form-inline">
        <select name="event" class="form-control form-control-sm mr-2">
          <option value="">Semua Event</option>
          @foreach($events as $e) <option value="{{ $e }}" {{ request('event')==$e?'selected':'' }}>{{ $e }}</option> @endforeach
        </select>
        <select name="subject_type" class="form-control form-control-sm mr-2">
          <option value="">Semua Tipe</option>
          @foreach($subjectTypes as $s) <option value="{{ $s }}" {{ request('subject_type')==$s?'selected':'' }}>{{ class_basename($s) }}</option> @endforeach
        </select>
        <input type="date" name="from" class="form-control form-control-sm mr-2" value="{{ request('from') }}">
        <input type="date" name="to" class="form-control form-control-sm mr-2" value="{{ request('to') }}">
        <button class="btn btn-sm btn-primary">Filter</button>
      </form>
    </div>
    <div class="card-body">
      <table class="table table-sm table-bordered">
        <thead><tr><th>Waktu</th><th>User</th><th>Event</th><th>Subject</th><th>Deskripsi</th><th>Aksi</th></tr></thead>
        <tbody>
          @forelse($logs as $l)
          <tr><td>{{ $l->created_at->format('d/m/Y H:i') }}</td><td>{{ $l->causer?->name??'System' }}</td>
            <td><span class="badge badge-info">{{ $l->event }}</span></td><td>{{ class_basename($l->subject_type) }} #{{ $l->subject_id }}</td>
            <td>{{ Str::limit($l->description,60) }}</td>
            <td><a href="{{ route('admin.activity-logs.show',$l->id) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a></td></tr>
          @empty <tr><td colspan="6" class="text-center">Belum ada log</td></tr> @endforelse
        </tbody>
      </table>
      {{ $logs->links() }}
    </div>
  </div>
</div>
@endsection
