<!-- Main Footer -->
<footer class="main-footer">
    <strong>Copyright &copy; Upperclass 2022. </strong>
    All rights reserved.
</footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<!-- jQuery -->
<script src="{{ asset_custom('') }}/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="{{ asset_custom('') }}/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>
<script src="{{ asset_custom('') }}/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="{{ asset_custom('') }}/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="{{ asset_custom('') }}/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="{{ asset_custom('') }}/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="{{ asset_custom('') }}/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="{{ asset_custom('') }}/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="{{ asset_custom('') }}/plugins/jszip/jszip.min.js"></script>
<script src="{{ asset_custom('') }}/plugins/pdfmake/pdfmake.min.js"></script>
<script src="{{ asset_custom('') }}/plugins/pdfmake/vfs_fonts.js"></script>
<script src="{{ asset_custom('') }}/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="{{ asset_custom('') }}/plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="{{ asset_custom('') }}/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

<script src="{{ asset_custom('') }}/plugins/sweetalert2/sweetalert2.min.js"></script>
<!-- AdminLTE App -->
<script src="{{ asset_custom('') }}/dist/js/adminlte.min.js"></script>
<script src="{{ asset_custom('') }}/plugins/select2/js/select2.full.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{ asset_custom('') }}/dist/js/demo.js"></script>
<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap-switch-button@1.1.0/dist/bootstrap-switch-button.min.js">
</script>
<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>
<script>
    <script>
            $(function() {
                $('#example2').DataTable({
                    "paging": true,
                    "lengthChange": false,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false,
                    "responsive": true,
                });

                

                //Initialize Select2 Elements
                $('.select2').select2()
                $(function() {
        $('.select').select2()
        $('.select').one('select2:open', function(e) {
            $('input.select2-search__field').prop('placeholder', 'Search...');
        });
    })
                
            });

</script>
@yield('script')
</body>

</html>
