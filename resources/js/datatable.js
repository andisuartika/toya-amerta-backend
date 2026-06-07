import DataTable from 'datatables.net-bs5';
import 'datatables.net-responsive-bs5';
import 'datatables.net-bs5/css/dataTables.bootstrap5.css';
import 'datatables.net-responsive-bs5/css/responsive.bootstrap5.css';

// DOM: header area (length + search) → table → footer area (info + pagination)
DataTable.defaults.dom =
    "<'dt-header'<'dt-length'l><'dt-search'f>>" +
    "<'row'<'col-12 p-0'tr>>" +
    "<'dt-footer'<'dt-info'i><'dt-paginate'p>>";

// Bahasa Indonesia
DataTable.defaults.language = {
    processing:     '<span class="spinner-border spinner-border-sm text-primary me-2" role="status"></span>Memuat...',
    search:         '',
    searchPlaceholder: 'Cari data...',
    lengthMenu:     'Tampilkan _MENU_ data',
    info:           'Menampilkan _START_–_END_ dari _TOTAL_ data',
    infoEmpty:      'Tidak ada data',
    infoFiltered:   '(difilter dari _MAX_ total)',
    zeroRecords:    '<div class="py-4 text-center text-muted">' +
                        '<iconify-icon icon="solar:inbox-out-bold-duotone" class="fs-32 d-block mb-2 opacity-40"></iconify-icon>' +
                        'Tidak ada data yang cocok' +
                    '</div>',
    emptyTable:     '<div class="py-4 text-center text-muted">' +
                        '<iconify-icon icon="solar:inbox-out-bold-duotone" class="fs-32 d-block mb-2 opacity-40"></iconify-icon>' +
                        'Belum ada data' +
                    '</div>',
    paginate: {
        first:    '«',
        previous: '‹',
        next:     '›',
        last:     '»',
    },
};

DataTable.defaults.pageLength = 15;
DataTable.defaults.lengthMenu = [[10, 15, 25, 50, -1], ['10', '15', '25', '50', 'Semua']];
DataTable.defaults.processing = true;
DataTable.defaults.serverSide = true;
DataTable.defaults.responsive = true;
DataTable.defaults.autoWidth  = false;
DataTable.defaults.order      = [];

window.DataTable = DataTable;
