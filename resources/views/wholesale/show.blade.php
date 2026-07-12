@extends('layouts.app')
@section('title', 'Detail Pesanan Grosir')

@section('content')
<style>
    .status-timeline-modern { display: flex; gap: 0; border-radius: 10px; overflow: hidden; box-shadow: 0 1px 6px rgba(0,0,0,0.06); }
    .status-timeline-modern .step { flex: 1; text-align: center; padding: 12px 6px; font-size: 0.72rem; font-weight: 500; min-width: 70px; position: relative; transition: all 0.2s; }
    .status-timeline-modern .step.done { background: #e8f5e9; color: #2e7d32; }
    .status-timeline-modern .step.done i { color: #43a047; }
    .status-timeline-modern .step.active { background: #fff3e0; color: #e65100; }
    .status-timeline-modern .step.active i { color: #ff6d00; }
    .status-timeline-modern .step.active::after { content: ''; position: absolute; bottom: 0; left: 20%; right: 20%; height: 3px; background: #ff6d00; border-radius: 3px 3px 0 0; }
    .status-timeline-modern .step.pending { background: #f5f5f5; color: #9e9e9e; }
    .status-timeline-modern .step.cancelled { background: #ffebee; color: #c62828; }
    .status-timeline-modern .step.cancelled i { color: #e53935; }
    .status-timeline-modern .step i { display: block; font-size: 1.3rem; margin-bottom: 4px; }
    @media (max-width: 575.98px) { .status-timeline-modern .step { font-size: 0.6rem; padding: 10px 4px; min-width: 54px; } .status-timeline-modern .step i { font-size: 1rem; } }
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
    .info-grid .item { padding: 8px 0; border-bottom: 1px solid #f0f0f0; }
    .info-grid .item .label { font-size: 0.68rem; color: #888; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 2px; }
    .info-grid .item .value { font-size: 0.88rem; color: #222; font-weight: 500; }
    .info-grid .item.full { grid-column: 1 / -1; }
    .terms-list { padding: 0; margin: 0; list-style: none; }
    .terms-list li { padding: 6px 0; border-bottom: 1px solid #f5f5f5; font-size: 0.8rem; color: #444; display: flex; gap: 8px; align-items: flex-start; }
    .terms-list li:last-child { border-bottom: none; }
    .terms-list li::before { content: '✓'; font-weight: 700; color: #28a745; }
    .btn-action-flow { font-size: 0.82rem; padding: 0.55rem 1.2rem; font-weight: 600; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.06); }
    .timestamp-row { display: flex; flex-wrap: wrap; gap: 10px; }
    .timestamp-row .ts-item { background: #f8f9fa; border-radius: 6px; padding: 6px 10px; font-size: 0.72rem; display: flex; align-items: center; gap: 5px; }
    .section-title-sm { font-size: 0.82rem; font-weight: 700; color: #555; text-transform: uppercase; letter-spacing: 0.5px; }
</style>

<div class="container-fluid px-3 px-md-4">
    {{-- Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
        <div>
            <a href="{{ route('wholesale.index') }}" class="btn btn-sm btn-outline-secondary mr-2"><i class="fas fa-arrow-left"></i></a>
            <span class="font-weight-bold" style="font-size:1.15rem;color:#333">{{ $order->invoice_number }}</span>
            @php
                $badgeMap = ['pending'=>'warning','reviewed'=>'primary','on_progress'=>'info','packed'=>'dark','shipped'=>'secondary','delivered'=>'success','completed'=>'success','cancelled'=>'danger'];
                $labelMap = ['pending'=>'Pending','reviewed'=>'Ditinjau','on_progress'=>'Diproses','packed'=>'Dibungkus','shipped'=>'Dikirim','delivered'=>'Diterima','completed'=>'Selesai','cancelled'=>'Dibatalkan'];
            @endphp
            <span class="badge badge-{{ $badgeMap[$order->status] ?? 'secondary' }} ml-2 px-3 py-1" style="font-size:0.75rem">{{ $labelMap[$order->status] ?? strtoupper($order->status) }}</span>
        </div>
        <div class="mt-2 mt-md-0">
            <a href="{{ $whatsappUrl }}" target="_blank" class="btn btn-sm btn-success mr-1"><i class="fab fa-whatsapp mr-1"></i> WhatsApp</a>
            <a href="{{ route('wholesale.print', $order->id) }}" target="_blank" class="btn btn-sm btn-outline-dark"><i class="fas fa-print mr-1"></i> Cetak</a>
        </div>
    </div>

    {{-- Timeline --}}
    @php
        $statusFlow = ['pending','reviewed','on_progress','packed','shipped','delivered','completed'];
        $flowIndex = array_search($order->status, $statusFlow);
        $flowLabels = ['pending'=>'PENDING','reviewed'=>'DITINJAU','on_progress'=>'DIPROSES','packed'=>'DIKEMAS','shipped'=>'DIKIRIM','delivered'=>'DITERIMA','completed'=>'SELESAI'];
        $flowIcons = ['pending'=>'fa-clock','reviewed'=>'fa-check-double','on_progress'=>'fa-spinner','packed'=>'fa-box','shipped'=>'fa-truck','delivered'=>'fa-handshake','completed'=>'fa-check-circle'];
    @endphp
    <div class="status-timeline-modern mb-4">
        @foreach($statusFlow as $i => $step)
            @php
                $cls = 'pending';
                if ($order->status === 'cancelled') {
                    $cls = $i <= $flowIndex ? 'cancelled' : 'pending';
                } elseif ($flowIndex !== false && $i <= $flowIndex) {
                    $cls = ($i === $flowIndex) ? 'active' : 'done';
                }
            @endphp
            <div class="step {{ $cls }}">
                <i class="fas {{ $flowIcons[$step] }}"></i>
                {{ $flowLabels[$step] }}
            </div>
        @endforeach
    </div>

    <div class="row">
        {{-- LEFT: Order Items --}}
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius:10px;overflow:hidden">
                <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center" style="border-bottom:1px solid #f0f0f0">
                    <span class="font-weight-bold" style="font-size:0.95rem;color:#333"><i class="fas fa-shopping-cart mr-2 text-primary"></i> Item Pesanan</span>
                    <span class="text-muted" style="font-size:0.78rem">{{ $order->details->count() }} item</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0" style="font-size:0.85rem">
                            <thead class="text-muted" style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.3px;background:#fafafa">
                                <tr><th class="pl-4 py-2">Produk</th><th class="py-2 text-center">Qty</th><th class="py-2 text-center">Volume</th><th class="py-2 text-right">Harga</th><th class="pr-4 py-2 text-right">Subtotal</th></tr>
                            </thead>
                            <tbody>
                                @foreach($order->details as $detail)
                                <tr style="border-bottom:1px solid #f8f8f8">
                                    <td class="pl-4 py-2">
                                        <span class="font-weight-500">{{ $detail->product_name }}</span>
                                        @if($detail->unit && $detail->unit !== 'pcs')
                                            <br><small class="text-muted">{{ $detail->unit }}</small>
                                        @endif
                                    </td>
                                    <td class="py-2 text-center font-weight-500">{{ $detail->quantity }}</td>
                                    <td class="py-2 text-center text-muted">{{ $detail->volume_ml ? $detail->volume_ml . ' ml' : '-' }}</td>
                                    <td class="py-2 text-right">
                                        Rp {{ number_format($detail->price, 0, ',', '.') }}
                                        @if($detail->price_per_ml)
                                            <br><small class="text-muted">{{ number_format($detail->price_per_ml, 0) }}/ml</small>
                                        @endif
                                    </td>
                                    <td class="pr-4 py-2 text-right font-weight-bold">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white px-4 py-3" style="border-top:1px solid #f0f0f0">
                    <div class="row">
                        <div class="col-6">
                            @if($order->package_target_amount > 0)
                            <small class="text-muted">Target Paket: <strong class="text-dark">Rp {{ number_format($order->package_target_amount, 0, ',', '.') }}</strong></small>
                            @endif
                        </div>
                        <div class="col-6 text-right">
                            <div><span class="text-muted" style="font-size:0.8rem">Total Pesanan</span></div>
                            <div class="font-weight-bold" style="font-size:1.2rem;color:#e65100">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
                            @if($order->shipping_cost > 0)
                                <small class="text-muted">+ Ongkir Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT: Info + Terms + Workflow --}}
        <div class="col-lg-4 mb-4">
            {{-- Shipping Info --}}
            <div class="card border-0 shadow-sm mb-3" style="border-radius:10px;overflow:hidden">
                <div class="card-header bg-white py-2 px-3" style="border-bottom:1px solid #f0f0f0">
                    <span class="section-title-sm"><i class="fas fa-truck mr-1 text-info"></i> Informasi Pengiriman</span>
                </div>
                <div class="card-body px-3 py-2">
                    <div class="info-grid">
                        <div class="item full"><div class="label">Penerima</div><div class="value">{{ $order->recipient_name }}</div></div>
                        <div class="item"><div class="label">Telepon</div><div class="value">{{ $order->recipient_phone }}</div></div>
                        <div class="item"><div class="label">Kurir</div><div class="value">{{ $order->shipping_courier ?? '-' }}</div></div>
                        <div class="item full"><div class="label">Alamat</div><div class="value" style="font-size:0.82rem">{{ $order->shipping_address }}</div></div>
                        <div class="item"><div class="label">Biaya Kirim</div><div class="value">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</div></div>
                        <div class="item"><div class="label">Estimasi Packing</div><div class="value">{{ $order->packing_days ?? 1 }} Hari</div></div>
                        @if($order->customer)
                        <div class="item"><div class="label">Pelanggan</div><div class="value">{{ $order->customer?->name ?? '-' }}</div></div>
                        @endif
                        <div class="item"><div class="label">P. Jawab</div><div class="value">{{ $order->handler->name ?? $order->delivery_handler ?? '-' }}</div></div>
                        @if($order->tracking_number)
                        <div class="item full"><div class="label">No. Resi</div><div class="value"><span class="text-primary font-weight-bold" style="font-size:0.95rem">{{ $order->tracking_number }}</span></div></div>
                        @endif
                        @if($order->notes)
                        <div class="item full"><div class="label">Catatan</div><div class="value" style="font-size:0.82rem">{{ $order->notes }}</div></div>
                        @endif
                        <div class="item"><div class="label">Dibuat Oleh</div><div class="value">{{ $order->user->name ?? 'System' }}</div></div>
                        <div class="item"><div class="label">Tanggal</div><div class="value">{{ $order->created_at->format('d/m/Y H:i') }}</div></div>
                    </div>
                    @if($order->barcode || $order->tracking_number)
                    <div class="mt-2 pt-2 d-flex justify-content-center align-items-center" style="gap:1rem;border-top:1px solid #f0f0f0">
                        @if($order->barcode)
                        <div class="text-center"><i class="fas fa-barcode fa-lg text-muted"></i><div class="small font-weight-bold mt-1">{{ $order->barcode }}</div></div>
                        @endif
                        @if($order->tracking_number)
                        <div class="text-center">
                            <div id="wholesaleShowQr" style="display:inline-block"></div>
                            <div class="small text-muted mt-1">Scan lacak</div>
                        </div>
                        @php $qrUrl = url('/wholesale-customer/track?invoice_number=' . $order->invoice_number); @endphp
                        <script>new QRCode(document.getElementById('wholesaleShowQr'),{text:'{{ $qrUrl }}',width:70,height:70});</script>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            {{-- Terms --}}
            <div class="card border-0 shadow-sm mb-3" style="border-radius:10px;overflow:hidden">
                <div class="card-header bg-white py-2 px-3" style="border-bottom:1px solid #f0f0f0">
                    <span class="section-title-sm"><i class="fas fa-file-contract mr-1 text-muted"></i> Ketentuan Grosir</span>
                </div>
                <div class="card-body px-3 py-2">
                    <ul class="terms-list">
                        <li>Pesanan ini <strong>kesepakatan grosir</strong> mengikat secara hukum.</li>
                        <li>Pembayaran sesuai ketentuan sebelum barang dikirim.</li>
                        <li>Resiko kirim ditanggung pembeli setelah diserahkan ke kurir.</li>
                        <li>Barang grosir <strong>tidak dapat ditukar/dikembalikan</strong> kecuali cacat produksi (claim maksimal 1×24 jam).</li>
                        <li>Ketidaksesuaian lapor maksimal 2×24 jam disertai foto/bukti.</li>
                        <li>Pembatalan sebelum diproses, dikenakan biaya administrasi.</li>
                        <li>Pembeli menyetujui seluruh ketentuan AL'ASHAR PARFUM.</li>
                    </ul>
                </div>
            </div>

            {{-- Workflow --}}
            <div class="card border-0 shadow-sm mb-3" style="border-radius:10px;overflow:hidden">
                <div class="card-header bg-white py-2 px-3" style="border-bottom:1px solid #f0f0f0">
                    <span class="section-title-sm"><i class="fas fa-tasks mr-1 text-primary"></i> Alur Kerja</span>
                </div>
                <div class="card-body px-3 py-2">
                    @if($order->status == 'pending')
                        <div class="alert alert-warning py-2 px-3 mb-2" style="font-size:0.8rem;border-radius:6px"><i class="fas fa-clock mr-1"></i> Menunggu review admin untuk dikonfirmasi.</div>
                        <button type="button" class="btn btn-primary-apms btn-action-flow w-100 mb-2" data-toggle="modal" data-target="#confirmModal">
                            <i class="fas fa-check-double mr-2"></i> KONFIRMASI PESANAN
                        </button>
                        @can('wholesale.manage')
                        <a href="{{ route('wholesale.edit', $order->id) }}" class="btn btn-outline-warning btn-sm w-100 mb-2"><i class="fas fa-edit mr-1"></i> Edit Pesanan</a>
                        @endcan
                        <form action="{{ route('wholesale.cancel', $order->id) }}" method="POST">
                            @csrf
                            <div class="input-group input-group-sm">
                                <input type="text" name="cancellation_reason" class="form-control" placeholder="Alasan batal" required>
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Batalkan pesanan?')"><i class="fas fa-times"></i></button>
                                </div>
                            </div>
                        </form>

                    @elseif($order->status == 'reviewed')
                        <div class="alert alert-primary py-2 px-3 mb-2" style="font-size:0.8rem;border-radius:6px"><i class="fas fa-check-circle mr-1"></i> Pesanan sudah dikonfirmasi. Proses sekarang?</div>
                        <form action="{{ route('wholesale.process', $order->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-info btn-action-flow w-100 text-white"><i class="fas fa-play mr-2"></i> MULAI PROSES</button>
                        </form>

                    @elseif($order->status == 'on_progress')
                        <div class="alert alert-info py-2 px-3 mb-2" style="font-size:0.8rem;border-radius:6px"><i class="fas fa-spinner mr-1"></i> Pesanan sedang dikerjakan.</div>
                        <form action="{{ route('wholesale.pack', $order->id) }}" method="POST">
                            @csrf
                            <div class="form-group mb-2">
                                <label style="font-size:0.75rem;color:#888">Penanggung Jawab Packing</label>
                                <select name="handler_id" class="form-control form-control-sm">
                                    <option value="">Pilih</option>
                                    @foreach($handlers as $h)
                                    <option value="{{ $h->id }}" {{ $order->handler_id == $h->id ? 'selected' : '' }}>{{ $h->name }} ({{ $h->role }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-dark btn-action-flow w-100"><i class="fas fa-box mr-2"></i> SELESAI PACKING</button>
                        </form>

                    @elseif($order->status == 'packed')
                        <div class="alert alert-dark py-2 px-3 mb-2" style="font-size:0.8rem;border-radius:6px"><i class="fas fa-box mr-1"></i> Barang sudah di-packing. Kirim sekarang?</div>
                        <form action="{{ route('wholesale.ship', $order->id) }}" method="POST">
                            @csrf
                            <div class="form-group mb-2">
                                <label style="font-size:0.75rem;color:#888">Kurir</label>
                                <input type="text" name="shipping_courier" class="form-control form-control-sm" value="{{ $order->shipping_courier }}" placeholder="J&T, Sicepat, dll">
                            </div>
                            <div class="form-group mb-2">
                                <label style="font-size:0.75rem;color:#888">Biaya Kirim (Rp)</label>
                                <input type="number" name="shipping_cost" class="form-control form-control-sm" value="{{ $order->shipping_cost }}" min="0">
                            </div>
                            <div class="form-group mb-2">
                                <label style="font-size:0.75rem;color:#888">No. Resi Pengiriman</label>
                                <input type="text" name="tracking_number" class="form-control form-control-sm" value="{{ $order->tracking_number }}" placeholder="JP0000123456">
                                <small class="text-muted" style="font-size:0.65rem">Nomor resi dari kurir</small>
                            </div>
                            <button type="submit" class="btn btn-secondary btn-action-flow w-100"><i class="fas fa-truck mr-2"></i> TANDAI DIKIRIM</button>
                        </form>

                    @elseif($order->status == 'shipped')
                        <div class="alert alert-secondary py-2 px-3 mb-2" style="font-size:0.8rem;border-radius:6px">
                            <i class="fas fa-truck mr-1"></i> Pesanan dalam perjalanan.
                            @if($order->shipping_courier) <br><small>Kurir: {{ $order->shipping_courier }}</small>@endif
                        </div>
                        <form action="{{ route('wholesale.deliver', $order->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success btn-action-flow w-100"><i class="fas fa-handshake mr-2"></i> TANDAI DITERIMA</button>
                        </form>

                    @elseif($order->status == 'delivered')
                        <div class="alert alert-success py-2 px-3 mb-2" style="font-size:0.8rem;border-radius:6px"><i class="fas fa-check-circle mr-1"></i> Pesanan sudah diterima pelanggan.</div>
                        <form action="{{ route('wholesale.complete', $order->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success btn-action-flow w-100"><i class="fas fa-check-double mr-2"></i> SELESAIKAN PESANAN</button>
                        </form>

                    @elseif($order->status == 'completed')
                        <div class="text-center py-3">
                            <i class="fas fa-check-circle" style="font-size:3rem;color:#28a745"></i>
                            <h5 class="font-weight-bold mt-2">Pesanan Selesai</h5>
                            <p class="text-muted mb-0" style="font-size:0.82rem">Selesai: {{ $order->completed_at ? $order->completed_at->format('d/m/Y H:i') : '-' }}</p>
                        </div>

                    @elseif($order->status == 'cancelled')
                        <div class="text-center py-3">
                            <i class="fas fa-times-circle" style="font-size:3rem;color:#dc3545"></i>
                            <h5 class="font-weight-bold mt-2">Pesanan Dibatalkan</h5>
                            @if($order->cancellation_reason)
                            <p class="text-muted mb-0" style="font-size:0.82rem">Alasan: {{ $order->cancellation_reason }}</p>
                            @endif
                        </div>
                    @endif

                    {{-- Timestamps --}}
                    <hr class="my-2">
                    <div class="section-title-sm mb-2" style="font-size:0.72rem"><i class="fas fa-history mr-1"></i> Riwayat Status</div>
                    <div class="timestamp-row">
                        @if($order->confirmed_at)<span class="ts-item"><i class="fas fa-check text-primary"></i> Konfirmasi {{ $order->confirmed_at->format('d/m H:i') }}</span>@endif
                        @if($order->reviewed_at)<span class="ts-item"><i class="fas fa-check text-info"></i> Review {{ $order->reviewed_at->format('d/m H:i') }}</span>@endif
                        @if($order->packed_at)<span class="ts-item"><i class="fas fa-box text-dark"></i> Packing {{ $order->packed_at->format('d/m H:i') }}</span>@endif
                        @if($order->shipped_at)<span class="ts-item"><i class="fas fa-truck text-secondary"></i> Kirim {{ $order->shipped_at->format('d/m H:i') }}</span>@endif
                        @if($order->delivered_at)<span class="ts-item"><i class="fas fa-handshake text-success"></i> Terima {{ $order->delivered_at->format('d/m H:i') }}</span>@endif
                        @if($order->completed_at)<span class="ts-item"><i class="fas fa-check-circle text-success"></i> Selesai {{ $order->completed_at->format('d/m H:i') }}</span>@endif
                        @if($order->cancelled_at)<span class="ts-item"><i class="fas fa-times-circle text-danger"></i> Batal {{ $order->cancelled_at->format('d/m H:i') }}</span>@endif
                    </div>

                    {{-- Cancel for active orders --}}
                    @if(in_array($order->status, ['reviewed', 'on_progress', 'packed']))
                    <hr class="my-2">
                    <form action="{{ route('wholesale.cancel', $order->id) }}" method="POST">
                        @csrf
                        <div class="input-group input-group-sm">
                            <input type="text" name="cancellation_reason" class="form-control" placeholder="Alasan batal" required>
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Yakin batalkan pesanan?')"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Confirm Modal --}}
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('wholesale.confirm', $order->id) }}" method="POST" class="modal-content" style="border-radius:12px;border:none">
            @csrf
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-check-double text-primary mr-2"></i>Konfirmasi Pesanan</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body text-center py-3">
                <div class="mb-2"><i class="fas fa-exclamation-triangle text-warning" style="font-size:2.2rem"></i></div>
                <p class="mb-1 font-weight-bold" style="font-size:1rem">Konfirmasi pesanan ini?</p>
                <p class="mb-0 text-muted" style="font-size:0.85rem">Stok gudang akan terpotong sesuai jumlah pesanan.</p>
            </div>
            <div class="modal-footer border-0 justify-content-center pt-0">
                <button type="button" class="btn btn-outline-secondary px-3" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary-apms px-4"><i class="fas fa-check mr-1"></i> Ya, Konfirmasi</button>
            </div>
        </form>
    </div>
</div>
@endsection
