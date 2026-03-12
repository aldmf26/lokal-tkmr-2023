<div class="row">
    @foreach ($karyawan as $k)
    <div class="col-lg-2 col-4">
        <label class="btn btn-default buying-selling">
            <div class="checkbox-group required">
                <input type="checkbox" name="kd_karyawan" value="{{$k->kd_karyawan }}" autocomplete="off"
                    class="cart_id_karyawan option1">
            </div>
            <span class="radio-dot"></span>
            <span class="buying-selling-word">{{$k->nm_karyawan}}</span>
        </label>
    </div>
    @endforeach
</div>
