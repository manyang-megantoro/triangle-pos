<!-- Modal -->
<div class="modal fade" id="importerModal" tabindex="-1" role="dialog" aria-labelledby="importerModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="importerModalLabel">{{ __('Import') }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <form id="upload_csv" method="post" target="_blank" enctype="multipart/form-data" action="/api/migrator/import">
                <div class="col-md-4">
                    <input type="file" name="file_import" id="file_import" accept=".csv,.xlsx" style="margin-top:15px;" />
                </div>
                <input type="hidden" name="class_name" id="class_name">
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
            <button type="button" class="btn btn-primary btn-migrator-importer-submit">{{ __('Upload') }}</button>
        </div>
        </div>
    </div>
</div>
@push('page_scripts')
    <script>
        $( document ).ready(function() {
            $(document).on("click",".btn-migrator-importer",function() {
                $("#upload_csv").trigger("reset");
                $("#upload_csv #class_name").val($(this).data('classname'));
                $('#importerModal').modal('show');
            });
            $('#upload_csv').on('submit', function(event){
                $('#importerModal').modal('hide');
                // event.preventDefault();
                // $.ajax({
                //     url:"{{ route('api.migrator.import') }}",
                //     method:"POST",
                //     data:new FormData(this),
                //     dataType:'json',
                //     contentType:false,
                //     cache:false,
                //     processData:false,
                //     success:function(jsonData)
                //     {
                //         alert('Import Success');
                //         // $('#file_import').val('');
                //         // $('#data-table').DataTable(jsonData);
                //     }
                // });
            });
            $(document).on("click",".btn-migrator-importer",function() {
                $('#importerModal').modal('show')
            });

            $(document).on("click",".btn-migrator-importer-submit",function() {
                $( "#upload_csv" ).trigger( "submit" );
            });
        });
    </script>
@endpush
