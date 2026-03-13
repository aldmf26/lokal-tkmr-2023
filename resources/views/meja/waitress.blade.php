<div class="meja-grid">
    @foreach ($meja as $m)
        @php
            $isOccupied = !empty($m->no_order);
            $isPaid = !empty($m->paid_order);
            $statusClass = $isOccupied ? 'occupied' : 'empty';
            $statusText = $isOccupied ? ($isPaid ? 'PAID' : 'TERISI') : 'KOSONG';
            
            if ($isPaid) $statusClass = 'pending'; // Use yellow/pending color for Paid but not cleared

            // Calculate time if occupied
            $timer = '';
            if ($isOccupied && !empty($m->j_mulai)) {
                $start = new DateTime($m->j_mulai);
                $now = new DateTime();
                $diff = $start->diff($now);
                $timer = ($diff->h > 0 ? $diff->h . 'h ' : '') . $diff->i . 'm';
            }
        @endphp

        {{-- Hide empty tables as requested --}}
        @if($isOccupied)
        <div class="meja-card {{ $statusClass }}">
            <span class="timer-badge"><i class="fas fa-clock"></i> {{ $timer }}</span>

            <div class="card-body">
                <div class="meja-number">{{ $m->nm_meja }}</div>
                <div class="meja-status">{{ $statusText }}</div>
                
                <div class="occupied-info">
                    <strong>{{ $m->no_order }}</strong><br>
                    {{ $m->qty1 }} Items
                </div>
            </div>

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
                        {{-- Added no_meja attribute to match JS plusPesanan logic --}}
                        <a data-toggle="modal" class="btn_tbh dropdown-item plusPesanan" 
                           no_order="{{ $m->no_order }}" no_meja="{{ $m->id_meja }}" href="#tbh_menu">Resto</a>
                        <a data-toggle="modal" class="btn_tbh_majo dropdown-item" 
                           no_order="{{ $m->no_order }}" href="#tbh_menu_majo">Stk</a>
                    </div>
                </div>

                <!-- Print Bill -->
                <a target="_blank" href="{{ route('billing', ['no' => $m->no_order]) }}" 
                   class="btn-meja-action" title="Print Bill">
                    <i class="fas fa-file-invoice-dollar"></i>
                </a>

                <!-- Payment Button -->
                <a href="javascript:void(0)" class="btn-meja-action btn_pembayaran" 
                   no_order="{{ $m->no_order }}" title="Bayar" 
                   style="background: {{ $isPaid ? '#bdc3c7' : '#2ecc71' }}; color: white;">
                    <i class="fas fa-cash-register"></i>
                </a>
                
                <!-- Clear Up - ONLY if paid -->
                @if($isPaid)
                <a class="btn-meja-action clear" kode="{{ $m->no_order }}" title="Clear Up" style="background: #3498db; color: white;">
                    <i class="fas fa-hand-sparkles"></i>
                </a>
                @endif
            </div>
        </div>
        @endif
    @endforeach
</div>
