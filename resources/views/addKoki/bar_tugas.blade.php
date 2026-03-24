    <div class="d-flex mb-3 mt-2" style="gap: 20px; font-weight: bold; background: #fff; padding: 10px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
        <div class="d-flex align-items-center">
            <div style="width: 20px; height: 20px; background-color: #007bff; border-radius: 4px; margin-right: 8px;"></div>
            <span>DINE IN</span>
        </div>
        <div class="d-flex align-items-center">
            <div style="width: 20px; height: 20px; background-color: #28a745; border-radius: 4px; margin-right: 8px;"></div>
            <span>GOJEK / GRAB</span>
        </div>
    </div>
    <table class="table" width="100%">
        <thead>
            <tr class="header">
                <th class="sticky-top th-atas">Meja</th>
                <th class="sticky-top th-atas">Menu</th>
                <th class="sticky-top th-atas">Request</th>
                <th class="sticky-top th-atas">Qty</th>
                <th class="sticky-top th-atas">Status</th>
                <th class="sticky-top th-atas">Time In</th>
            </tr>
        </thead>
        <tbody style="font-size: 18px;">
            @foreach ($meja as $m)
                @php
                    $bgColor = ($m->id_distribusi == 1) ? 'bg-primary' : 'bg-success';
                    $prefix = 'Meja';
                    if ($m->id_distribusi == 2) $prefix = 'Gojek';
                    if ($m->id_distribusi == 3) $prefix = 'Grab';

                    $menus = $menus_all[$m->no_order] ?? [];
                @endphp
                <tr class="header">
                    <td class="{{ $bgColor }}">
                        {{ $prefix }} {{ $m->nm_meja }}
                    </td>
                    <td class="{{ $bgColor }}" style="vertical-align: middle;">
                        <a class="muncul muncul{{ $m->id_meja }} btn btn-light btn-sm" style="font-weight: bold; color: #2c3e50 !important;" id_meja="{{ $m->id_meja }}"
                            no_order="{{ $m->no_order }}">View Selesai</a>
                        <a class="hilang hilang{{ $m->id_meja }} btn btn-light btn-sm" style="display:none; font-weight: bold; color: #2c3e50 !important;" id_meja="{{ $m->id_meja }}">Hide Selesai</a>
                    </td>
                    <td class="{{ $bgColor }}"></td>
                    <td class="{{ $bgColor }}"></td>
                    <td class="{{ $bgColor }}"></td>
                    <td colspan="50" class="{{ $bgColor }}"></td>
                </tr>

                <tbody class="load_menu_s{{ $m->id_meja }}"></tbody>
                <tbody class="addmeja{{ $m->id_meja }}"></tbody>

                @foreach ($menus as $menu)
                    <tr class="header">
                        <td></td>
                        <td style="white-space:nowrap;text-transform: lowercase;">
                            {{ $menu->nm_menu }}
                            <br>
                            <small class="text-danger font-weight-bold">Total: {{ $menu->ttlMenuSemua }}</small>
                        </td>
                        <td>
                            <div class="text-primary font-weight-bold" style="font-size: 0.8rem;">
                                {{ $menu->request }}
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-warning" style="font-size: 1.1rem;">{{ $menu->qty }}</span>
                            @if($menu->other_tables)
                            <div class="mt-1">
                                <small class="text-muted" style="display: block; line-height: 1;">Meja Lain:</small>
                                <small class="text-success font-weight-bold">{{ $menu->other_tables }}</small>
                            </div>
                            @endif
                        </td>
                        <td>
                            <a kode="{{ $menu->id_order }}" class="btn btn-info btn-sm selesai"
                                id_meja="{{ $m->id_meja }}"><i class="fas fa-check"></i> Selesai</a>
                        </td>
                        <td style="font-weight: bold;">
                            {{ date('H:i', strtotime($menu->j_mulai)) }}
                        </td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
