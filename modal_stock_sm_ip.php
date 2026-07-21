<div class="modal fade"
     id="modalDetailStock"
     tabindex="-1"
     role="dialog"
     aria-labelledby="modalDetailStockLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">

        <div class="modal-content">

            <!-- HEADER -->
            <div class="modal-header bg-primary d-flex justify-content-between align-items-center">

                <h5 class="modal-title mb-0"
                    id="modalDetailStockLabel">
                    Detail Stock
                </h5>

                <div class="d-flex align-items-center">
                    <a id="btnExportDetail"
                       href="#"
                       target="_blank"
                       class="btn btn-success btn-sm mr-2">
                        <i class="fas fa-file-excel"></i>
                        Download Excel
                    </a>

                    <button
                        type="button"
                        class="close text-white"
                        data-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>

            <!-- BODY -->
            <div class="modal-body p-2"
                 id="modalDetailBody">

                <div class="text-center p-4 text-muted">
                    Loading...
                </div>
            </div>
        </div>
    </div>
</div>