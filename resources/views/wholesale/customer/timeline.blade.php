@php
$steps = [
    ['key' => 'pending',     'label' => 'Baru',    'icon' => 'fa-file-invoice', 'ts' => $order->created_at],
    ['key' => 'reviewed',    'label' => 'Dikonfir','icon' => 'fa-check-circle',  'ts' => $order->confirmed_at ?? null],
    ['key' => 'on_progress', 'label' => 'Proses',  'icon' => 'fa-cogs',          'ts' => null],
    ['key' => 'packed',      'label' => 'Packing', 'icon' => 'fa-box',           'ts' => $order->packed_at ?? null],
    ['key' => 'shipped',     'label' => 'Dikirim', 'icon' => 'fa-truck',         'ts' => $order->shipped_at ?? null],
    ['key' => 'delivered',   'label' => 'Terima',  'icon' => 'fa-handshake',     'ts' => $order->delivered_at ?? null],
];
$sOrder = ['pending','reviewed','on_progress','packed','shipped','delivered','completed','cancelled'];
$ci = array_search($order->status, $sOrder);
$cancel = $order->status === 'cancelled';
$sKeys = collect($steps)->pluck('key');
$isDone = fn($k) => !$cancel && $sKeys->search($k) < $ci;
$isActive = fn($k) => !$cancel && $order->status === $k;
$sClass = fn($k) => $cancel ? 'cancelled' : ($isActive($k) ? 'active' : ($isDone($k) ? 'done' : 'pending'));
@endphp
<div class="stepper">
    @foreach($steps as $i => $s)
        @php $sc = $sClass($s['key']); @endphp
        <div class="step {{ (in_array($sc,['done','active'])) ? $sc : '' }}">
            <div class="step-icon {{ $sc }}"><i class="fas {{ $s['icon'] }}"></i></div>
            <div class="step-label {{ $sc }}">{{ $s['label'] }}</div>
        </div>
    @endforeach
    @if($cancel)
        <div class="step cancelled">
            <div class="step-icon cancelled"><i class="fas fa-times"></i></div>
            <div class="step-label cancelled">Batal</div>
        </div>
    @elseif($order->status === 'completed')
        <div class="step done">
            <div class="step-icon done"><i class="fas fa-check-double"></i></div>
            <div class="step-label done">Selesai</div>
        </div>
    @endif
</div>
