@extends('template.master')
@section('content')
    <style>
        h6 {
            color: #155592;
            font-weight: bold;
        }

        .nav-pills .nav-link.active {
            color: #fff;
            background-color: #00A549;
            box-shadow: 0px 10px 20px 0px rgba(50, 50, 50, 0.52)
        }

        .custom-scrollbar-js,
        .custom-scrollbar-css {
            height: 75px;
        }

        /* Custom Scrollbar using CSS */
        .custom-scrollbar-css {
            overflow-y: scroll;
        }

        /* scrollbar width */
        .custom-scrollbar-css::-webkit-scrollbar {
            width: 3px;
        }

        /* scrollbar track */
        .custom-scrollbar-css::-webkit-scrollbar-track {
            background: #EEE;
        }

        /* scrollbar handle */
        .custom-scrollbar-css::-webkit-scrollbar-thumb {
            border-radius: 1rem;
            background: #26C784;
            background: -webkit-linear-gradient(to right, #11998e, #26C784);
            background: linear-gradient(to right, #11998e, #26C784);
        }
    </style>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container">
                <div class="row mb-2 justify-content-center">
                    <div class="col-sm-12">
                        <center>
                            <h4 style="color: #787878; font-weight: bold;">Tugas Bar / Beverages</h4>
                        </center>
                    </div>
                </div>
            </div>
        </div>
        
        <input type="hidden" id="id_distribusi" value="<?= $id ?>">
        <input type="hidden" id="jml_order" value="{{ $orderan[0]->jml_order ?? 0 }}">
        
        <div class="row justify-content-center mb-2">
            <div class="col-lg-2">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-info text-white"><i class="fas fa-search"></i></span>
                    </div>
                    <input type="text" id="search_meja" class="form-control" placeholder="Cari Meja..." style="font-weight: bold;">
                </div>
            </div>
            <div class="col-lg-2 mt-2 mt-lg-0">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-info text-white"><i class="fas fa-th-large"></i></span>
                    </div>
                    <select id="limit_meja" class="form-control" style="font-weight: bold;">
                        <option value="3">Tampil 3 Meja</option>
                        <option value="5">Tampil 5 Meja</option>
                        <option value="7">Tampil 7 Meja</option>
                    </select>
                </div>
            </div>
            <div class="col-lg-2 mt-2 mt-lg-0">
                <button type="button" id="refresh_halaman" class="btn btn-success btn-block">
                    <i class="fas fa-sync-alt mr-1"></i> Refresh Halaman
                </button>
            </div>
            <div class="col-lg-2 mt-2 mt-lg-0">
                <button type="button" class="btn btn-warning btn-block text-white" data-toggle="modal" data-target="#summary" onclick="load_history()" style="font-weight: bold;">
                    <i class="fas fa-history mr-1"></i> Selesai (1 Jam)
                </button>
            </div>
        </div>
        
        <!-- Main content -->
        <div class="content">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <audio id="audio" src=""></audio>

                                <!-- Summary Card for Batch Cooking (Bar version) -->
                                <div class="card card-outline card-success shadow-sm mb-4">
                                    <div class="card-header">
                                        <h3 class="card-title" style="font-weight: bold;"><i class="fas fa-glass-martini-alt mr-1"></i> Ringkasan Minuman (Batch Bar)</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                                        </div>
                                    </div>
                                    <div class="card-body p-2" id="summary_batch">
                                        <div class="d-flex flex-wrap" style="gap: 10px;">
                                            @php
                                                $allOrderSummary = DB::select("
                                                    SELECT b.nm_menu, SUM(grouped_qty.sum_qty) as total_qty, 
                                                    GROUP_CONCAT(CONCAT('Meja ', grouped_qty.no_meja, '(', grouped_qty.sum_qty, ')') SEPARATOR ', ') as tables
                                                    FROM (
                                                        SELECT id_harga, no_meja, SUM(qty) as sum_qty
                                                        FROM tb_order
                                                        WHERE id_lokasi = '$id_lokasi' AND selesai = 'dimasak' AND aktif = '1' AND void = 0
                                                        GROUP BY id_harga, no_meja
                                                    ) grouped_qty
                                                    JOIN tb_harga h ON grouped_qty.id_harga = h.id_harga
                                                    JOIN tb_menu b ON h.id_menu = b.id_menu
                                                    WHERE b.id_kategori = 5
                                                    GROUP BY grouped_qty.id_harga
                                                    ORDER BY total_qty DESC");
                                            @endphp
                                            @foreach($allOrderSummary as $summary)
                                                <div class="border rounded p-2 bg-light" style="min-width: 150px; flex: 1; border-left: 4px solid #28a745 !important;">
                                                    <div style="font-size: 0.9rem; font-weight: 800; text-transform: uppercase; color: #1e7e34;">{{ $summary->nm_menu }}</div>
                                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                                        <span class="badge badge-success" style="font-size: 1rem;">Total: {{ $summary->total_qty }}</span>
                                                        <small class="text-muted font-weight-bold ml-2">{{ $summary->tables }}</small>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div id="tugas_bar">
                                    <!-- Tasks will be loaded here via AJAX -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden audio element for notifications if needed -->
    <audio id="notif_bell" src="{{ asset_custom('assets/suara/notif.mp3') }}"></audio>

    <form>
        <div class="modal fade" id="summary" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg-max2" role="document" style="max-width: 1200px;">
                <div class="modal-content ">
                    <div class="modal-header bg-info">
                        <h5 class="modal-title text-light">View 1 Jam Terakhir</h5>
                        <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="badan"></div>
                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection

@section('script')
    <script>
        $(document).ready(function() {
            load_tugas();

            var ajaxCall = null;
            function load_tugas() {
                var limit = $("#limit_meja").val();
                var search_meja = $("#search_meja").val();
                
                // Reload summary card content
                $('#summary_batch').load(location.href + ' #summary_batch > *');

                if (ajaxCall != null) {
                    ajaxCall.abort();
                }

                ajaxCall = $.ajax({
                    method: "GET",
                    url: "{{ route('get_bar') }}?limit=" + limit + "&meja=" + search_meja,
                    dataType: "html",
                    success: function(hasil) {
                        $('#tugas_bar').html(hasil);
                    }
                });
            }

            $(document).on('change', '#limit_meja', function() {
                load_tugas();
            });

            $(document).on('keyup', '#search_meja', function() {
                load_tugas();
            });

            $(document).on('click', '#refresh_halaman', function() {
                window.location.reload();
            });

            window.load_history = function() {
                $("#badan").html('<div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-3x" style="color: #787878;"></i><h5 class="mt-3">Memuat riwayat...</h5></div>');
                $("#badan").load("{{ route('view1jam') }}");
            };

            // Re-use logic for completing drinks
            $(document).on('click', '.selesai', function(event) {
                var kode = $(this).attr('kode');
                var id_meja = $(this).attr('id_meja');
                var btn = $(this);

                // UX improvement: make button act instantly to feel much faster
                btn.html('<i class="fas fa-spinner fa-spin"></i> Loading...').removeClass('btn-info').addClass('btn-secondary').css('pointer-events', 'none');
                btn.closest('tr').css('opacity', '0.5');

                $.ajax({
                    type: "GET",
                    url: "<?= route('head_selesei') ?>?kode=" + kode,
                    success: function(response) {
                        // Quick removal so it feels instant
                        btn.closest('tr').remove();
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 1000,
                            icon: 'success',
                            title: 'Minuman selesai'
                        });
                        load_tugas();
                    }
                });
            });

            $(document).on('click', '.muncul', function(event) {
                var id_meja = $(this).attr('id_meja');
                var no_order = $(this).attr('no_order');
                $.ajax({
                    type: "get",
                    url: "{{ route('load_menu_selesai') }}",
                    data: {
                        id_meja: id_meja,
                        no_order: no_order
                    },
                    beforeSend: function() {
                        $('.load_menu_s' + id_meja).html('loading...');
                    },
                    success: function(r) {
                        $('.load_menu_s' + id_meja).html(r);
                        $('.muncul' + id_meja).hide();
                        $('.hilang' + id_meja).show();
                    }
                });
            });

            $(document).on('click', '.hilang', function(event) {
                var id_meja = $(this).attr('id_meja');
                $('.load_menu_s' + id_meja).html('');
                $('.hilang' + id_meja).hide();
                $('.muncul' + id_meja).show();
            });

            // Auto refresh every 30 seconds if idle or if order count changes
            setInterval(function() {
                load_tugas();
            }, 30000);
        });
    </script>
@endsection
