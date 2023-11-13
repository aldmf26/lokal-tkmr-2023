@php
    $ttl = 0;
    $ttl2 = 0;
    $sub_total = 0;
    $sub_total2 = 0;
    foreach (Cart::content() as $c):
        if ($c->options->program == 'resto') {
            $ttl += $c->qty;
            $sub_total += $c->qty * $c->price;
        } else {
            $ttl2 += $c->qty;
            $sub_total2 += $c->qty * $c->price;
        }
    endforeach;
@endphp
<?php if($ttl + $ttl2 == 0):?>

<div class="col-lg-12">
    <div class="cart-table">
        <div class="cart-table-warp">
            <center>
                <img width="150" src="{{ asset('public/assets') }}/img_menu/shopping-cart.png" alt=""><br><br>
                <h4>Keranjang Belanja Kosong</h4>
            </center>
            <br><br>
        </div>
    </div>
</div>
<button type="submit" class="btn btn-success bg-gradient btn-block " disabled> SEND TO KITCHEN</button>
<?php else:?>
<input type="hidden" value="{{ $id_distri->id_distribusi }}">
<input type="hidden" value="{{ number_format($batas->rupiah, 0) }}">
<div class="row">
    <style>
        .hargaPotongan {
            text-decoration: line-through;
        }
    </style>
    <div class="col-lg-12">
        <div class="table-responsive">
            <table class="table">
                @if ($ttl == 0)
                @else
                    <tr>
                        <td colspan="4" align="center">
                            <dt>Resto</dt>
                        </td>
                    </tr>
                @endif
                @foreach ($cart as $c)
                    @if ($c->options->program == 'resto')
                        <tr>

                            <td>
                                <strong>{{ $c->name }}
                                    <br>
                                    * {{ $c->options->req }}
                                    {{-- <input type="text" name="req2[]" value="{{ $c->options->req }}" class="form-control">
                            --}}
                                    <input type="hidden" name="rowid[]" value="{{ $c->rowId }}"
                                        class="form-control">
                                    <input type="hidden" name="program[]" value="{{ $c->options->program }}"
                                        class="form-control">
                                </strong>
                            </td>
                            
                            <td>
                                
                                <strong class="{{ $c->options->potongan > 0 ? 'hargaPotongan' : '' }}">
                                    {{ number_format($c->options->hargaNormal, 0) }}
                                </strong>
                                @if ($c->options->potongan > 0)
                                    <strong>
                                        {{ number_format($c->price , 0) }}
                                    </strong>
                                @endif
                            </td>
                            <td align="center">
                                @php

                                    $limit = DB::table('tb_limit')
                                        ->select('tb_limit.jml_limit')
                                        ->join('tb_menu', 'tb_limit.id_menu', 'tb_menu.id_menu')
                                        ->where('tb_limit.id_menu', $c->options->id_menu)
                                        ->first();
                                    // dd($id_menu);
                                @endphp
                                <div class="wrap-num-product flex-w m-l-auto m-r-0">
                                    <a class="min_cart mr-3 btn-num-product-down" id="{{ $c->rowId }}"
                                        qty="{{ $c->qty }}">
                                        <i class="fa fa-minus"></i></a>
                                    <input type="text" value="{{ $c->qty }}" class="text-center "
                                        style="width: 35px;border: 1px solid #D7D7D7;">

                                    <?php if($limit){ ?>
                                    <?php if($c->qty == $limit->jml_limit){ ?>

                                    <?php }else { ?>

                                    <a class="plus_cart btn-num-product-up ml-3" id="{{ $c->rowId }}"
                                        qty="{{ $c->qty }}" id_menu="{{ $c->id }}"><i
                                            class="fa fa-plus"></i></a>
                                    <?php } ?>
                                    <?php }else{ ?>
                                    <a class="plus_cart btn-num-product-up ml-3" id="{{ $c->rowId }}"
                                        qty="{{ $c->qty }}" id_menu="{{ $c->id }}"><i
                                            class="fa fa-plus"></i></a>
                                    <?php } ?>
                                </div>

                            </td>

                            <td>
                                <a href="javascript:void(0)" id="{{ $c->rowId }}" nama="{{ $c->name }}"
                                    id_menu="{{ $c->options->id_menu }}" id_kategori="{{ $c->options->id_kategori }}"
                                    class="btn btn-danger btn-sm delete_cart"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td colspan="4">
                                @foreach ($c->options->nm_karyawan as $key => $nm_karyawan)
                                    @foreach ($nm_karyawan as $k)
                                        <span class="badge badge-secondary">{{ $k }}</span>
                                    @endforeach
                                @endforeach
                            </td>

                        </tr>
                        <tr>

                            <td>
                                <strong>{{ $c->name }}
                                    <br>
                                    * {{ $c->options->req }}
                                    {{-- <input type="text" name="req2[]" value="{{ $c->options->req }}" class="form-control">
                            --}}
                                    <input type="hidden" name="rowid[]" value="{{ $c->rowId }}"
                                        class="form-control">
                                    <input type="hidden" name="program[]" value="{{ $c->options->program }}"
                                        class="form-control">
                                </strong>
                            </td>
                            <td>
                                <strong>
                                    {{ number_format($c->price, 0) }}
                                </strong>
                            </td>
                            <td align="center">
                                @php

                                    $limit = DB::table('tb_limit')
                                        ->select('tb_limit.jml_limit')
                                        ->join('tb_menu', 'tb_limit.id_menu', 'tb_menu.id_menu')
                                        ->where('tb_limit.id_menu', $c->options->id_menu)
                                        ->first();
                                    // dd($id_menu);
                                @endphp
                                <div class="wrap-num-product flex-w m-l-auto m-r-0">
                                    <a class="min_cart mr-3 btn-num-product-down" id="{{ $c->rowId }}"
                                        qty="{{ $c->qty }}">
                                        <i class="fa fa-minus"></i></a>
                                    <input type="text" value="{{ $c->qty }}" class="text-center "
                                        style="width: 35px;border: 1px solid #D7D7D7;">

                                    <?php if($limit){ ?>
                                    <?php if($c->qty == $limit->jml_limit){ ?>

                                    <?php }else { ?>

                                    <a class="plus_cart btn-num-product-up ml-3" id="{{ $c->rowId }}"
                                        qty="{{ $c->qty }}" id_menu="{{ $c->id }}"><i
                                            class="fa fa-plus"></i></a>
                                    <?php } ?>
                                    <?php }else{ ?>
                                    <a class="plus_cart btn-num-product-up ml-3" id="{{ $c->rowId }}"
                                        qty="{{ $c->qty }}" id_menu="{{ $c->id }}"><i
                                            class="fa fa-plus"></i></a>
                                    <?php } ?>
                                </div>

                            </td>

                            <td>
                                <a href="javascript:void(0)" id="{{ $c->rowId }}" nama="{{ $c->name }}"
                                    id_menu="{{ $c->options->id_menu }}" id_kategori="{{ $c->options->id_kategori }}"
                                    class="btn btn-danger btn-sm delete_cart"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                    @endif
                @endforeach

                <tr>
                    <th style="font-size: 14px;" class="text-left">SUBTOTAL</th>
                    <th style="font-size: 14px;" colspan="3" class="text-right">Rp.
                        {{ number_format($sub_total + $sub_total2, 0) }}
                    </th>
                </tr>
                <tr>
                    <?php if ($id_distri->service == 'Y'):?>
                    <th style="font-size: 14px;" class="text-left">Service Charge</th>
                    @php
                        $service = $sub_total * 0.07;
                    @endphp
                    <th style="font-size: 14px;" colspan="3" class="text-right">Rp.
                        {{ number_format($service, 0) }}</th>
                    <?php else: ?>
                    @php
                        $service = 0;
                    @endphp
                    <?php endif ?>
                </tr>
                <tr>
                    <?php if ($id_distri->ongkir == 'Y'):?>
                    <?php if($sub_total < $batas->rupiah): ?>
                    <th style="font-size: 14px;" class="text-left">ONGKIR</th>
                    <th style="font-size: 14px;" colspan="3" class="text-right">
                        Rp.{{ number_format($onk->rupiah, 0) }} </th>
                    @php
                        $ongkir = $onk->rupiah;
                    @endphp
                    <?php else: ?>
                    <th style="font-size: 14px;" class="text-left">ONGKIR</th>
                    <th style="font-size: 14px;" colspan="3" class="text-right">
                        Free </th>
                    @php
                        $ongkir = 0;
                    @endphp
                    <?php endif ?>
                    <?php else: ?>
                    @php
                        $ongkir = 0;
                    @endphp
                    <?php endif ?>
                </tr>
                <tr>
                    <?php if ($id_distri->tax == 'Y'):?>
                    <th style="font-size: 14px;" class="text-left">Tax</th>
                    @php
                        $tax = ($sub_total + $sub_total2 + $service + $ongkir) * 0.1;
                    @endphp
                    <th style="font-size: 14px;" colspan="3" class="text-right">Rp.
                        {{ number_format($tax, 0) }}</th>
                    <?php else: ?>
                    @php
                        $tax = 0;
                    @endphp
                    <?php endif ?>
                </tr>
                <tr>
                    <th style="font-size: 16px;" class="text-left">TOTAL</th>
                    <?php
                    $total2 = $sub_total + $sub_total2 + $service + $tax + $ongkir;
                    
                    $a = round($total2);
                    $b = number_format(substr($a, -3), 0);
                    
                    if ($b == '000') {
                        $c = $a;
                        $round = '000';
                    } elseif ($b < 1000) {
                        $c = $a - $b + 1000;
                        $round = 1000 - $b;
                    }
                    ?>
                    <th style="font-size: 16px;" colspan="3" class="text-right">Rp.
                        {{ number_format($c) }}</th>
                </tr>










            </table>
            <button type="submit" class="btn btn-success bg-gradient btn-block">SEND TO KITCHEN</button>
        </div>
    </div>
</div>
<?php endif?>
