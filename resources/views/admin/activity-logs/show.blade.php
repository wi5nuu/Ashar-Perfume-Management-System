@extends('layouts.app')
@section('content')
<div class="container-fluid">
  <h1 class="h3 mb-4 text-gray-800">Detail Log</h1>
  <div class="card shadow"><div class="card-body">
    <dl class="row">
      <dt class="col-sm-3">Waktu</dt><dd class="col-sm-9">{{ $log->created_at->format('d/m/Y H:i:s') }}</dd>
      <dt class="col-sm-3">User</dt><dd class="col-sm-9">{{ $log->causer?->name??'System' }}</dd>
      <dt class="col-sm-3">Event</dt><dd class="col-sm-9"><span class="badge badge-info">{{ $log->event }}</span></dd>
      <dt class="col-sm-3">Subject</dt><dd class="col-sm-9">{{ $log->subject_type }} #{{ $log->subject_id }}</dd>
      <dt class="col-sm-3">Deskripsi</dt><dd class="col-sm-9">{{ $log->description }}</dd>
      @if($log->properties->count())
      <dt class="col-sm-3">Properties</dt><dd class="col-sm-9"><pre>{{ json_encode($log->properties,JSON_PRETTY_PRINT) }}</pre></dd>
      @endif
    </dl>
    <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-secondary">Kembali</a>
  </div></div>
</div>
@endsection
