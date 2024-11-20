"use strict";
(function($) {
    "use strict";
    var apiEnd = $('#product-table').data('url');
    $('#product-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: apiEnd,
        columns: [
           { data: 'image' },
           { data: 'name', name: 'name' },
           { data: 'pricing', name: 'price' },
           { data: 'stock', name: 'stock_count' },
           { data: 'actions' },
        ],
        order: [
        //    [1, 'desc']
        ],
    })
})(jQuery);