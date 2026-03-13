<div class="row mb-3">
    <div class="col-12">
        <div class="d-flex justify-content-center align-items-center flex-wrap" style="gap: 20px; font-size: 0.9rem; font-weight: 600;">
            <div class="d-flex align-items-center"><span style="width: 15px; height: 15px; background: #e74c3c; border-radius: 4px; margin-right: 8px;"></span> BELUM BAYAR</div>
            <div class="d-flex align-items-center"><span style="width: 15px; height: 15px; background: #f1c40f; border-radius: 4px; margin-right: 8px;"></span> SUDAH BAYAR</div>
            <div class="d-flex align-items-center"><span style="width: 15px; height: 15px; background: #2ecc71; border-radius: 4px; margin-right: 8px;"></span> KOSONG</div>
        </div>
    </div>
</div>

<div class="meja-grid">
    @foreach ($meja as $m)
        @php
            $isOccupied = !empty($m->no_order);
            $isPaid = !empty($m->paid_order);
            $statusClass = $isOccupied ? 'occupied' : 'empty';
            $statusText = $isOccupied ? ($isPaid ? 'PAID' : 'TERISI') : 'KOSONG';
            
            if ($isPaid) $statusClass = 'pending'; 

            $timer = '';
            if ($isOccupied && !empty($m->j_mulai)) {
                $start = new DateTime($m->j_mulai);
                $now = new DateTime();
                $diff = $start->diff($now);
                $timer = ($diff->h > 0 ? $diff->h . 'h ' : '') . $diff->i . 'm';
            }
        @endphp

        {{-- Hide empty tables as per user's request --}}
        @if($isOccupied)
        <div class="meja-card {{ $statusClass }}">
            <span class="timer-badge"><i class="fas fa-clock"></i> {{ $timer }}</span>

            <div class="card-body">
                <div class="meja-number">{{ $m->nm_meja }}</div>
                <div class="meja-status text-center w-100">{{ $statusText }}</div>
                
                <div class="occupied-info">
                    <strong style="color: #34495e; font-size: 0.8rem;">#{{ $m->no_order }}</strong><br>
                    <span class="badge badge-secondary mb-1">{{ $m->qty1 }} Items</span><br>
                    <div style="font-size: 1.2rem; font-weight: 900; color: #2c3e50; border-top: 1px solid rgba(0,0,0,0.1); padding-top: 5px;">
                        Rp {{ number_format($m->subtotal) }}
                    </div>
                </div>
            </div>

            <div class="meja-footer">
                <!-- View Detail -->
                <a class="btn-meja-action muncul" data-toggle="modal" 
                   id_meja="{{ $m->id_meja }}" no_order="{{ $m->no_order }}" href="#view_menu" title="Detail Pesanan">
                    <i class="fas fa-search-plus"></i>
                </a>

                <!-- Add Order Dropdown -->
                <div class="dropdown">
                    <a class="btn-meja-action" type="button" data-toggle="dropdown" title="Tambah Pesanan">
                        <i class="fas fa-cart-plus"></i>
                    </a>
                    <div class="dropdown-menu">
                        <a data-toggle="modal" class="btn_tbh dropdown-item" 
                           no_order="{{ $m->no_order }}" href="#tbh_menu">Produk Resto</a>
                        <a data-toggle="modal" class="btn_tbh_majo dropdown-item" 
                           no_order="{{ $m->no_order }}" href="#tbh_menu_majo">Produk STK (Majoo)</a>
                    </div>
                </div>

                <!-- Print Bill -->
                <a target="_blank" href="{{ route('billing', ['no' => $m->no_order]) }}" 
                   class="btn-meja-action" title="Print Bill">
                    <i class="fas fa-print"></i>
                </a>

                <!-- Payment - Only if not paid yet -->
                @if(!$isPaid)
                <a href="javascript:void(0)" class="btn-meja-action btn_pembayaran" 
                   no_order="{{ $m->no_order }}" title="Bayar Sekarang" 
                   style="background: #27ae60; color: white;">
                    <i class="fas fa-money-bill-wave"></i>
                </a>
                @else
                <a href="javascript:void(0)" class="btn-meja-action" title="Sudah Dibayar" 
                   style="background: #bdc3c7; color: white; cursor: not-allowed;">
                    <i class="fas fa-check-circle"></i>
                </a>
                @endif
                
                <!-- Clear Up - Only after payment -->
                @if($isPaid)
                <a class="btn-meja-action clear" kode="{{ $m->no_order }}" title="Clear Up Meja" 
                   style="background: #2980b9; color: white;">
                    <i class="fas fa-broom"></i>
                </a>
                @endif
            </div>
        </div>
        @endif
    @endforeach
</div>
