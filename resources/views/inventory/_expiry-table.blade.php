<div class="table-responsive">
    <table class="table table-sm table-hover mb-0">
        <thead class="thead-light">
            <tr>
                <th>Produk</th>
                <th>Cabang</th>
                <th>Batch</th>
                <th>Stok</th>
                <th>Tgl Kadaluarsa</th>
                <th class="text-center">Sisa Hari</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
            <tr>
                <td>{{ $item->product->name ?? '-' }}</td>
                <td>{{ $item->branch->name ?? '-' }}</td>
                <td><code>{{ $item->batch_number ?? '-' }}</code></td>
                <td>{{ $item->current_stock }}</td>
                <td>{{ $item->expiration_date ? $item->expiration_date->format('d/m/Y') : '-' }}</td>
                <td class="text-center">
                    @if($item->days_left < 0)
                        <span class="badge badge-danger">Kadaluarsa {{ abs($item->days_left) }} hari lalu</span>
                    @else
                        <span class="badge badge-{{ $color }}">{{ $item->days_left }} hari</span>
                    @endif
                </td>
                <td>
                    @if($item->days_left < 0)
                        <span class="badge badge-dark">EXPIRED</span>
                    @elseif($item->expiry_status === 'critical')
                        <span class="badge badge-danger">CRITICAL</span>
                    @else
                        <span class="badge badge-{{ $color }}">{{ strtoupper($item->expiry_status) }}</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center text-muted">Tidak ada produk di kategori ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
