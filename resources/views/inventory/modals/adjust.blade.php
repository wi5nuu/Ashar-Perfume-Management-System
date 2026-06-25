<!-- Stock Adjustment Modal -->
<div class="modal fade" id="adjustModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Stock Adjustment</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="adjustForm" action="{{ route('inventory.adjust') }}" method="POST">
                @csrf
                <input type="hidden" id="adjustInventoryId" name="inventory_id">
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Produk</label>
                                <select class="form-control select2" id="adjustProduct" name="product_id" required>
                                    <option value="">Pilih Produk</option>
                                    @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-stock="{{ $product->current_stock ?? 0 }}">
                                        {{ $product->name }} (Stok: {{ $product->current_stock ?? 0 }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Batch Number (Opsional)</label>
                                <input type="text" class="form-control" name="batch_number">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tipe Adjustment</label>
                                <select class="form-control" id="adjustmentType" name="adjustment_type" required>
                                    <option value="add">Tambah Stok</option>
                                    <option value="subtract">Kurangi Stok</option>
                                    <option value="set">Set Stok Ke</option>
                                    <option value="correction">Koreksi</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Jumlah</label>
                                <input type="number" class="form-control" id="adjustmentQuantity" 
                                       name="quantity" min="0" step="1" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Harga Beli (per unit)</label>
                                <input type="number" class="form-control" name="cost_per_unit" 
                                       min="0" step="0.01">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Alasan Adjustment *</label>
                        <select class="form-control" name="reason" required>
                            <option value="">Pilih Alasan</option>
                            <option value="stock_take">Stock Take</option>
                            <option value="damaged">Produk Rusak</option>
                            <option value="expired">Kadaluarsa</option>
                            <option value="lost">Produk Hilang</option>
                            <option value="found">Produk Ditemukan</option>
                            <option value="donation">Donasi</option>
                            <option value="sample">Sample</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Deskripsi / Catatan</label>
                        <textarea class="form-control" name="description" rows="3" 
                                  placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Stok Saat Ini:</strong> <span id="currentStockText">0</span> unit
                        <br>
                        <strong>Stok Setelah Adjustment:</strong> <span id="afterAdjustText">0</span> unit
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-apms">Simpan Adjustment</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function() {
    // Update adjustment calculation
    function updateAdjustmentCalc() {
        const type = $('#adjustmentType').val();
        const quantity = parseInt($('#adjustmentQuantity').val()) || 0;
        const current = parseInt($('#currentStockText').text()) || 0;
        let after = current;
        
        switch(type) {
            case 'add':
                after = current + quantity;
                break;
            case 'subtract':
                after = current - quantity;
                break;
            case 'set':
                after = quantity;
                break;
            case 'correction':
                after = quantity;
                break;
        }
        
        $('#afterAdjustText').text(after);
        
        // Color code
        if (after < 0) {
            $('#afterAdjustText').removeClass('text-success text-warning').addClass('text-danger');
        } else if (after < 10) {
            $('#afterAdjustText').removeClass('text-success text-danger').addClass('text-warning');
        } else {
            $('#afterAdjustText').removeClass('text-warning text-danger').addClass('text-success');
        }
    }
    
    $('#adjustProduct').change(function() {
        const selected = $(this).find('option:selected');
        const currentStock = selected.data('stock') || 0;
        $('#currentStockText').text(currentStock);
        updateAdjustmentCalc();
    });
    
    $('#adjustmentType, #adjustmentQuantity').on('change keyup', updateAdjustmentCalc);
    
    // Form submission
    $('#adjustForm').submit(function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Stock adjustment berhasil disimpan'
                }).then(() => {
                    location.reload();
                });
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan'
                });
            }
        });
    });
});
</script>
@endpush