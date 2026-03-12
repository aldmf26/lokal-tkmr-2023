<body class="hold-transition layout-top-nav">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand-md navbar-light navbar-white"
            style=" background:#197dab; color: #787878;">
            <div class="container">
                @php
                if (Session::get('id_lokasi') == 1) {
                $gambar = 'Takemori_new.jpg';
                $h5 = 'TAKEMORI';
                } elseif (Session::get('id_lokasi') == 2) {
                $gambar = 'soondobu.jpg';
                $h5 = 'SOONDOBU';
                } else {
                $gambar = 'user copy.png';
                $h5 = 'ADMINISTRATOR';
                }
                @endphp
                <img src="{{ asset_custom('') }}/pages/login/img/{{ $gambar }}" alt="AdminLTE Logo"
                    class="brand-image img-circle elevation-3" style="opacity: .8"> &nbsp;
                <h5 style="font-weight: bold; color:white">{{ $h5 }}</h5>
                <button class="btn btn-success ml-3" id="btnCekBill" data-toggle="modal" data-target="#modalCekBill">
                    <i class="fas fa-file-invoice"></i> Cek Bill
                </button>

                <button class="order-1 navbar-toggler first-button" type="button" data-toggle="collapse"
                    data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <div class="animated-icon1"><span></span><span></span><span></span></div>
                </button>

                <div class="collapse navbar-collapse order-3" id="navbarCollapse">
                    <ul class="navbar-nav">


                    </ul>
                </div>

                <!-- Right navbar links -->
                <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-light" href="#" id="navbarDropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ @Auth::user()->nama }} <i class="fas fa-user"></i>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink"
                            style="left: 0px; right: inherit;">
                            <a class="dropdown-item" href="#">Ganti Password</a>
                            <a class="dropdown-item" href="{{ route('logout' . @$logout) }}">Logout</a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>

<!-- Modal Cek Bill -->
<div class="modal fade" id="modalCekBill" tabindex="-1" role="dialog" aria-labelledby="modalCekBillLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document" style="max-width: 500px;">
        <div class="modal-content">
            <div class="modal-header" style="background: #197dab; color: white;">
                <h5 class="modal-title" id="modalCekBillLabel"><i class="fas fa-file-invoice"></i> Daftar Bill Aktif</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="p-3" style="border-bottom: 1px solid #dee2e6;">
                <input type="text" id="searchBill" class="form-control" placeholder="🔍 Cari meja, no invoice, atau distribusi..." autocomplete="off">
            </div>
            <div class="modal-body p-0" style="max-height: 65vh; overflow-y: auto;">
                <div id="listBillContainer" style="min-height: 100px;">
                    <div class="text-center p-4">
                        <div class="spinner-border text-info" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="text-muted mt-2">Memuat data...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <small class="text-muted" id="billCount"></small>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
var billData = [];

function formatRupiah(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function renderBillList(data) {
    var container = document.getElementById('listBillContainer');
    var countEl = document.getElementById('billCount');

    if (data.length === 0) {
        container.innerHTML =
            '<div class="text-center p-4">' +
            '<i class="fas fa-inbox" style="font-size: 40px; color: #ccc;"></i>' +
            '<p class="text-muted mt-2">Tidak ada bill ditemukan</p>' +
            '</div>';
        countEl.textContent = '0 bill';
        return;
    }

    var html = '';
    for (var i = 0; i < data.length; i++) {
        var m = data[i];
        html += '<a href="' + m.url + '" target="_blank" class="list-group-item list-group-item-action" style="border-left: 4px solid #00A549; padding: 12px 15px;">';
        html += '<div class="d-flex justify-content-between align-items-start mb-1">';
        html += '<strong style="font-size: 16px; color: #197dab;">Meja ' + m.nm_meja + '</strong>';
        html += '<span class="badge" style="background: #00A549; color: white;">' + m.total_qty + ' item</span>';
        html += '</div>';
        html += '<div style="font-size: 13px; color: #555;">';
        html += '<div class="d-flex justify-content-between">';
        html += '<span><i class="fas fa-file-invoice text-info"></i> ' + m.no_order + '</span>';
        html += '<span><i class="fas fa-clock text-muted"></i> ' + m.waktu + '</span>';
        html += '</div>';
        if (m.dis_name) {
            html += '<div class="mt-1"><i class="fas fa-utensils text-success"></i> ' + m.dis_name + '</div>';
        }
        html += '</div>';
        html += '<hr style="margin: 8px 0;">';
        html += '<div class="d-flex justify-content-between" style="font-size: 13px;">';
        html += '<span class="text-muted">Subtotal</span>';
        html += '<span>Rp ' + formatRupiah(Math.round(m.subtotal)) + '</span>';
        html += '</div>';
        html += '<div class="d-flex justify-content-between" style="font-size: 14px; font-weight: bold;">';
        html += '<span style="color: #197dab;">Grand Total</span>';
        html += '<span style="color: #00A549;">Rp ' + formatRupiah(Math.round(m.grand_total)) + '</span>';
        html += '</div>';
        html += '</a>';
    }

    container.innerHTML = '<div class="list-group list-group-flush">' + html + '</div>';
    countEl.textContent = data.length + ' bill aktif';
}

document.getElementById('btnCekBill').addEventListener('click', function() {
    var container = document.getElementById('listBillContainer');
    var searchInput = document.getElementById('searchBill');
    searchInput.value = '';

    container.innerHTML =
        '<div class="text-center p-4">' +
        '<div class="spinner-border text-info" role="status"><span class="sr-only">Loading...</span></div>' +
        '<p class="text-muted mt-2">Memuat data...</p>' +
        '</div>';

    fetch("{{ route('get_list_bill') }}")
        .then(function(response) { return response.json(); })
        .then(function(data) {
            billData = data;
            renderBillList(billData);
        })
        .catch(function() {
            container.innerHTML =
                '<div class="text-center p-4">' +
                '<i class="fas fa-exclamation-triangle" style="font-size: 40px; color: #dc3545;"></i>' +
                '<p class="text-muted mt-2">Gagal memuat data</p>' +
                '</div>';
        });
});

document.getElementById('searchBill').addEventListener('input', function() {
    var keyword = this.value.toLowerCase();
    if (!keyword) {
        renderBillList(billData);
        return;
    }
    var filtered = billData.filter(function(m) {
        return ('meja ' + m.nm_meja).toLowerCase().indexOf(keyword) !== -1 ||
               m.no_order.toLowerCase().indexOf(keyword) !== -1 ||
               m.dis_name.toLowerCase().indexOf(keyword) !== -1;
    });
    renderBillList(filtered);
});
</script>
