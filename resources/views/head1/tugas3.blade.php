@extends('template.master')
@section('content')
<!-- Content Wrapper. Contains page content -->
<style>
    h6 {
        color: #155592;
        font-weight: bold;
    }
</style>
<style>
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
                        <h4 style="color: #787878; font-weight: bold;">Tugas Head sdsa</h4>
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
                            <div id="tugas_head"></div>
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
</style>
@endsection
@section('script')

<script>
    $(document).ready(function() {

        load_tugas();

        function load_tugas() {
            $("#tugas_head").load("{{ route('get_drink') }}?", "data", function(
                response, status, request) {
                this; // dom element

            });

        }

        $(document).on('click', '.muncul', function() {

            var id_harga = $(this).attr('id_harga');
            var status = $(".status" + id_harga).val();

            if (status == 'Show') {

                $('.mytr' + id_harga).show();
                $(".status" + id_harga).val('Hidden');
                $.ajax({
                    method: "GET",
                    url: "{{ route('get_tr2') }}?id_harga=" + id_harga,
                    dataType: "html",
                    success: function(hasil) {
                        $('.mytr' + id_harga).html(hasil);

                    }
                });
            } else {
                $(".status" + id_harga).val('Show');
                $('.mytr' + id_harga).hide();
            }
        });
        $(document).on('click', '.muncul2', function() {

            var id_harga = $(this).attr('id_harga');
            var status = $(".status2" + id_harga).val();
            if (status == 'Show') {

                $('.mytr' + id_harga).show();
                $(".status2" + id_harga).val('Hidden');
                $.ajax({
                    method: "GET",
                    url: "{{ route('get_tr3') }}?id_harga=" + id_harga,
                    dataType: "html",
                    success: function(hasil) {
                        $('.mytr' + id_harga).html(hasil);

                    }
                });
            } else {
                $(".status2" + id_harga).val('Show');
                $('.mytr' + id_harga).hide();
            }
        });


        $(document).on('click', '.btn_pembayaran', function(event) {
            var kode = [];
            $('input[name="nota"]:checked').each(function() {
                kode.push($(this).attr("kode"))
            });
            $.ajax({
                type: "POST",
                url: "<?= route('selesai_check') ?>",
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
                        title: 'Data orderan telah diselesaikan'
                    });

                }

            });
        });


        $(document).on('click', '.koki1', function(event) {
            var kode = $(this).attr('kode');
            var kry = $(this).attr('kry');
            var id_harga = $(this).attr('id_harga');

            $.ajax({
                type: "POST",
                url: "<?= route('koki1') ?>",
                data: {
                    kode: kode,
                    kry: kry,
                    '_token': "{{ csrf_token() }}"
                },
                success: function(response) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        icon: 'success',
                        title: 'Koki 1 berhasil ditambahkan'
                    });
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_drink') }}",
                        dataType: "html",
                        success: function(hasil) {
                            $('#tugas_head').html(hasil);
                            $(".status" + id_harga).val('Hidden');
                            $.ajax({
                                method: "GET",
                                url: "{{ route('get_tr2') }}?id_harga=" + id_harga,
                                dataType: "html",
                                success: function(hasil) {
                                    $(".status" + id_harga).val('Show');
                                    $('.mytr' + id_harga).html(hasil);

                                }
                            });
                        }
                    });




                }
            });
        });


        $(document).on('click', '.koki2', function(event) {

            var kode = $(this).attr('kode');
            var kry = $(this).attr('kry');
            var id_harga = $(this).attr('id_harga');
            $.ajax({
                type: "POST",
                url: "<?= route('koki2') ?>",
                data: {
                    kode: kode,
                    kry: kry,
                    '_token': "{{ csrf_token() }}"
                },
                success: function(response) {

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        icon: 'success',
                        title: 'Koki 2 berhasil ditambahkan'
                    });
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_drink') }}",
                        dataType: "html",
                        success: function(hasil) {
                            $('#tugas_head').html(hasil);
                            $(".status" + id_harga).val('Hidden');
                            $.ajax({
                                method: "GET",
                                url: "{{ route('get_tr2') }}?id_harga=" + id_harga,
                                dataType: "html",
                                success: function(hasil) {
                                    $('.mytr' + id_harga).html(hasil);

                                }
                            });
                        }
                    });

                }
            });
        });

        $(document).on('click', '.koki3', function(event) {
            var kode = $(this).attr('kode');
            var kry = $(this).attr('kry');
            var id_harga = $(this).attr('id_harga');
            $.ajax({
                type: "POST",
                url: "<?= route('koki3') ?>",
                data: {
                    kode: kode,
                    kry: kry,
                    '_token': "{{ csrf_token() }}"
                },
                success: function(response) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        icon: 'success',
                        title: 'Koki 3 berhasil ditambahkan'
                    });
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_drink') }}",
                        dataType: "html",
                        success: function(hasil) {
                            $('#tugas_head').html(hasil);
                            $(".status" + id_harga).val('Hidden');
                            $.ajax({
                                method: "GET",
                                url: "{{ route('get_tr2') }}?id_harga=" + id_harga,
                                dataType: "html",
                                success: function(hasil) {
                                    $('.mytr' + id_harga).html(hasil);

                                }
                            });
                        }
                    });

                }
            });
        });

        $(document).on('click', '.un_koki1', function(event) {
            var kode = $(this).attr('kode');
            var id_harga = $(this).attr('id_harga');
            $.ajax({
                type: "POST",
                url: "<?= route('un_koki1') ?>",
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
                        title: 'Koki 1 dibatalkan'
                    });
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_drink') }}",
                        dataType: "html",
                        success: function(hasil) {
                            $('#tugas_head').html(hasil);
                            $(".status" + id_harga).val('Hidden');
                            $.ajax({
                                method: "GET",
                                url: "{{ route('get_tr2') }}?id_harga=" + id_harga,
                                dataType: "html",
                                success: function(hasil) {
                                    $('.mytr' + id_harga).html(hasil);

                                }
                            });
                        }
                    });



                }
            });
        });
        $(document).on('click', '.un_koki2', function(event) {
            var kode = $(this).attr('kode');
            var id_harga = $(this).attr('id_harga');
            $.ajax({
                type: "POST",
                url: "<?= route('un_koki2') ?>",
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
                        title: 'Koki 2 dibatalkan'
                    });
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_drink') }}",
                        dataType: "html",
                        success: function(hasil) {
                            $('#tugas_head').html(hasil);
                            $(".status" + id_harga).val('Hidden');
                            $.ajax({
                                method: "GET",
                                url: "{{ route('get_tr2') }}?id_harga=" + id_harga,
                                dataType: "html",
                                success: function(hasil) {
                                    $('.mytr' + id_harga).html(hasil);

                                }
                            });
                        }
                    });

                }
            });
        });
        $(document).on('click', '.un_koki3', function(event) {
            var kode = $(this).attr('kode');
            var id_harga = $(this).attr('id_harga');
            $.ajax({
                type: "POST",
                url: "<?= route('un_koki3') ?>",
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
                        title: 'Koki 3 dibatalkan'
                    });
                    $.ajax({
                        method: "GET",
                        url: "{{ route('get_tr2') }}?id_harga=" + id_harga,
                        dataType: "html",
                        success: function(hasil) {
                            $('.mytr' + id_harga).html(hasil);

                        }
                    });

                }
            });
        });
        $(document).on('click', '.selesai', function(event) {
            var kode = $(this).attr('kode');
            var id_harga = $(this).attr('id_harga');

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
                        url: "{{ route('get_drink') }}",
                        dataType: "html",
                        success: function(hasil) {
                            $('#tugas_head').html(hasil);
                            $(".status" + id_harga).val('Hidden');
                            $.ajax({
                                method: "GET",
                                url: "{{ route('get_tr2') }}?id_harga=" + id_harga,
                                dataType: "html",
                                success: function(hasil) {
                                    $('.mytr' + id_harga).html(hasil);

                                }
                            });
                        }
                    });


                }
            });
        });
        $(document).on('click', '.cancel', function(event) {
            var kode = $(this).attr('kode');
            var id_harga = $(this).attr('id_harga');

            $.ajax({
                type: "GET",
                url: "<?= route('head_cancel') ?>?kode=" + kode,
                success: function(response) {
                    console.log(id_harga);
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
                        url: "{{ route('get_drink') }}",
                        dataType: "html",
                        success: function(hasil) {
                            $('#tugas_head').html(hasil);
                            $(".status" + id_harga).val('Hidden');
                            $.ajax({
                                method: "GET",
                                url: "{{ route('get_tr3') }}?id_harga=" + id_harga,
                                dataType: "html",
                                success: function(hasil) {
                                    $('.mytr' + id_harga).html(hasil);

                                }
                            });
                        }
                    });
                }
            });
        });
        $(document).on('click', '.gagal', function(event) {

            Swal.fire({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                icon: 'error',
                title: 'Koki belum di pilih'
            });



        });



        $(document).on('change', '.tes', function(event) {
            var kode = [];
            $('input[name="nota"]:checked').each(function() {
                kode.push($(this).attr("kode"))
            });
            console.log(kode);
        });

        load_distribusi();
        setInterval(function() {
            $.ajax({
                type: "GET",
                dataType: "json",
                data: {},
            });

            load_distribusi();


        }, 5000);
        setInterval(function() {
            $.ajax({
                type: "GET",
                dataType: "json",
                data: {},
            });

            load_tugas();


        }, 50000);

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

                        $("#jml_order").val(jml_baru);
                    }
                });

        }


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


@endsection
