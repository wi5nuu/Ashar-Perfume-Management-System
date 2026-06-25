<div class="d-flex justify-content-between align-items-center mb-3">
    <div><h3 class="font-weight-bold text-dark mb-0"><i class="fas fa-boxes text-primary mr-2"></i>Permintaan Stok</h3></div>
    <div>
        @can('stock_requests.create')
        <a href="{{ route('stock-requests.create') }}" class="btn btn-primary-apms"><i class="fas fa-plus mr-1"></i>Ajukan</a>
        @endcan
        <a href="{{ route('stock-requests.index') }}" class="btn btn-outline-secondary ml-1"><i class="fas fa-list mr-1"></i>Semua</a>
    </div>
</div>
