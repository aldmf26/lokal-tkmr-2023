<style>
    .table tr:not(.header) {
        display: none;
    }
</style>




<script src="{{ asset_custom('') }}/plugins/jquery/jquery.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>
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
</script>
