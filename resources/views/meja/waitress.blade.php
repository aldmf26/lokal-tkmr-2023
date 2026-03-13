<div class="row mb-3">
    <div class="col-12 text-center">
        <div class="d-inline-flex border rounded-pill px-4 py-2 bg-white shadow-sm"
            style="gap: 20px; font-size: 0.8rem; font-weight: 700;">
            <div class="d-flex align-items-center"><span class="mr-2"
                    style="width: 12px; height: 12px; background: #e74c3c; border-radius: 4px;"></span> DIMASAK</div>
            <div class="d-flex align-items-center"><span class="mr-2"
                    style="width: 12px; height: 12px; background: #3498db; border-radius: 4px;"></span> SIAP BAYAR</div>
            <div class="d-flex align-items-center"><span class="mr-2"
                    style="width: 12px; height: 12px; background: #f1c40f; border-radius: 4px;"></span> SUDAH BAYAR
            </div>
        </div>
    </div>
</div>

<div class="meja-grid">
    @foreach ($meja as $m)
        @php
            $isOccupied = !empty($m->no_order);
            $isPaid = !empty($m->paid_order);
            $isCooking = $m->items_cooking > 0;

            $statusClass = 'empty';
            $statusText = 'KOSONG';

            if ($isOccupied) {
                if ($isPaid) {
                    $statusClass = 'pending';
                    $statusText = 'SUDAH BAYAR';
                } elseif ($isCooking) {
                    $statusClass = 'occupied';
                    $statusText = 'DIMASAK';
                } else {
                    $statusClass = 'ready';
                    $statusText = 'SIAP BAYAR';
                }
            }

            $tableNum = str_ireplace(['Meja ', 'Gojek ', 'Gojek', 'Grab ', 'Grab'], '', $m->nm_meja);

            // Checker Dapur Logic
            $hasPrintedDapur = ($m->prn == 'Y' || $m->c_prn == 'Y');
            $dapurLabel = $hasPrintedDapur ? 'COPY CHK DPUR' : 'CHK DPUR';
            $dapurRoute = $hasPrintedDapur ? 'copy_checker' : 'checker';

            // Checker Tamu Logic
            $hasPrintedTamu = ($m->t_prn == 'Y' || $m->ct_prn == 'Y');
            $tamuLabel = $hasPrintedTamu ? 'COPY CHK TAMU' : 'CHK TAMU';
            $tamuRoute = $hasPrintedTamu ? 'copy_checker_tamu' : 'checker_tamu';
        @endphp

        <style>
            .meja-card.ready {
                border-top: 8px solid #3498db;
            }

            .ready .meja-status {
                background: rgba(52, 152, 219, 0.1);
                color: #3498db;
            }

            .btn-text-action {
                font-size: 0.75rem;
                font-weight: 800;
                padding: 10px 5px;
                height: auto;
                line-height: 1.2;
                text-align: center;
                border-radius: 8px;
                text-transform: uppercase;
            }
        </style>

        @if($isOccupied)
            @php
                $distriType = 'DINE IN';
                if (stripos($m->nm_meja, 'Gojek') !== false)
                    $distriType = 'GOJEK';
                if (stripos($m->nm_meja, 'Grab') !== false)
                    $distriType = 'GRAB';
            @endphp
            <div class="meja-card {{ $statusClass }}">
                <div class="card-body">
                    <div class="distri-type">{{ $distriType }}</div>
                    <div class="meja-number">{{ $tableNum }}</div>
                    <div class="meja-status text-center w-100 mb-2">{{ $statusText }}</div>

                    <div class="occupied-info">
                        <div class="text-muted mb-1" style="font-size: 0.85rem; font-weight: 600;">#{{ $m->no_order }}</div>
                        <div class="font-weight-bold" style="font-size: 1.1rem; color: #2c3e50;">
                            Rp {{ number_format($m->subtotal) }}
                        </div>
                    </div>
                </div>

                <div class="meja-footer">
                    <!-- Baris 1 -->
                    <a class="btn-meja-action btn-text-action muncul" data-toggle="modal" id_meja="{{ $m->id_meja }}"
                        no_order="{{ $m->no_order }}" href="#view_menu">
                        DETAIL
                    </a>

                    <div class="dropdown">
                        <a class="btn-meja-action btn-text-action w-100" type="button" data-toggle="dropdown">
                            TAMBAH
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a data-toggle="modal" class="btn_tbh dropdown-item plusPesanan" no_order="{{ $m->no_order }}"
                                no_meja="{{ $m->id_meja }}" href="#tbh_menu">Resto</a>
                            <a data-toggle="modal" class="btn_tbh_majo dropdown-item plusPesanan" no_order="{{ $m->no_order }}"
                                no_meja="{{ $m->id_meja }}" href="#tbh_menu_majo">STK (Majoo)</a>
                        </div>
                    </div>

                    <!-- Baris 2 -->
                    <a target="_blank" href="{{ route('billing', ['no' => $m->no_order]) }}"
                        class="btn-meja-action btn-text-action">
                        BILL
                    </a>

                    @if(!$isPaid)
                        <a href="javascript:void(0)" class="btn-meja-action btn-text-action btn_pembayaran"
                            no_order="{{ $m->no_order }}"
                            style="background: {{ $isCooking ? '#95a5a6' : '#27ae60' }}; color: white; border-color: transparent;">
                            BAYAR
                        </a>
                    @else
                        <a class="btn-meja-action btn-text-action clear" kode="{{ $m->no_order }}"
                            style="background: #e67e22; color: white; border-color: transparent;">
                            BERSIHKAN
                        </a>
                    @endif

                    <!-- Baris 3 -->
                    <a target="_blank" href="{{ route($dapurRoute, ['no' => $m->no_order]) }}"
                        class="btn-meja-action btn-text-action">
                        {{ $dapurLabel }}
                    </a>

                    <a target="_blank" href="{{ route($tamuRoute, ['no' => $m->no_order]) }}"
                        class="btn-meja-action btn-text-action">
                        {{ $tamuLabel }}
                    </a>

                    <!-- Baris 4 -->
                    <a target="_blank" href="{{ route('all_checker', ['no' => $m->no_order]) }}"
                        class="btn-meja-action btn-text-action" style="grid-column: span 2;">
                        PRINT ALL
                    </a>
                </div>
            </div>
        @endif
    @endforeach
</div>