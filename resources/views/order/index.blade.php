@extends('template.master')
@section('content')
    <?php
    $dt = date('Y-m-d');
    date_default_timezone_set('Asia/Jakarta');
    ?>
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
            /for absolutely positioning spinners/ position: relative;
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

        .wrap-num-product {
        width: 140px;

        border-radius: 3px;
        overflow: hidden;
    }

    .btn-num-product-up,
    .btn-num-product-down {
        width: 0px;
        height: 100%;
        cursor: pointer;
    }



    .num-product {
        width: calc(100% - 90px);
        height: 100%;
        border-left: 1px solid #e6e6e6;
        border-right: 1px solid #e6e6e6;
        background-color: #f7f7f7;
    }

    input.num-product {
        -moz-appearance: textfield;
        appearance: none;
        -webkit-appearance: none;
    }

    input.num-product::-webkit-outer-spin-button,
    input.num-product::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .tes {
        text-align: center;
        font-size: 20px;
        font-weight: bold;
    }

    element.style {}

    label:not(.form-check-label):not(.custom-file-label) {
        font-weight: 700;
    }

    .buying-selling.active {
        background-image: linear-gradient(to right, #00B7B5 0%, #00B7B5 19%, #019392 60%, #04817F 100%);
    }

    .option1 {
        display: none;
    }

    .buying-selling {
        width: 123px;
        padding: 10px;
        position: relative;
    }

    .buying-selling-word {
        font-size: 10px;
        font-weight: 600;
        margin-left: 35px;
    }

    .radio-dot:before,
    .radio-dot:after {
        content: "";
        display: block;
        position: absolute;
        background: #fff;
        border-radius: 100%;
    }

    .radio-dot:before {
        width: 20px;
        height: 20px;
        border: 1px solid #ccc;
        top: 10px;
        left: 16px;
    }

    .radio-dot:after {
        width: 12px;
        height: 12px;
        border-radius: 100%;
        top: 14px;
        left: 20px;
    }

    .buying-selling.active .buying-selling-word {
        color: #fff;
    }

    .buying-selling.active .radio-dot:after {
        background-image: linear-gradient(to right, #00B7B5 0%, #00B7B5 19%, #019392 60%, #04817F 100%);
    }

    .buying-selling.active .radio-dot:before {
        background: #fff;
        border-color: #699D17;
    }

    .buying-selling:hover .radio-dot:before {
        border-color: #adadad;
    }

    .buying-selling.active:hover .radio-dot:before {
        border-color: #699D17;
    }


    /* .buying-selling.active .radio-dot:after {
					background-image: linear-gradient(to right, #f78ca0 0%, #f9748f 19%, #fd868c 60%, #fe9a8b 100%);
				} */

    /* dot */
    .buying-selling:hover .radio-dot:after {
        background-image: linear-gradient(to right, #00B7B5 0%, #00B7B5 19%, #019392 60%, #04817F 100%);
    }

    /* .buying-selling.active:hover .radio-dot:after {
					background-image: linear-gradient(to right, #f78ca0 0%, #f9748f 19%, #fd868c 60%, #fe9a8b 100%);

				} */

    @media (max-width: 400px) {

        .mobile-br {
            display: none;
        }

        .buying-selling {
            width: 49%;
            padding: 10px;
            position: relative;
        }

    }

    </style>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    @foreach ($distribusi as $d)
                        <div class="col-md-3 col-6">
                            <div class="card bg-gradient">
                                <div class="card-body">
                                    <nav class=" nav-pills nav-fill">
                                        <?php if ($d->id_distribusi == $id_dis) : ?>
                                        <a class="nav-item nav-link active"
                                            href="{{ route('order', ['dis' => $d->id_distribusi]) }}"
                                            style="font-weight: bold; color: #fff;">{{ $d->nm_distribusi }}</a>
                                        <?php else : ?>
                                        <a class="nav-item nav-link "
                                            href="{{ route('order', ['dis' => $d->id_distribusi]) }}"
                                            style="font-weight: bold; color: #fff;">{{ $d->nm_distribusi }}</a>
                                        <?php endif ?>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <div class="col-md-3 col-6" id="peringatan">

                    </div>
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <ul class="nav nav-pills mb-3 custom-scrollbar-css" id="pills-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home"
                                            role="tab" aria-controls="pills-home" aria-selected="true"><i
                                                class="fa fa-search"></i></a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link majoo" id_dis="{{$id}}" id="pills-profile-tab" data-toggle="pill"
                                            href="#majoo" role="tab" aria-controls="pills-profile"
                                            aria-selected="false"><strong>STK</strong></a>
                                    </li>
                                    @foreach ($kategori as $k)
                                        <li class="nav-item">
                                            <a class="nav-link" id="pills-profile-tab" data-toggle="pill"
                                                href="#{{ $k->ket }}" role="tab" aria-controls="pills-profile"
                                                aria-selected="false"><strong>{{ $k->kategori }}</strong></a>
                                        </li>
                                    @endforeach


                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <input type="hidden" id='dis' value="<?= $id ?>">
                        <input type="hidden" id='dis2' value="<?= $id_dis ?>">

                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-home" role="tabpanel"
                                aria-labelledby="pills-home-tab">
                                <div class="card">
                                    <div class="card-body">
                                        <input type="text" name="search_text" id="search_field" class="form-control"
                                            placeholder="Cari Menu . . ." />
                                    </div>
                                </div>
                                <div id="demonames">
                                    <div id="menu">

                                    </div>
                                    <div id="result2">

                                    </div>
                                </div>
                            </div>
                            @php
                                $tgl = date('Y-m-d');
                                $id_lokasi = Session::get('id_lokasi');
                                $sold_out = DB::table('tb_sold_out')
                                    ->where('tgl', $tgl)
                                    ->get();
                                $id_menu_sold_out = [];
                                foreach ($sold_out as $s) {
                                    $id_menu_sold_out[] = $s->id_menu;
                                }
                                
                                $idl = [];
                                $limit = DB::select("SELECT tb_menu.id_menu as id_menu FROM tb_menu
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        LEFT JOIN(SELECT SUM(qty) as jml_jual, tb_harga.id_menu FROM tb_order LEFT JOIN tb_harga ON tb_order.id_harga = tb_harga.id_harga WHERE tb_order.id_lokasi = $id_lokasi AND tb_order.tgl = '$tgl' AND tb_order.void = 0 GROUP BY tb_harga.id_menu) dt_order ON tb_menu.id_menu = dt_order.id_menu
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        LEFT JOIN(SELECT id_menu,batas_limit FROM tb_limit WHERE tgl = '$tgl' AND id_lokasi = $id_lokasi GROUP BY id_menu)dt_limit ON tb_menu.id_menu = dt_limit.id_menu
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        WHERE lokasi = $id_lokasi AND dt_order.jml_jual >= dt_limit.batas_limit");
                                foreach ($limit as $l) {
                                    $idl[] = $l->id_menu;
                                }
                            @endphp
                            @foreach ($kategori as $k)
                                <div class="tab-pane fade" id="{{ $k->ket }}" role="tabpanel"
                                    aria-labelledby="pills-profile-tab">
                                    <div class="row">
                                        @php
                                            $menu = DB::table('view_menu_kategori')
                                                ->join('tb_menu', 'view_menu_kategori.id_menu', 'tb_menu.id_menu')
                                                ->where('view_menu_kategori.lokasi', $id_lokasi)
                                                ->where('view_menu_kategori.id_distribusi', $id)
                                                ->where('view_menu_kategori.id_kategori', $k->kd_kategori)
                                                ->where('tb_menu.aktif', 'on')
                                                ->whereNotIn('view_menu_kategori.id_menu', $id_menu_sold_out)
                                                ->whereNotIn('view_menu_kategori.id_menu', $idl)
                                                ->get();
                                        @endphp

                                        @foreach ($menu as $t)
                                            <div class="col-md-3">
                                                <a href="" class="input_cart2" data-toggle="modal" data-target="#myModal"
                                                    id_harga="{{ $t->id_harga }}" id_dis="{{ $id_dis }}">
                                                    <div class="card">
                                                        <div
                                                            style="background-color: rgba(0, 0, 0, 0.5); padding:5px 0 5px;">
                                                            <h6 style="font-weight: bold; color:#fff;"
                                                                class="text-center">
                                                                {{ ucwords(Str::lower($t->nm_menu)) }}

                                                            </h6>
                                                        </div>
                                                        <div class="card-body" style="padding:0.2rem;">
                                                            <p class="mt-2 text-center demoname"
                                                                style="font-size:15px; color: #787878;"><strong>Rp.
                                                                    {{ number_format($t->harga) }}</strong></p>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                            <div class="tab-pane fade" id="majoo" role="tabpanel" aria-labelledby="pills-profile-tab">
                                <div class="col-lg-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <input type="text" name="search_text" id_dis="{{$id}}" id="search_majoo"
                                                class="form-control" placeholder="Cari Menu . . ." />
                                        </div>
                                    </div>
                                </div>
                                <div id="id_majoo">
                                    <div id="produk_majo">

                                    </div>
                                    <div id="result_majo">

                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <form action="{{ route('payment') }}" method="get">
                            <input type="hidden" name="distribusi" value="<?= $id_dis ?>">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="text-center" style="font-weight: bold;">KERANJANG BELANJA</h4>
                                    <div class="row justify-content-center">
                                    <div class="col-lg-2 col-2">
                                        <div class="card " style="padding: 0.25rem; background-color: red; ">
                                            <table>
                                                <td align="center"><input type="radio" name="warna" id="merah"
                                                        value="Merah" required></td>
                                                <td><label for="merah"
                                                        style="font-size: 10px;color: white">Merah</label></td>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-2">
                                        <div class="card " style="padding: 0.25rem ;background-color: yellow;">
                                            <table>
                                                <td align="center"><input type="radio" name="warna" id="kuning"
                                                        value="Kuning"></td>
                                                <td><label for="kuning" style="font-size: 10px">Kuning</label></td>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-2">
                                        <div class="card " style="padding: 0.25rem; background-color: green;">
                                            <table>
                                                <td align="center"><input type="radio" name="warna" id="hijau"
                                                        value="Hijau"></td>
                                                <td><label for="Hijau"
                                                        style="font-size: 10px;color: white">Hijau</label></td>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-2">
                                        <div class="card " style="padding: 0.25rem;background-color: blue;">
                                            <table>
                                                <td align="center"><input type="radio" name="warna" id="biru"
                                                        value="Biru"></td>
                                                <td><label for="biru" style="font-size: 10px; color: white">Biru</label>
                                                </td>
                                            </table>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-2 col-2">
                                        <div class="card " style="padding: 0.25rem">
                                            <table>
                                                <td align="center"><input type="radio" name="warna" id="putih"
                                                        value="Putih"></td>
                                                <td><label for="putih" style="font-size: 10px">Putih</label></td>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-2">
                                        <div class="card " style="padding: 0.25rem;background-color: rgb(22, 177, 204);">
                                            <table>
                                                <td align="center"><input type="radio" name="warna" id="takeaway"
                                                        value="Takeaway"></td>
                                                <td><label for="takeaway" style="font-size: 10px;color: white">Takeaway</label></td>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <label for="">Server</label>
                                        <select name="admin" class="form-control select2" required>
                                            <option value="">Pilih Server</option>
                                            <?php foreach($absen as $a): ?>
                                            <?php if($admin == $a->nama): ?>
                                            <option value="<?= $a->nama ?>" selected>
                                                <?= $a->nama ?>
                                            </option>
                                            <?php else :?>
                                            <option value="<?= $a->nama ?>">
                                                <?= $a->nama ?>
                                            </option>
                                            <?php endif ?>

                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                        <div class="col-lg-4">
                                            <label for="">Meja</label>
                                            <select name="meja" id="meja" class="form-control select2bs4">

                                            </select>
                                        </div>
                                        <div class="col-lg-4">
                                            <label for="">Orang</label>
                                            <input type="number" name="orang" class="form-control" value="1">
                                            <input type="hidden" class="form-control id_distribusi"
                                                value="{{ $id_distri->id_distribusi }}">
                                        </div>
                                    </div>


                                    <hr>
                                    <div id="keranjang">

                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content -->
    </div>

    <form method="get" class="input_cart">
        <div class="modal fade modal-cart" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-sm" role="document">
                <div id="harga"></div>
            </div>
        </div>
        </div>
    </form>
    <form method="get" class="input_cart_majo">
        <div class="modal fade modal-cart" id="modal_majo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header bg-info">
                        <h5 class="modal-title majoo">Detail Produk</h5>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <!-- Modal body -->
                    <div class="modal-body">

                        <div id="harga_majoo">

                        </div>
                        <hr>
                        <h5 style="font-size: 1rem;">DIJUAL OLEH</h5>

                        <div class="buying-selling-group" id="buying-selling-group" data-toggle="buttons">


                        </div>

                        <button type="submit" class="btn float-right  btn-costume"> SIMPAN</button>

                    </div>
                </div>
            </div>
        </div>
        </div>
    </form>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            var dis = $("#dis").val();
            var dis2 = $("#dis2").val();
            load_menu(1);

            $(document).on('click', '.pagination a', function(event) {
                event.preventDefault();
                var page = $(this).attr('href').split('page=')[1];
                load_menu(page);
            });


            // setInterval(function() {
            //     $("#peringatan").load("{{ route('get_peringatan') }}", "data", function(response, status,
            //         request) {
            //         this; // dom element

            //     });

            // }, 1000);




            function load_menu(page) {
                var dis = $("#dis").val();
                var dis2 = $("#dis2").val();
                // console.log(dis);
                // alert(page);
                console.log(page);
                $.ajax({
                    method: "GET",

                    url: "{{ route('get_order') }}?page=" + page + "&id_dis=" + dis + "&id_dis2=" + dis2,
                    dataType: "html",
                    success: function(hasil) {
                        $('#menu').html(hasil);
                    }
                });
            }
            $('#search_field').keyup(function() {
                var keyword = $("#search_field").val();
                if (keyword != '') {
                    $('#result2').show();
                    $('#menu').hide();
                    load_data(keyword);
                } else {
                    $('#result2').hide();
                    $('#menu').show();
                }

            });

            function load_data(keyword) {
                $.ajax({
                    method: "GET",
                    url: "{{ route('search') }}",
                    data: {
                        keyword: keyword,
                        dis: dis,
                        dis2: dis2,
                    },
                    success: function(hasil) {
                        $('#result2').html(hasil);
                    }
                });
            }

            $(document).on('click', '.input_cart2', function() {
                var id_harga = $(this).attr("id_harga");
                var id_dis = $(this).attr("id_dis");

                // console.log(id_harga);
                $.ajax({
                    url: "{{ route('item_menu') }}",
                    method: "GET",
                    data: {
                        id_harga: id_harga,
                        id_dis: id_dis,
                    },
                    success: function(data) {
                        $('#harga').html(data);
                        // alert(data);
                    }
                });

            });


            load_cart();

            function load_cart() {
                var dis2 = $("#dis2").val();
                $.ajax({
                    method: "GET",
                    url: "{{ route('keranjang') }}?dis=" + dis2,
                    success: function(hasil) {
                        $('#keranjang').html(hasil);
                    }
                });
            }

            $(document).on('submit', '.input_cart', function(event) {
                event.preventDefault();
                $('.btn_to_cart').hide();
                var id_harga2 = $("#id_harga2").val();
                var price = $("#price").val();
                var name = $("#name").val();
                var qty = $("#qty").val();
                var id_menu = $("#id_menu").val();
                var req = $("#req").val();
                var tipe = $("#tipe").val();
                var dis = "{{request()->get('dis')}}";

                $.ajax({
                    url: "{{ route('cart') }}",
                    method: 'GET',
                    data: {
                        id_harga2: id_harga2,
                        price: price,
                        name: name,
                        qty: qty,
                        req: req,
                        id_menu: id_menu,
                        tipe: tipe,
                        dis: dis,
                    },
                    success: function(data) {
                        if (data == 'berhasil') {
                            $('#cart_session').html(data);
                            $('.modal-cart').modal('hide');
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                icon: 'success',
                                title: 'Data berhasil ditambahkan'
                            });
                            load_cart();
                        } else {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                icon: 'error',
                                title: 'Jumlah melebihi batas limit, Maksimal order ' +
                                    data + ' porsi'
                            });
                        }
                    }
                });
            });

            $(document).on('click', '.delete_cart', function(event) {
                var rowid = $(this).attr("id");
                // alert(rowId);
                $.ajax({
                    url: "{{ route('delete_order') }}",
                    method: "GET",
                    data: {
                        rowid: rowid
                    },
                    success: function(data) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            icon: 'success',
                            title: 'Item dihapus dari keranjang'
                        });
                        $('#cart_session').html(data);
                        load_cart();
                    }
                });
            });

            $(document).on('click', '.min_cart', function(event) {
                var rowid = $(this).attr("id");
                var qty = $(this).attr("qty");

                // alert(qty);
                $.ajax({
                    url: "{{ route('min_cart') }}",
                    method: "GET",
                    data: {
                        rowid: rowid,
                        qty: qty
                    },
                    success: function(data) {
                        // $('#cart_session').html(data); 
                        load_cart();
                    }
                });
            });
            $(document).on('click', '.plus_cart', function(event) {
                var rowid = $(this).attr("id");
                var qty = $(this).attr("qty");

                // alert(qty);
                $.ajax({
                    url: "{{ route('plus_cart') }}",
                    method: "GET",
                    data: {
                        rowid: rowid,
                        qty: qty
                    },
                    success: function(data) {
                        // $('#cart_session').html(data); 
                        load_cart();
                    }
                });
            });

            get_meja(1);

            function get_meja(dis) {
                var dis2 = $("#dis2").val();
                $.ajax({
                    method: "GET",
                    url: "{{ route('get_meja2') }}?dis=" + dis2,
                    dataType: "html",
                    success: function(hasil) {
                        $('#meja').html(hasil);
                    }
                });
            }
        });
    </script>
    <script>
        $(document).ready(function(){
            var dis = $("#dis").val()
            $(document).on('click', '.majoo', function(event) {
                var id_dis = $(this).attr("id_dis");
                var url = "{{ route('get_majo') }}?id_dis=" + id_dis
                $('#produk_majo').load(url);
            });

            $('#search_majoo').keyup(function() {
                var keyword = $("#search_majoo").val();
                if (keyword != '') {
                    $('#result_majo').show();
                    $('#produk_majo').hide();
                    load_majo(keyword);
                } else {
                    $('#result_majo').hide();
                    $('#produk_majo').show();
                }

            });
            function load_majo(keyword) {
                    $.ajax({
                        method: "GET",
                        url: "{{ route('search_majo') }}",
                        data: {
                            keyword: keyword,
                            dis: dis

                        },
                        success: function(hasil) {
                            $('#result_majo').html(hasil);
                        }
                    });
                }
            $(document).on('click', '.stok_habis', function(event) {
                Swal.fire({
                  toast: true,
                  position: 'top-end',
                  showConfirmButton: false,
                  timer: 3000,
                  icon: 'error',
                  title: ' Stok habis'
                });
            });

            $(document).on('click', '.input_cart3', function() {
                    var id_produk = $(this).attr("id_produk");


                    // console.log(id_harga);
                    $.ajax({
                        url: "{{ route('item_menu_majoo') }}",
                        method: "GET",
                        data: {
                            id_produk: id_produk,
                        },
                        success: function(data) {
                            $('#harga_majoo').html(data);
                            // alert(data);
                        }
                    });

                    $.ajax({
                        url: "{{route('get_karyawan_majo')}}",
                        method: "GET",
                        success: function(data) {
                        $('.buying-selling-group').html(data);

                    }
                });

            });

            load_cart();

                function load_cart() {
                    var dis2 = $("#dis2").val();
                    $.ajax({
                        method: "GET",
                        url: "{{ route('keranjang') }}?dis=" + dis2,
                        success: function(hasil) {
                            $('#keranjang').html(hasil);
                        }
                    });
                }



            $(document).on('submit', '.input_cart_majo', function(event) {
              event.preventDefault();
              var id = $("#cart_id").val();
              var jumlah = $("#cart_jumlah").val();
              var satuan = $("#cart_satuan").val();
              var catatan = $("#cart_catatan").val();
                //   var kd_karyawan = $('.cart_id_karyawan').val();
                var kode = []
              var kd_karyawan = $('input[name^="kd_karyawan"]:checked').each(function() {
                    kode.push($(this).val())

                });
              $.ajax({
                url: "{{route('cart_majoo')}}",
                method: 'get',
                data:{
                    id : id,
                    jumlah : jumlah,
                    satuan : satuan,
                    catatan : catatan,
                    kd_karyawan : kode,
                },
                success: function(data) {
                  if (data == 'kosong') {
                    Swal.fire({
                      toast: true,
                      position: 'top-end',
                      showConfirmButton: false,
                      timer: 3000,
                      icon: 'error',
                      title: 'Stok tidak cukup'
                    });
                  }else if (data == 'null'){
                    Swal.fire({
                      toast: true,
                      position: 'top-end',
                      showConfirmButton: false,
                      timer: 3000,
                      icon: 'error',
                      title: 'Data penjual belum diisi'
                    });
                  }else{
                    $('#cart_session').html(data);
                    $('.modal-cart').modal('hide');
                    load_cart();
                  }

                }
              });
            });
        })
    </script>
@endsection
