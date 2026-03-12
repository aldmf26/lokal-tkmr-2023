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

        .nav {
            white-space: nowrap;
            display: block !important;
            flex-wrap: nowrap;
            max-width: 100%;
            overflow-x: scroll;
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch
        }

        .nav li {
            display: inline-block
        }

        input[type=number] {
            /*for absolutely positioning spinners*/
            position: relative;
            padding: 5px;
            padding-right: 25px;
        }

        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            opacity: 1;
        }

        input[type=number]::-webkit-outer-spin-button,
        input[type=number]::-webkit-inner-spin-button {
            -webkit-appearance: inner-spin-button !important;
            width: 25px;
            position: absolute;
            top: 0;
            right: 0;
            height: 100%;
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

        .badge-notif {
            position: relative;
        }

        .badge-notif[data-badge]:after {
            content: attr(data-badge);
            position: absolute;
            top: 3px;
            right: -2px;
            font-size: .7em;
            background: #e53935;
            color: white;
            width: 18px;
            height: 18px;
            text-align: center;
            line-height: 18px;
            border-radius: 50%;
        }
    </style>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container">
                <div class="row mb-2 justify-content-center">
                    <div class="col-sm-12">
                        <center>
                            <h4 style="color: #787878; font-weight: bold;">Tugas Head</h4>
                        </center>

                    </div><!-- /.col -->

                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->
        <input type="hidden" id="id_distribusi" value="<?= $id ?>">
        <input type="hidden" id="jml_order" value="{{ $orderan[0]->jml_order }}">
        <!-- Main content -->
        <div class="content">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="card">
                            <div id="jumlah"></div>
                            <div class="card-header">
                                <div id="distribusi"></div>
                            </div>
                            <div class="card-body">
                                <audio id="audio" src=""></audio>
                                <div id="tugas_head">

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
    <style>
        .modal-lg-max {
            max-width: 900px;
        }

        .modal-lg-max2 {
            max-width: 1200px;
        }
    </style>
    <form>
        <div class="modal fade" id="summary" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg-max2" role="document">
                <div class="modal-content ">
                    <div class="modal-header btn-costume">
                        <h5 class="modal-title text-light" id="exampleModalLabel">View 1 Jam Terakhir</h5>
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
            load_distribusi();


            function load_tugas() {
                var id_distribusi = $("#id_distribusi").val();
                // var jumlah1 = $("#jumlah").val();
                // var jumlah2 = $("#jumlah1").val();
                $.ajax({
                    method: "GET",
                    url: "{{ route('get_head') }}?id=" + id_distribusi,
                    dataType: "html",
                    beforeSend: function() {
                        $('#tugas_head').html('loading...');
                    },
                    success: function(hasil) {
                        $('#tugas_head').html(hasil);
                    }
                });

            }
            $(document).on('click', '.selesai_majo', function(event) {
                var kode = $(this).attr('kode');
                var no_order = $(this).attr('no_order');
                $.ajax({
                    type: "POST",
                    url: "{{ route('meja_selesai_majo') }}",
                    data: {
                        kode: kode,
                        '_token': "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            icon: 'success',
                            title: 'Makanan telah selesai'
                        });
                        load_tugas();
                    }
                });
            });
            $(document).on('click', '.selesai', function(event) {
                var kode = $(this).attr('kode');
                var s = $("#searchHead").val();
                var id_meja = $(this).attr('id_meja');
                // alert(id_meja);
                $.ajax({
                    type: "GET",
                    url: "<?= route('head_selesei') ?>?kode=" + kode,

                    success: function(response) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            icon: 'success',
                            title: 'Makanan telah selesai'
                        });

                        $.ajax({
                            method: "GET",
                            url: "{{ route('head2') }}",
                            data: {
                                id_meja: id_meja
                            },
                            dataType: "html",
                            success: function(hasil) {
                                $('.addmeja' + id_meja).html(hasil);
                                $('.meja' + id_meja).remove();
                                $('.meja' + id_meja).hide();
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.cancel', function(event) {
                var kode = $(this).attr('kode');
                var s = $("#searchHead").val();
                var id_meja = $(this).attr('id_meja');
                $.ajax({
                    type: "GET",
                    url: "<?= route('head_cancel') ?>?kode=" + kode,
                    success: function(response) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            icon: 'info',
                            title: 'Cancel orderan'
                        });

                        $.ajax({
                            method: "GET",
                            url: "{{ route('head2') }}?id_meja=" + id_meja,
                            dataType: "html",
                            beforeSend: function() {
                                $('.addmeja' + id_meja).html('loading...');
                            },
                            success: function(hasil) {
                                $('.addmeja' + id_meja).html(hasil);
                                $('.load_menu_s' + id_meja).html('');
                                $('.meja' + id_meja).remove();
                                $('.hilang' + id_meja).hide();
                                $('.muncul' + id_meja).show();
                            }
                        });

                    }
                });
            });


            function load_distribusi() {
                var id_distribusi = $("#id_distribusi").val();
                // var jumlah1 = $("#jumlah").val();
                var jml_baru = $("#jumlah1").val();
                var jml_order = $("#jml_order").val();
                $("#distribusi").load("{{ route('distribusi3') }}?id=" + id_distribusi, "data",
                    function(
                        response, status, request) {
                        this; // dom element
                        if (jml_baru != jml_order) {
                            load_tugas();
                            $("#jml_order").val(jml_baru);
                        }
                    });

            }


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

                // Sembunyikan data
                $('.load_menu_s' + id_meja).html('');


                // Ubah visibilitas tombol
                $('.hilang' + id_meja).hide();
                $('.muncul' + id_meja).show();
            });
        });
    </script>
    <script>
        function selection() {
            var selected = document.getElementById("select1").value;
            if (selected == 0) {
                document.getElementById("input1").removeAttribute("hidden");
            } else {
                //elsewhere actions
            }
        }
    </script>

    {{-- <script>
        $(document).ready(function() {
            var ua = navigator.userAgent,
                event = (ua.match(/iPad/i)) ? "touchstart" : "click";
            if ($('.table').length > 0) {
                $('.table .header').on(event, function() {
                    $(this).toggleClass("active", "").nextUntil('.header').css('display', function(i, v) {
                        return this.style.display === 'table-row' ? 'none' : 'table-row';
                    });
                });

            }
        })
    </script> --}}
@endsection
