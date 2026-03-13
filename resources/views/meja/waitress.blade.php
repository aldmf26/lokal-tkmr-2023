<div class="meja-grid">
    @foreach ($meja as $m)
        @php
            $isOccupied = !empty($m->no_order);
            $statusClass = $isOccupied ? 'occupied' : 'empty';
            $statusText = $isOccupied ? 'TERISI' : 'KOSONG';
            
            // Calculate time if occupied
            $timer = '';
            if ($isOccupied && !empty($m->j_mulai)) {
                $start = new DateTime($m->j_mulai);
                $now = new DateTime();
                $diff = $start->diff($now);
                $timer = ($diff->h > 0 ? $diff->h . 'h ' : '') . $diff->i . 'm';
            }
        @endphp

        <div class="meja-card {{ $statusClass }}" 
             @if(!$isOccupied) 
                onclick="window.location.href='{{ route('order', ['id_distribusi' => $id, 'meja' => $m->id_meja]) }}'"
             @endif>
            
            @if($isOccupied)
                <span class="timer-badge"><i class="fas fa-clock"></i> {{ $timer }}</span>
            @endif

            <div class="card-body">
                <div class="meja-number">{{ $m->nm_meja }}</div>
                <div class="meja-status">{{ $statusText }}</div>
                
                @if($isOccupied)
                    <div class="occupied-info">
                        <strong>{{ $m->no_order }}</strong><br>
                        {{ $m->qty1 }} Items
                    </div>
                @else
                    <div class="text-muted"><i class="fas fa-plus-circle"></i> Buka Order</div>
                @endif
            </div>

            @if($isOccupied)
                <div class="meja-footer">
                    <!-- View Order -->
                    <a class="btn-meja-action muncul" data-toggle="modal" 
                       id_meja="{{ $m->id_meja }}" no_order="{{ $m->no_order }}" href="#view_menu" title="Detail">
                        <i class="fas fa-eye"></i>
                    </a>

                    <!-- Add Order -->
                    <div class="dropdown">
                        <a class="btn-meja-action" type="button" data-toggle="dropdown" title="Tambah">
                            <i class="fas fa-plus"></i>
                        </a>
                        <div class="dropdown-menu">
                            <a data-toggle="modal" class="btn_tbh dropdown-item" no_order="{{ $m->no_order }}" href="#tbh_menu">Resto</a>
                            <a data-toggle="modal" class="btn_tbh_majo dropdown-item" no_order="{{ $m->no_order }}" href="#tbh_menu_majo">Stk</a>
                        </div>
                    </div>

                    <!-- Print Bill -->
                    <a target="_blank" href="{{ route('billing', ['no' => $m->no_order]) }}" 
                       class="btn-meja-action" title="Print Bill">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </a>

                    <!-- Payment - ALWAYS ENABLED -->
                    <a href="javascript:void(0)" class="btn-meja-action btn_pembayaran" 
                       no_order="{{ $m->no_order }}" title="Bayar" style="background: #2ecc71; color: white;">
                        <i class="fas fa-cash-register"></i>
                    </a>
                    
                    <!-- Clear Up -->
                    <a class="btn-meja-action clear" kode="{{ $m->no_order }}" title="Clear Up">
                        <i class="fas fa-hand-sparkles"></i>
                    </a>
                </div>
            @endif
        </div>
    @endforeach
</div>
