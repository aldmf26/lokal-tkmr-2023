<!-- Main Sidebar Container -->
<aside class="main-sidebar elevation-4">
    @php
    $id_lokasi = Request::get('acc');
    @endphp
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link">
        <img src="{{ asset_custom('') }}{{$id_lokasi == 1 ? '/menu/img/Takemori.svg' : '/menu/img/soondobu.jpg'}}"
            alt="AdminLTE Logo" class="brand-image image-center elevation-3" style="opacity: .8">
        <h5 class="text-block text-white text-md">{{$id_lokasi == 1 ? 'Accounting Takemori' : 'Accounting Soondobu'}}
        </h5>
    </a>

    <hr>

    <!-- Sidebar -->
    <div class="sidebar">

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{ route('dashboard', ['acc' => $id_lokasi]) }}" class="nav-link  ">
                        <i class="
                        nav-icon fas fa-tachometer-alt text-white"></i>
                        <p class="text-white">Dashboard</p>
                    </a>
                </li>
                <li class="nav-item ">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-file-invoice text-white"></i>
                        <p class="text-white">
                            Accounting
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="display: none;">
                        <li class="nav-item">
                            <a href="{{ route('akun', ['acc' => $id_lokasi]) }}"
                                class="nav-link {{Request::is('akun') ? 'active' : ''}}">
                                <i class="far fa-circle nav-icon text-white"></i>
                                <p class="text-white">Akun</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('jPemasukan', ['acc' => $id_lokasi]) }}"
                                class="nav-link {{Request::is('jPemasukan') ? 'active' : ''}}">
                                <i class="far fa-circle nav-icon text-white"></i>
                                <p class="text-white">Jurnal Pemasukan</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('jPengeluaran', ['acc' => $id_lokasi]) }}"
                                class="nav-link {{Request::is('jPengeluaran') ? 'active' : ''}}">
                                <i class="far fa-circle nav-icon text-white"></i>
                                <p class="text-white">Jurnal Pengeluaran</p>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
