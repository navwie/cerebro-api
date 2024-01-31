@extends('layouts.base')

@section('content')

    <div class="container-fluid">
        <div class="fade-in">
            <div class="card">
                <div class="card-header">&nbsp;</div>
                <div class="card-body">
                    <table class="table" id="main-statistic-table" style="width: 100%">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Clicks</th>
                            <th>Total leads</th>
                            <th>Sold leads</th>
                            <th>EPC</th>
                            <th>EPL</th>
                            <th>Income</th>
                            <th>Redirect</th>
                            <th>Payout</th>
                            <th>Revenue</th>
                            <th>Profit</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')

    <script type="module">
        'use strict';
        $(document).ready(function () {

            $('#main-statistic-table thead th').each(function (i) {
                if (i !== 0) {
                    return true;
                }
                let title = $(this).text();
                $(this).html('<input class="form-control individualSearch" type="text" placeholder="' + title + '" />');

            });
            //prevent order on search field click
            $('input.individualSearch', this).on('click', function () {
                return false;
            });

            $('#main-statistic-table').dataTable({
                initComplete: function () {
                    // Apply the search
                    this.api()
                        .columns()
                        .every(function () {
                            let that = this;
                            $('input', this.header()).val(this.search());
                            $('input', this.header()).on('keyup change clear', function (e) {

                                if (that.search() !== this.value) {
                                    that.search(this.value).draw();
                                }
                            });
                        });
                },
                lengthMenu: [5, 10, 20, 50, 100, 250, 500],
                pageLength: 10,
                dom: 'lBrtip',
                buttons: [
                    'copyHtml5',
                    'excelHtml5',
                    'csvHtml5',
                    'pdfHtml5'
                ],
                processing: true,
                serverSide: true,
                stateSave: true,
                ajax: {
                    url: '{{url('main-statistic')}}',
                    type: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                    }
                },
                columnDefs: [
                    {searchable: false, targets: [1, 2, 3, 4, 5, 6, 7, 8, 9]},
                    {sortable: false, targets: [8, 9, 10]},
                    {visible: false, targets: []}
                ],
                scroller: {
                    loadingIndicator: true
                },
            });
        });
    </script>
@endpush

