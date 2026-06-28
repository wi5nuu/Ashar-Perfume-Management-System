@php $title = 'AI Business Dashboard'; @endphp
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- INSIGHT BANNER --}}
    @if(!empty($insights))
    <div class="row mb-3">
        <div class="col-12">
            <div class="card card-apms border-0 shadow-sm">
                <div class="card-body py-2 px-3">
                    <small class="text-muted font-weight-bold text-uppercase" style="font-size:0.6rem;"><i class="fas fa-brain mr-1"></i> INSIGHT OTOMATIS</small>
                    <div class="mt-1">
                        @foreach($insights as $ins)
                        <div class="d-flex align-items-start mb-1">
                            <span class="badge badge-{{ ($ins['type'] ?? 'info') === 'positive' ? 'success' : (($ins['type'] ?? 'info') === 'warning' ? 'warning' : 'danger') }} p-1 mr-2 mt-1" style="font-size:8px;"><i class="fas {{ $ins['icon'] ?? 'fa-circle' }}"></i></span>
                            <small style="font-size:0.7rem;">{{ $ins['text'] ?? '' }}</small>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- TODAY'S STATS --}}
    <div class="row mb-3">
        @php $stats = [
            ['label'=>'Pendapatan Hari Ini','value'=>'Rp '.number_format($todayStats['revenue'],0,',','.'),'icon'=>'fa-money-bill-wave','color'=>'primary','change'=>$todayStats['revenueChange']],
            ['label'=>'Transaksi','value'=>$todayStats['transactions'],'icon'=>'fa-shopping-cart','color'=>'success','change'=>$todayStats['transChange']],
            ['label'=>'Biaya Hari Ini','value'=>'Rp '.number_format($todayStats['today_expenses'],0,',','.'),'icon'=>'fa-coins','color'=>'info'],
            ['label'=>'Stok Kritis/Habis','value'=>$todayStats['low_stock'].' / '.$todayStats['out_of_stock'],'icon'=>'fa-exclamation-triangle','color'=>'warning'],
            ['label'=>'Pesanan Grosir Baru','value'=>$todayStats['pending_orders'],'icon'=>'fa-boxes-packing','color'=>'secondary'],
            ['label'=>'Pelanggan Baru','value'=>$todayStats['customers'],'icon'=>'fa-users','color'=>'dark'],
        ]; @endphp
        @foreach($stats as $s)
        <div class="col-lg-2 col-4 mb-2">
            <div class="info-box bg-gradient-{{ $s['color'] }} shadow-sm mb-0">
                <span class="info-box-icon"><i class="fas {{ $s['icon'] }}"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text" style="font-size:0.6rem;">{{ $s['label'] }}</span>
                    <span class="info-box-number mb-0" style="font-size:0.85rem;">{{ $s['value'] }}</span>
                    @if(isset($s['change']))
                    <small class="text-{{ $s['change'] >= 0 ? 'white' : 'white' }}" style="opacity:0.8;font-size:0.6rem;">
                        <i class="fas fa-{{ $s['change'] >= 0 ? 'arrow-up' : 'arrow-down' }}"></i> {{ abs($s['change']) }}%
                    </small>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ROW 1: MAIN KPIs --}}
    <div class="row">
        {{-- HEALTH + FORECAST + TREND --}}
        <div class="col-lg-4 mb-3">
            <div class="card card-apms mb-3">
                <div class="card-header py-2">
                    <h3 class="card-title" style="font-size:0.85rem;"><i class="fas fa-heartbeat mr-1 text-{{ $health['gradeClass'] }}"></i> Skor Kesehatan</h3>
                    <span class="badge badge-{{ $health['gradeClass'] }} float-right" style="font-size:0.65rem;">{{ $health['grade'] }}</span>
                </div>
                <div class="card-body text-center py-2">
                    <div class="position-relative d-inline-block mb-2">
                        <canvas id="healthGauge" width="140" height="140" data-score="{{ $health['totalScore'] }}"></canvas>
                        <div class="position-absolute" style="top:50%;left:50%;transform:translate(-50%,-50%);">
                            <h2 class="font-weight-bold mb-0" style="font-size:1.6rem;color:{{ $health['totalScore']>=85?'#28a745':($health['totalScore']>=70?'#007bff':($health['totalScore']>=50?'#ffc107':'#dc3545')) }};">{{ $health['totalScore'] }}</h2>
                        </div>
                    </div>
                    <div class="text-left small" style="font-size:0.6rem;">
                        @php $pills = [['Profitabilitas',$health['profitScore'],30,'success'],['Stok',$health['stockScore'],25,'info'],['Pertumbuhan',$health['growthScore'],20,'primary'],['Likuiditas',$health['liquidityScore'],15,'warning'],['Efisiensi',$health['efficiencyScore'],10,'secondary']]; @endphp
                        @foreach($pills as $p)
                        <div class="mb-1">
                            <div class="d-flex justify-content-between"><span>{{ $p[0] }}</span><span>{{ round($p[1]) }}/{{ $p[2] }}</span></div>
                            <div class="progress" style="height:3px;"><div class="progress-bar bg-{{ $p[3] }}" style="width:{{ $p[2]>0?($p[1]/$p[2])*100:0 }}%"></div></div>
                        </div>
                        @endforeach
                    </div>
                    <hr class="my-1">
                    <div class="small" style="font-size:0.6rem;">
                        <div class="row">
                            <div class="col-6">Margin: <strong>{{ round($health['margin'],1) }}%</strong></div>
                            <div class="col-6">Laba: <strong class="text-{{ $health['netProfit']>=0?'success':'danger' }}">Rp {{ number_format($health['netProfit'],0,',','.') }}</strong></div>
                            <div class="col-6">Trans/hari: <strong>{{ round($health['avgDaily'],1) }}</strong></div>
                            <div class="col-6">Kadaluarsa: <strong class="text-danger">{{ $health['nearExpiry'] ?? 0 }}</strong></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-apms mb-3">
                <div class="card-header py-2"><h3 class="card-title" style="font-size:0.85rem;"><i class="fas fa-chart-line mr-1 text-info"></i> Proyeksi Pendapatan</h3></div>
                <div class="card-body py-2">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div><span class="text-muted" style="font-size:0.6rem;">Akhir Bulan</span><h5 class="mb-0 font-weight-bold" style="font-size:1rem;">Rp {{ number_format($forecast['projectedRevenue'],0,',','.') }}</h5></div>
                        <span class="badge badge-{{ $forecast['statusClass'] }} p-2" style="font-size:0.65rem;">{{ $forecast['statusLabel'] }}</span>
                    </div>
                    <div style="font-size:0.6rem;">
                        <div class="d-flex justify-content-between"><span class="text-muted">Saat ini (hari {{ $forecast['daysElapsed'] }}/{{ $forecast['daysInMonth'] }})</span><strong>Rp {{ number_format($forecast['currentRevenue'],0,',','.') }}</strong></div>
                        <div class="d-flex justify-content-between"><span class="text-muted">Rata-rata/hari</span><strong>Rp {{ number_format($forecast['dailyAverage'],0,',','.') }}</strong></div>
                        <div class="d-flex justify-content-between"><span class="text-muted">Bulan lalu</span><strong>Rp {{ number_format($forecast['lastMonthRevenue'],0,',','.') }}</strong></div>
                        <div class="progress mt-1" style="height:5px;">@php $fpct = $forecast['lastMonthRevenue']>0?min(100,($forecast['currentRevenue']/$forecast['lastMonthRevenue'])*100):0; @endphp<div class="progress-bar bg-info" style="width:{{ $fpct }}%"></div></div>
                        @if($forecast['daysRemaining']>0&&$forecast['paceNeeded']>0)
                        <div class="mt-1"><span class="text-muted">Pace dibutuhkan: </span><strong class="text-warning">Rp {{ number_format($forecast['paceNeeded'],0,',','.') }}/hari</strong></div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card card-apms mb-3">
                <div class="card-header py-2"><h3 class="card-title" style="font-size:0.85rem;"><i class="fas fa-chart-bar mr-1 text-success"></i> Tren 5 Bulan</h3></div>
                <div class="card-body py-2">
                    @php $mxRev = max(array_column($trends['months'],'revenue')); @endphp
                    <div class="d-flex align-items-end justify-content-around" style="height:80px;">
                        @foreach($trends['months'] as $m)
                        @php $h = $mxRev>0?($m['revenue']/$mxRev)*100:0;$ch=$loop->index>0?($trends['revenueChange'][$loop->index-1]??0):0; @endphp
                        <div class="text-center" style="flex:1;">
                            <small class="text-{{ $ch>=0?'success':'danger' }}" style="font-size:0.5rem;"><i class="fas fa-{{ $ch>=0?'caret-up':'caret-down' }}"></i></small>
                            <div class="mx-1" style="height:{{ $h }}%;background:{{ $m['revenue']>=($trends['months'][max(0,$loop->index-1)]['revenue']??0)?'#28a745':'#dc3545' }};border-radius:3px 3px 0 0;min-height:{{ $h>0?3:0 }}px;opacity:0.7;"></div>
                            <small style="font-size:0.5rem;">{{ substr($m['label'],0,3) }}</small>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-1 text-center" style="font-size:0.6rem;">
                        @php $lst=end($trends['months']);$fst=reset($trends['months']);$oc=$fst['revenue']>0?round((($lst['revenue']-$fst['revenue'])/$fst['revenue'])*100,1):0; @endphp
                        <span class="text-muted">5 bln: </span><strong class="text-{{ $oc>=0?'success':'danger' }}">{{ $oc>=0?'+':'' }}{{ $oc }}%</strong>
                        <span class="text-muted ml-1">Tren: </span><strong>{{ $trends['revenueTrend'] }}</strong>
                    </div>
                </div>
            </div>

            {{-- Stock Value --}}
            <div class="card card-apms mb-3">
                <div class="card-header py-2"><h3 class="card-title" style="font-size:0.85rem;"><i class="fas fa-warehouse mr-1 text-secondary"></i> Nilai Gudang</h3></div>
                <div class="card-body py-2">
                    <h5 class="mb-0 font-weight-bold text-primary" style="font-size:1rem;">Rp {{ number_format($stockValue['totalValue'],0,',','.') }}</h5>
                    <div class="small" style="font-size:0.6rem;">
                        <div class="d-flex justify-content-between"><span class="text-muted">Total item:</span><strong>{{ number_format($stockValue['totalItems'],0,',','.') }}</strong></div>
                        <div class="d-flex justify-content-between"><span class="text-muted">Total produk:</span><strong>{{ $stockValue['totalProducts'] }}</strong></div>
                        <div class="d-flex justify-content-between"><span class="text-muted">Rata-rata biaya/unit:</span><strong>Rp {{ number_format($stockValue['avgCost'],0,',','.') }}</strong></div>
                        @if($stockValue['categories']->isNotEmpty())
                        <hr class="my-1">
                        <div class="text-muted mb-1">Per Kategori:</div>
                        @foreach($stockValue['categories']->take(4) as $c)
                        <div class="d-flex justify-content-between"><span>{{ $c->name }}</span><span>Rp {{ number_format($c->total_val,0,',','.') }}</span></div>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ANOMALIES + PEAK HOURS + CATEGORY TRENDS + SUPPLIER --}}
        <div class="col-lg-4 mb-3">
            <div class="card card-apms mb-3">
                <div class="card-header py-2"><h3 class="card-title" style="font-size:0.85rem;"><i class="fas fa-exclamation-triangle mr-1 text-danger"></i> Anomali</h3>@if(count($anomalies)>0)<span class="badge badge-danger ml-1">{{ count($anomalies) }}</span>@endif</div>
                <div class="card-body p-0">
                    @forelse($anomalies as $a)
                    <div class="p-2 border-left border-{{ $a['type'] }} border-left-3 {{ !$loop->last?'border-bottom':'' }}">
                        <div class="d-flex align-items-start">
                            <span class="badge badge-{{ $a['type'] }} p-1 mr-2 mt-1" style="font-size:8px;"><i class="fas {{ $a['icon'] }}"></i></span>
                            <div>
                                <strong style="font-size:0.7rem;">{{ $a['title'] }}</strong>
                                <p class="mb-0 text-muted mt-1" style="font-size:0.6rem;">{{ $a['text'] }}</p>
                                <small class="text-{{ $a['type'] }}" style="font-size:0.55rem;"><i class="fas fa-arrow-right mr-1"></i>{{ $a['action'] }}</small>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-3"><i class="fas fa-check-circle fa-2x mb-1 d-block text-success"></i><h6 style="font-size:0.75rem;">Tidak ada anomali</h6></div>
                    @endforelse
                </div>
            </div>

            <div class="card card-apms mb-3">
                <div class="card-header py-2"><h3 class="card-title" style="font-size:0.85rem;"><i class="fas fa-clock mr-1 text-info"></i> Jam & Hari Sibuk</h3></div>
                <div class="card-body py-2">
                    @if($peakHours['peakHour'])
                    <div class="row mb-2">
                        <div class="col-6 text-center"><div class="text-muted" style="font-size:0.55rem;">Jam Tersibuk</div><h4 class="mb-0 text-info" style="font-size:1.1rem;">{{ $peakHours['peakHour']->hour }}:00</h4><small class="text-muted" style="font-size:0.6rem;">{{ $peakHours['peakHour']->count }} tx</small></div>
                        <div class="col-6 text-center"><div class="text-muted" style="font-size:0.55rem;">Hari Tersibuk</div><h4 class="mb-0 text-info" style="font-size:1.1rem;">{{ $peakHours['dayMap'][$peakHours['peakDay']->day_name]??$peakHours['peakDay']->day_name }}</h4><small class="text-muted" style="font-size:0.6rem;">{{ $peakHours['peakDay']->count }} tx</small></div>
                    </div>
                    @endif
                    @if($peakHours['hourlyData']->isNotEmpty())
                    @php $mc = $peakHours['maxCount']; @endphp
                    <div style="font-size:0.6rem;">@foreach($peakHours['hourlyData'] as $h)<div class="d-flex align-items-center mb-1"><span style="width:22px;font-size:0.55rem;">{{ $h->hour }}</span><div class="flex-grow-1 mx-1"><div class="progress" style="height:4px;"><div class="progress-bar bg-info" style="width:{{ $mc>0?($h->count/$mc)*100:0 }}%"></div></div></div><span style="width:18px;text-align:right;font-size:0.55rem;">{{ $h->count }}</span></div>@endforeach</div>
                    @endif
                </div>
            </div>

            {{-- Category Trends --}}
            @if(!empty($categoryTrends['categories']))
            <div class="card card-apms mb-3">
                <div class="card-header py-2"><h3 class="card-title" style="font-size:0.85rem;"><i class="fas fa-tag mr-1 text-primary"></i> Tren Kategori</h3></div>
                <div class="card-body py-2">
                    @if($categoryTrends['topGainer']&&$categoryTrends['topGainer']['change']>0)
                    <div class="d-flex align-items-center mb-1 p-1 rounded" style="background:#d4edda;font-size:0.6rem;"><span class="badge badge-success p-1 mr-2" style="font-size:7px;"><i class="fas fa-arrow-up"></i></span><strong>{{ $categoryTrends['topGainer']['name'] }}</strong>&nbsp;naik {{ $categoryTrends['topGainer']['change'] }}%</div>
                    @endif
                    @if($categoryTrends['topLoser']&&$categoryTrends['topLoser']['change']<0)
                    <div class="d-flex align-items-center mb-1 p-1 rounded" style="background:#f8d7da;font-size:0.6rem;"><span class="badge badge-danger p-1 mr-2" style="font-size:7px;"><i class="fas fa-arrow-down"></i></span><strong>{{ $categoryTrends['topLoser']['name'] }}</strong>&nbsp;turun {{ abs($categoryTrends['topLoser']['change']) }}%</div>
                    @endif
                    <div style="font-size:0.6rem;">@foreach($categoryTrends['categories'] as $cat)<div class="d-flex justify-content-between align-items-center mb-1"><span>{{ $cat['name'] }}</span><div><span class="text-muted">Rp {{ number_format($cat['revenue'],0,',','.') }}</span>@if($cat['change']!=0)<span class="ml-1 badge badge-{{ $cat['trend']==='up'?'success':($cat['trend']==='down'?'danger':'secondary') }}" style="font-size:7px;">{{ $cat['change']>0?'+':'' }}{{ $cat['change'] }}%</span>@endif</div></div>@endforeach</div>
                </div>
            </div>
            @endif

            {{-- Supplier Summary --}}
            <div class="card card-apms mb-3">
                <div class="card-header py-2"><h3 class="card-title" style="font-size:0.85rem;"><i class="fas fa-truck mr-1 text-secondary"></i> Supplier</h3></div>
                <div class="card-body py-2">
                    <div class="row text-center" style="font-size:0.65rem;">
                        <div class="col-4"><div class="text-muted" style="font-size:0.55rem;">Total</div><strong style="font-size:0.9rem;">{{ $suppliers['total'] }}</strong></div>
                        <div class="col-4"><div class="text-muted" style="font-size:0.55rem;">PO</div><strong style="font-size:0.9rem;">{{ $suppliers['poCount'] }}</strong></div>
                        <div class="col-4"><div class="text-muted" style="font-size:0.55rem;">Rata2 Telat</div><strong style="font-size:0.9rem;" class="text-{{ ($suppliers['avgLeadTime']??0)>0?'danger':'success' }}">{{ $suppliers['avgLeadTime'] ?? 0 }} hari</strong></div>
                    </div>
                    @if(!empty($suppliers['top']))
                    <hr class="my-1">
                    <div style="font-size:0.6rem;">
                        <span class="text-muted">Top: </span><strong>{{ $suppliers['top']->name }}</strong>
                        <span class="text-muted ml-1">{{ $suppliers['top']->po_count }} PO</span>
                        <span class="text-muted ml-1">Rp {{ number_format($suppliers['top']->total_spent,0,',','.') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Promo Summary --}}
            <div class="card card-apms mb-3">
                <div class="card-header py-2"><h3 class="card-title" style="font-size:0.85rem;"><i class="fas fa-tags mr-1 text-warning"></i> Kupon & Promo</h3></div>
                <div class="card-body py-2">
                    <div class="row text-center" style="font-size:0.65rem;">
                        <div class="col-4"><div class="text-muted" style="font-size:0.55rem;">Aktif</div><strong style="font-size:0.9rem;" class="text-success">{{ $promos['active'] ?? 0 }}</strong></div>
                        <div class="col-4"><div class="text-muted" style="font-size:0.55rem;">Total</div><strong style="font-size:0.9rem;">{{ $promos['totalCoupons'] ?? 0 }}</strong></div>
                        <div class="col-4"><div class="text-muted" style="font-size:0.55rem;">Digunakan</div><strong style="font-size:0.9rem;">{{ $promos['totalUsage'] ?? 0 }}</strong></div>
                    </div>
                    @if(!empty($promos['topPromo']))
                    <hr class="my-1">
                    <div style="font-size:0.6rem;"><span class="text-muted">Terpopuler: </span><strong>{{ $promos['topPromo']->code }}</strong> <span class="text-muted">({{ $promos['topPromo']->used_count }}x)</span></div>
                    @endif
                </div>
            </div>
        </div>

        {{-- RECOMMENDATIONS + PRODUCTS + CUSTOMERS + BRANCH + WHOLESALE --}}
        <div class="col-lg-4 mb-3">
            <div class="card card-apms mb-3">
                <div class="card-header py-2"><h3 class="card-title" style="font-size:0.85rem;"><i class="fas fa-lightbulb mr-1 text-warning"></i> Rekomendasi</h3><span class="badge badge-warning float-right" style="font-size:0.65rem;">{{ count($advices) }}</span></div>
                <div class="card-body p-0" style="max-height:250px;overflow-y:auto;">
                    @forelse($advices as $a)
                    <div class="p-2 {{ !$loop->last?'border-bottom':'' }}">
                        <div class="d-flex align-items-start">
                            <span class="badge badge-{{ str_replace('text-','',$a['color']) }} p-1 mr-2 mt-1" style="font-size:8px;"><i class="fas {{ $a['icon'] }}"></i></span>
                            <div><strong class="{{ $a['color'] }}" style="font-size:0.65rem;">{{ $a['title'] }}</strong><div class="text-muted mt-1" style="font-size:0.55rem;">{{ $a['text'] }}</div></div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-3"><i class="fas fa-info-circle fa-2x mb-1 d-block"></i><h6 style="font-size:0.75rem;">Belum ada rekomendasi</h6></div>
                    @endforelse
                </div>
            </div>

            @if($topBottomProducts['top']->isNotEmpty())
            <div class="card card-apms mb-3">
                <div class="card-header py-2"><h3 class="card-title" style="font-size:0.85rem;"><i class="fas fa-crown mr-1 text-warning"></i> Produk Terlaris</h3></div>
                <div class="card-body p-0">
                    @foreach($topBottomProducts['top'] as $p)
                    <div class="p-2 {{ !$loop->last?'border-bottom':'' }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div><strong style="font-size:0.65rem;">{{ $p->name }}</strong><small class="d-block text-muted" style="font-size:0.55rem;">{{ $p->brand }}</small></div>
                            <div class="text-right"><span class="font-weight-bold text-success" style="font-size:0.65rem;">{{ $p->qty }} pcs</span><small class="d-block text-muted" style="font-size:0.55rem;">Rp {{ number_format($p->revenue,0,',','.') }}</small></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Branch Overview --}}
            @if($branches['branchCount']>0)
            <div class="card card-apms mb-3">
                <div class="card-header py-2"><h3 class="card-title" style="font-size:0.85rem;"><i class="fas fa-store mr-1 text-primary"></i> Cabang (Bulan Ini)</h3></div>
                <div class="card-body p-0">
                    @foreach($branches['branchData'] as $b)
                    <div class="p-2 {{ !$loop->last?'border-bottom':'' }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <strong style="font-size:0.65rem;">{{ $b['name'] }}</strong>
                            <div class="text-right">
                                <span class="font-weight-bold" style="font-size:0.65rem;">Rp {{ number_format($b['revenue'],0,',','.') }}</span>
                                <small class="d-block text-muted" style="font-size:0.55rem;">{{ $b['transactions'] }} tx · Laba: <span class="text-{{ $b['profit']>=0?'success':'danger' }}">Rp {{ number_format($b['profit'],0,',','.') }}</span></small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Wholesale Overview --}}
            <div class="card card-apms mb-3">
                <div class="card-header py-2"><h3 class="card-title" style="font-size:0.85rem;"><i class="fas fa-boxes-packing mr-1 text-info"></i> Pesanan Grosir</h3></div>
                <div class="card-body py-2">
                    <div class="row text-center" style="font-size:0.65rem;">
                        <div class="col-4"><div class="text-muted" style="font-size:0.55rem;">Total</div><strong style="font-size:0.9rem;">{{ $wholesale['total'] }}</strong></div>
                        <div class="col-4"><div class="text-muted" style="font-size:0.55rem;">Pending</div><strong class="text-warning" style="font-size:0.9rem;">{{ $wholesale['pendingCount'] }}</strong></div>
                        <div class="col-4"><div class="text-muted" style="font-size:0.55rem;">Revenue</div><strong style="font-size:0.9rem;">Rp {{ number_format($wholesale['totalRevenue']/1000000,1,',','.') }}jt</strong></div>
                    </div>
                    <div style="font-size:0.6rem;" class="mt-1">
                        @php $wStatusLabels = ['pending'=>'Pending','reviewed'=>'Review','on_progress'=>'Proses','packed'=>'Packing','shipped'=>'Kirim','delivered'=>'Terima','completed'=>'Selesai','cancelled'=>'Batal']; @endphp
                        @foreach($wStatusLabels as $k=>$l)
                        @if(($wholesale['counts'][$k]??0)>0)
                        <span class="badge badge-{{ $k==='cancelled'?'danger':($k==='completed'||$k==='delivered'?'success':'secondary') }} mr-1" style="font-size:7px;">{{ $l }}: {{ $wholesale['counts'][$k] }}</span>
                        @endif
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Customer Insights --}}
            <div class="card card-apms mb-3">
                <div class="card-header py-2"><h3 class="card-title" style="font-size:0.85rem;"><i class="fas fa-users mr-1 text-success"></i> Insight Pelanggan</h3></div>
                <div class="card-body py-2">
                    <div class="row text-center" style="font-size:0.6rem;">
                        <div class="col-4 mb-1"><div class="text-muted">Baru</div><strong style="font-size:0.85rem;">{{ $customerInsights['newCustomers'] }}</strong><small class="d-block text-{{ $customerInsights['newCustomerChange']>=0?'success':'danger' }}">{{ $customerInsights['newCustomerChange']>=0?'+':'' }}{{ $customerInsights['newCustomerChange'] }}%</small></div>
                        <div class="col-4 mb-1"><div class="text-muted">Total</div><strong style="font-size:0.85rem;">{{ $customerInsights['totalCustomers'] }}</strong></div>
                        <div class="col-4 mb-1"><div class="text-muted">Aktif</div><strong style="font-size:0.85rem;">{{ $customerInsights['activeBuyers'] }}</strong></div>
                        <div class="col-6"><div class="text-muted">Rata2 Tx</div><strong>Rp {{ number_format($customerInsights['avgTransaction'],0,',','.') }}</strong><small class="d-block text-{{ $customerInsights['avgChange']>=0?'success':'danger' }}">{{ $customerInsights['avgChange']>=0?'+':'' }}{{ $customerInsights['avgChange'] }}%</small></div>
                        <div class="col-6"><div class="text-muted">Repeat</div><strong>{{ $customerInsights['repeatBuyers'] }}</strong></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ROW 2: LOW STOCK + TOP CUSTOMERS + EMPLOYEE + ACTIVITIES --}}
    <div class="row">
        {{-- Low Stock Details --}}
        <div class="col-lg-3 mb-3">
            <div class="card card-apms h-100">
                <div class="card-header py-2"><h3 class="card-title" style="font-size:0.85rem;"><i class="fas fa-exclamation-triangle mr-1 text-danger"></i> Stok Kritis</h3><span class="badge badge-danger float-right" style="font-size:0.65rem;">{{ $lowStockItems['totalCritical'] }}</span></div>
                <div class="card-body p-0">
                    @forelse($lowStockItems['items'] as $i)
                    <div class="p-2 {{ !$loop->last?'border-bottom':'' }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div><strong style="font-size:0.65rem;">{{ $i['name'] }}</strong><small class="d-block text-muted" style="font-size:0.55rem;">Stok: {{ $i['stock'] }}/{{ $i['min'] }} ({{ $i['pct'] }}%)</small></div>
                            <div class="text-right">
                                <span class="badge badge-{{ $i['days_left']<=3?'danger':($i['days_left']<=7?'warning':'secondary') }}" style="font-size:0.6rem;">{{ $i['days_left'] }} hari</span>
                                <small class="d-block text-muted" style="font-size:0.5rem;">{{ $i['daily_sold'] }}/hari</small>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-3"><i class="fas fa-check-circle fa-2x mb-1 d-block text-success"></i><h6 style="font-size:0.7rem;">Tidak ada stok kritis</h6></div>
                    @endforelse
                    <div class="p-2 bg-light"><small class="text-muted" style="font-size:0.6rem;">+ {{ $lowStockItems['totalOut'] }} produk habis total</small></div>
                </div>
            </div>
        </div>

        {{-- Top Customers --}}
        <div class="col-lg-3 mb-3">
            <div class="card card-apms h-100">
                <div class="card-header py-2"><h3 class="card-title" style="font-size:0.85rem;"><i class="fas fa-star mr-1 text-warning"></i> Top Pelanggan</h3></div>
                <div class="card-body p-0">
                    @if($topCustomers['topRetail']->isNotEmpty())
                    <div class="p-1 bg-light border-bottom"><small class="font-weight-bold text-muted" style="font-size:0.6rem;">RETAIL</small></div>
                    @foreach($topCustomers['topRetail'] as $c)
                    <div class="p-2 {{ !$loop->last?'border-bottom':'' }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div><strong style="font-size:0.6rem;">{{ $c->name }}</strong><small class="d-block text-muted" style="font-size:0.5rem;">{{ $c->tx_count }}x transaksi</small></div>
                            <span class="font-weight-bold text-success" style="font-size:0.6rem;">Rp {{ number_format($c->total_spent,0,',','.') }}</span>
                        </div>
                    </div>
                    @endforeach
                    @endif
                    @if($topCustomers['topWholesale']->isNotEmpty())
                    <div class="p-1 bg-light border-bottom"><small class="font-weight-bold text-muted" style="font-size:0.6rem;">GROSIR</small></div>
                    @foreach($topCustomers['topWholesale'] as $c)
                    <div class="p-2 {{ !$loop->last?'border-bottom':'' }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div><strong style="font-size:0.6rem;">{{ $c->name }}</strong><small class="d-block text-muted" style="font-size:0.5rem;">{{ $c->order_count }}x pesanan</small></div>
                            <span class="font-weight-bold text-info" style="font-size:0.6rem;">Rp {{ number_format($c->total_spent,0,',','.') }}</span>
                        </div>
                    </div>
                    @endforeach
                    @endif
                    @if($topCustomers['topRetail']->isEmpty()&&$topCustomers['topWholesale']->isEmpty())
                    <div class="text-center text-muted py-3"><h6 style="font-size:0.7rem;">Belum ada data</h6></div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Employee Today --}}
        <div class="col-lg-3 mb-3">
            <div class="card card-apms h-100">
                <div class="card-header py-2"><h3 class="card-title" style="font-size:0.85rem;"><i class="fas fa-user-clock mr-1 text-info"></i> Karyawan Hari Ini</h3></div>
                <div class="card-body p-0">
                    @if($employeeToday['activeShift'])
                    <div class="p-2 bg-light border-bottom" style="font-size:0.6rem;">
                        <span class="text-muted">Shift: </span><strong>{{ $employeeToday['activeShift']->user?->name ?? '-' }}</strong>
                        @if($employeeToday['activeShift']->start_time)<small class="text-muted d-block">Buka: {{ \Carbon\Carbon::parse($employeeToday['activeShift']->start_time)->format('H:i') }}</small>@endif
                    </div>
                    @else
                    <div class="p-2 bg-light border-bottom"><small class="text-danger" style="font-size:0.6rem;"><i class="fas fa-clock mr-1"></i>Tidak ada shift aktif</small></div>
                    @endif
                    <div class="p-2 border-bottom"><div class="row text-center" style="font-size:0.6rem;"><div class="col-6"><span class="text-muted">Absen hari ini</span><br><strong style="font-size:0.9rem;">{{ $employeeToday['totalToday'] }}</strong></div><div class="col-6"><span class="text-muted">Sedang kerja</span><br><strong class="text-success" style="font-size:0.9rem;">{{ $employeeToday['activeNow'] }}</strong></div></div></div>
                    @forelse($employeeToday['attendances'] as $a)
                    <div class="p-2 {{ !$loop->last?'border-bottom':'' }}" style="font-size:0.6rem;">
                        <div class="d-flex justify-content-between"><strong>{{ $a['name'] }}</strong><span class="text-muted">Masuk: {{ $a['time_in'] }}</span></div>
                        <small class="text-muted">{{ $a['role'] }}</small>
                    </div>
                    @empty
                    <div class="text-center text-muted py-2"><small style="font-size:0.6rem;">Belum ada absensi</small></div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Revenue Composition --}}
        <div class="col-lg-3 mb-3">
            <div class="card card-apms h-100">
                <div class="card-header py-2"><h3 class="card-title" style="font-size:0.85rem;"><i class="fas fa-chart-pie mr-1 text-success"></i> Komposisi Pendapatan</h3></div>
                <div class="card-body py-2">
                    <div class="row text-center mb-2" style="font-size:0.6rem;">
                        <div class="col-4"><div class="text-muted">Tunai</div><strong class="text-success" style="font-size:0.8rem;">Rp {{ number_format($revenueComposition['todayCash'],0,',','.') }}</strong></div>
                        <div class="col-4"><div class="text-muted">Hutang</div><strong class="text-warning" style="font-size:0.8rem;">Rp {{ number_format($revenueComposition['todayDebt'],0,',','.') }}</strong></div>
                        <div class="col-4"><div class="text-muted">Transfer</div><strong class="text-info" style="font-size:0.8rem;">Rp {{ number_format($revenueComposition['todayTransfer'],0,',','.') }}</strong></div>
                    </div>
                    <div class="progress mb-2" style="height:8px;">
                        @php $totPmt = max(1,$revenueComposition['todayCash']+$revenueComposition['todayDebt']+$revenueComposition['todayTransfer']); @endphp
                        <div class="progress-bar bg-success" style="width:{{ ($revenueComposition['todayCash']/$totPmt)*100 }}%"></div>
                        <div class="progress-bar bg-warning" style="width:{{ ($revenueComposition['todayDebt']/$totPmt)*100 }}%"></div>
                        <div class="progress-bar bg-info" style="width:{{ ($revenueComposition['todayTransfer']/$totPmt)*100 }}%"></div>
                    </div>
                    <div style="font-size:0.6rem;">
                        <small class="text-muted d-block mb-1">Rata-rata per Hari:</small>
                        <div style="font-size:0.55rem;">
                            @foreach($revenueComposition['weekdays'] as $w)
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span style="width:35px;">{{ $w['label'] }}</span>
                                <div class="flex-grow-1 mx-1"><div class="progress" style="height:4px;"><div class="progress-bar bg-primary" style="width:{{ $w['pct'] }}%"></div></div></div>
                                <span style="width:45px;text-align:right;">{{ $w['pct'] }}%</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ROW 3: CASH FLOW + EXPENSES + ACTIVITIES --}}
    <div class="row">
        {{-- Cash Flow --}}
        <div class="col-lg-4 mb-3">
            <div class="card card-apms h-100">
                <div class="card-header py-2"><h3 class="card-title" style="font-size:0.85rem;"><i class="fas fa-water mr-1 text-{{ $cashFlow['status']==='surplus'?'success':'danger' }}"></i> Arus Kas (Proyeksi 30 Hari)</h3></div>
                <div class="card-body py-2">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h3 class="mb-0 font-weight-bold text-{{ $cashFlow['status']==='surplus'?'success':'danger' }}" style="font-size:1.2rem;">Rp {{ number_format($cashFlow['projectedBalance30'],0,',','.') }}</h3>
                        <span class="badge badge-{{ $cashFlow['status']==='surplus'?'success':'danger' }}" style="font-size:0.65rem;">{{ $cashFlow['status']==='surplus'?'Surplus':'Defisit' }}</span>
                    </div>
                    @if($cashFlow['daysUntilZero']!==null)
                    <div class="alert alert-danger py-1 px-2 mb-2" style="font-size:0.6rem;"><i class="fas fa-exclamation-circle mr-1"></i>Kas habis dalam <strong>{{ $cashFlow['daysUntilZero'] }} hari</strong></div>
                    @endif
                    <div style="font-size:0.6rem;">
                        <div class="d-flex justify-content-between"><span class="text-muted">Pemasukan/hari:</span><strong class="text-success">Rp {{ number_format($cashFlow['avgDailyIn'],0,',','.') }}</strong></div>
                        <div class="d-flex justify-content-between"><span class="text-muted">Pengeluaran/hari:</span><strong class="text-danger">Rp {{ number_format($cashFlow['avgDailyOut'],0,',','.') }}</strong></div>
                        <div class="d-flex justify-content-between"><span class="text-muted">Saldo (20 hr):</span><strong>Rp {{ number_format($cashFlow['currentBalance'],0,',','.') }}</strong></div>
                    </div>
                    <hr class="my-1">
                    <div class="small" style="font-size:0.55rem;">
                        @foreach(array_reverse(array_slice($cashFlow['balances'],-7,7,true)) as $date=>$bal)
                        <div class="d-flex justify-content-between">
                            <span>{{ \Carbon\Carbon::parse($date)->format('d/m') }}</span>
                            <span class="text-{{ $bal>=0?'success':'danger' }}">Rp {{ number_format($bal,0,',','.') }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Expense Breakdown --}}
        <div class="col-lg-4 mb-3">
            <div class="card card-apms h-100">
                <div class="card-header py-2"><h3 class="card-title" style="font-size:0.85rem;"><i class="fas fa-coins mr-1 text-orange"></i> Rincian Biaya</h3></div>
                <div class="card-body py-2">
                    <div class="text-center mb-2"><span class="text-muted" style="font-size:0.55rem;">Total Biaya Bulan Ini</span><h5 class="mb-0 font-weight-bold" style="font-size:1rem;">Rp {{ number_format($expenseInsight['totalExpenses'],0,',','.') }}</h5></div>
                    @if(!empty($expenseInsight['analysis']))
                    <div style="font-size:0.6rem;">
                        @foreach($expenseInsight['analysis'] as $e)
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span>{{ $e['category'] }}</span>
                            <div><span class="text-muted">Rp {{ number_format($e['total'],0,',','.') }}</span><span class="ml-1 badge badge-{{ $e['change']>5?'danger':($e['change']<-5?'success':'secondary') }}" style="font-size:6px;">{{ $e['change']>0?'+':'' }}{{ $e['change'] }}%</span></div>
                        </div>
                        @if($e['pct']>5)
                        <div class="progress mb-1" style="height:2px;"><div class="progress-bar bg-{{ $e['change']>10?'danger':'warning' }}" style="width:{{ $e['pct'] }}%"></div></div>
                        @endif
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Recent Activities --}}
        <div class="col-lg-4 mb-3">
            <div class="card card-apms h-100">
                <div class="card-header py-2"><h3 class="card-title" style="font-size:0.85rem;"><i class="fas fa-rss mr-1 text-secondary"></i> Aktivitas Terbaru</h3></div>
                <div class="card-body p-0" style="max-height:350px;overflow-y:auto;">
                    @forelse($activities['activities'] as $act)
                    <div class="p-2 {{ !$loop->last?'border-bottom':'' }}">
                        <div class="d-flex align-items-start">
                            <span class="badge badge-{{ $act['color'] }} p-1 mr-2 mt-1" style="font-size:7px;"><i class="fas {{ $act['icon'] }}"></i></span>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <strong style="font-size:0.6rem;">{{ $act['title'] }}</strong>
                                    <small class="text-muted" style="font-size:0.5rem;">{{ $act['time'] }}</small>
                                </div>
                                <small class="text-muted d-block" style="font-size:0.55rem;">{{ $act['description'] }}</small>
                                <small class="font-weight-bold text-success" style="font-size:0.55rem;">Rp {{ number_format($act['amount'],0,',','.') }}</small>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-3"><h6 style="font-size:0.7rem;">Belum ada aktivitas</h6></div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    var c = document.getElementById('healthGauge');
    if (c) {
        var s = parseInt(c.dataset.score)||0, ctx = c.getContext('2d'), cx=70, cy=70, r=58;
        var sa = -Math.PI*0.75, ea = Math.PI*0.75, ta = ea-sa, ca = sa+(ta*s/100);
        ctx.beginPath(); ctx.arc(cx,cy,r,sa,ea); ctx.strokeStyle='#e9ecef'; ctx.lineWidth=10; ctx.lineCap='round'; ctx.stroke();
        var cl = s>=85?'#28a745':(s>=70?'#007bff':(s>=50?'#ffc107':'#dc3545'));
        ctx.beginPath(); ctx.arc(cx,cy,r,sa,ca); ctx.strokeStyle=cl; ctx.lineWidth=10; ctx.lineCap='round'; ctx.stroke();
        for(var i=0;i<=10;i++){var a=sa+(ta*i/10);ctx.beginPath();ctx.moveTo(cx+(r-8)*Math.cos(a),cy+(r-8)*Math.sin(a));ctx.lineTo(cx+(r-2)*Math.cos(a),cy+(r-2)*Math.sin(a));ctx.strokeStyle=i%5===0?'#6c757d':'#ced4da';ctx.lineWidth=i%5===0?1.5:0.8;ctx.stroke();}
    }
});
</script>
@endpush
