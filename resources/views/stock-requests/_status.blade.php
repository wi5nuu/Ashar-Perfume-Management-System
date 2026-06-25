@if($stockRequest->status === 'pending')
    <span class="badge badge-warning"><i class="fas fa-clock mr-1"></i>Pending</span>
@elseif($stockRequest->status === 'approved')
    <span class="badge badge-info"><i class="fas fa-check mr-1"></i>Disetujui</span>
@elseif($stockRequest->status === 'preparing')
    <span class="badge badge-primary"><i class="fas fa-boxes mr-1"></i>Disiapkan</span>
@elseif($stockRequest->status === 'shipped')
    <span class="badge badge-warning"><i class="fas fa-truck mr-1"></i>Dikirim</span>
@elseif($stockRequest->status === 'received')
    <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i>Diterima</span>
@elseif($stockRequest->status === 'cancelled')
    <span class="badge badge-danger"><i class="fas fa-times mr-1"></i>Dibatalkan</span>
@endif
