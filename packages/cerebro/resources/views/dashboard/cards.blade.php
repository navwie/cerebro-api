@extends('layouts.base')

@section('content')
    <div class="container-fluid">
        <div class="fade-in">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header"><strong>Filter</strong></div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xs-1 col-md-2">
                                    <div class="form-group">
                                        <label for="to-date">From</label>
                                        <div class="input-group date datepicker">
                                            <input class="form-control" name="from_date" id="from-date" type="text"
                                                   value="{!! date('d/m/Y') !!} "/>
                                            <span class="input-group-append">
                                              <span class="input-group-text bg-light d-block">
                                                <i class="fa fa-calendar"></i>
                                              </span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-1 col-md-2">
                                    <div class="form-group">
                                        <label for="to-date">To</label>
                                        <div class="input-group date datepicker">
                                            <input class="form-control" name="to_date" id="to-date" type="text"
                                                   value="{!! date('d/m/Y') !!}"/>
                                            <span class="input-group-append">
                                              <span class="input-group-text bg-light d-block">
                                                <i class="fa fa-calendar"></i>
                                              </span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-1 col-md-2">
                                    <div class="form-group">
                                        <label for="period">Period</label>
                                        <select class="form-control" id="period">
                                            <option data-from="{!! date('d/m/Y') !!}" data-to="{!! date('d/m/Y') !!}">
                                                Today
                                            </option>
                                            <option data-from="{!! date('d/m/Y', strtotime('-1 day')) !!}"
                                                    data-to="{!! date('d/m/Y', strtotime('-1 day')) !!}">Yesterday
                                            </option>
                                            <option
                                                data-from="{!! date('d/m/Y', strtotime('-' . date('w') . ' days')) !!}"
                                                data-to="{!! date('d/m/Y', strtotime('+' . (6 - date('w')) . ' days')) !!}">
                                                This week
                                            </option>
                                            <option
                                                data-from="{!! date('d/m/Y', strtotime('-' . (date('w') + 7) . ' days')) !!}"
                                                data-to="{!! date('d/m/Y', strtotime('-' . (date('w') + 1) . ' days')) !!}">
                                                Last week
                                            </option>
                                            <option data-from="{!! date('01/m/Y') !!}"
                                                    data-to="{!! date('d/m/Y', strtotime('last day of this month')) !!}">
                                                This month
                                            </option>
                                            <option
                                                data-from="{!! date('d/m/Y', strtotime('first day of previous month')) !!}"
                                                data-to="{!! date('d/m/Y', strtotime('last day of previous month')) !!}">
                                                Last month
                                            </option>
                                            <option
                                                data-from="{!! date('d/m/Y', strtotime('first day of january this year')) !!}"
                                                data-to="{!! date('d/m/Y', strtotime('last day of december this year')) !!}">
                                                This year
                                            </option>
                                            <option
                                                data-from="{!! date('d/m/Y', strtotime('first day of january previous year')) !!}"
                                                data-to="{!! date('d/m/Y', strtotime('last day of december previous year')) !!}">
                                                Last year
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xs-1 col-md-2">

                                </div>
                                @if(auth()->user()->hasRole('admin'))
                                    <div class="col-xs-1 col-md-3">
                                        <div class="form-group">
                                            <label for="form">Form</label>
                                            <select class="form-control" id="form">
                                                <option value=""> -- Select form --</option>
                                                @foreach ($forms as $form)
                                                    <option value="{{$form->id}}">
                                                        {{$form->name}}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @else
                                    <div class="col-xs-1 col-md-3">

                                    </div>
                                @endif
                                <div class="col-xs-12 col-md-1 d-flex align-items-center mt-2">
                                    <button id="refresh-dashboard" class="btn btn-block btn-outline-dark"  type="button">
                                        <i class="fa-solid fa-arrows-rotate"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 col-lg-4">
                    <div class="card">
                        <div class="card-header content-center">
                            <h2>Clicks</h2>
                            <div class="c-chart-wrapper">
                                <canvas id="social-box-chart-1" height="90"></canvas>
                            </div>
                        </div>
                        <div class="card-body row text-center">
                            <div class="col">
                                <div class="text-value-xl"><span id="total-clicks-period-value">0</span></div>
                                <div class="text-uppercase text-muted small">Clicks</div>
                            </div>
                            <div class="c-vr ps-0 pe-0"></div>
                            <div class="col">
                                <div class="text-value-xl"><span id="uniq-clicks-period-value">0</span></div>
                                <div class="text-uppercase text-muted small">Unique clicks</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <div class="card">
                        <div class="card-header content-center">
                            <h2>Visits to submits</h2>
                            <div class="c-chart-wrapper">
                                <canvas id="social-box-chart-2" height="90"></canvas>
                            </div>
                        </div>
                        <div class="card-body row text-center">
                            <div class="col">
                                <div class="text-value-xl"><span id="uniq-visits-in-period-value">0</span></div>
                                <div class="text-uppercase text-muted small">Visits</div>
                            </div>
                            <div class="c-vr ps-0 pe-0"></div>
                            <div class="col">
                                <div class="text-value-xl"><span id="submits-in-period-value">0</span></div>
                                <div class="text-uppercase text-muted small">Submits</div>
                            </div>
                            <div class="c-vr ps-0 pe-0"></div>
                            <div class="col">
                                <div class="text-value-xl"><span id="uniq-visits-to-submit-value">0</span>%</div>
                                <div class="text-uppercase text-muted small">Visits to Submits</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <div class="card">
                        <div class="card-header content-center">
                            <h2>Short statistic</h2>
                            <div class="c-chart-wrapper">
                                <canvas id="social-box-chart-3" height="90"></canvas>
                            </div>
                        </div>
                        <div class="card-body row text-center">
                            <div class="col">
                                <div class="text-value-xl"><span id="unique-clicks-value">0</span></div>
                                <div class="text-uppercase text-muted small">Total unique clicks</div>
                            </div>
                            <div class="c-vr ps-0 pe-0"></div>
                            <div class="col">
                                <div class="text-value-xl"><span id="total-clicks-value">0</span></div>
                                <div class="text-uppercase text-muted small">Total clicks</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">&nbsp;</div>
                <div class="card-body">
                    <table class="table" id="card-clicks-table" style="width: 100%">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Domain</th>
                            <th>Total clicks</th>
                            <th>Uniq clicks</th>
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

        document.addEventListener('DOMContentLoaded', () => {
            new TempusDominus(document.getElementById('to-date'), {
                localization: {
                    format: 'dd/MM/yyyy',
                },
                display: {
                    components: {
                        decades: true,
                        year: true,
                        month: true,
                        date: true,
                        hours: false,
                        minutes: false,
                        seconds: false,
                    }
                }
            });

            new TempusDominus(document.getElementById('from-date'), {
                localization: {
                    format: 'dd/MM/yyyy',
                },
                display: {
                    components: {
                        decades: true,
                        year: true,
                        month: true,
                        date: true,
                        hours: false,
                        minutes: false,
                        seconds: false,
                    }
                }
            });

            document.querySelector('#refresh-dashboard').addEventListener('click', event => {
                dashboardRequest();
            });

            document.querySelector('#period').addEventListener('change', event => {
                let from = event.target.options[event.target.selectedIndex].dataset.from;
                let to = event.target.options[event.target.selectedIndex].dataset.to;
                document.querySelector("#from-date").value = from;
                document.querySelector("#to-date").value = to;
            });
            dashboardRequest();
        });

        const initTable = (data) => {
            if($.fn.DataTable.isDataTable( '#card-clicks-table' )) {
                let table = $('#card-clicks-table').dataTable();
                table.fnClearTable();
                table.api().rows.add( data );
                table.api().columns()
                    .every(function () {
                        let that = this;
                        $('input', this.header()).val(this.search());
                        $('input', this.header()).on('keyup change clear', function (e) {
                            if (that.search() !== this.value) {
                                that.search(this.value).draw();
                            }
                        });
                    }).draw();
            } else{
                $('#card-clicks-table thead th').each(function (i) {
                    if (i > 1) {
                        return true;
                    }
                    let title = $(this).text();
                    $(this).html('<input class="form-control individualSearch" type="text" placeholder="' + title + '" />');
                });
                //prevent order on search field click
                $('input.individualSearch', this).on('click', function () {
                    return false;
                });
                $('#card-clicks-table').dataTable({
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
                    processing: true,
                    stateSave: true,
                    columnDefs: [
                        {sortable: true, targets: [0,1,2,3]},
                    ],
                    scroller: {
                        loadingIndicator: true
                    },
                    data: data,
                    columns: [
                        { data: 'name' },
                        { data: 'domain_name' ,
                            "fnCreatedCell": function (nTd, sData) {
                                $(nTd).html("<a href='http://" + sData + "' target='_blank'>" + sData + "</a>");
                            }},
                        { data: 'total_clicks' },
                        { data: 'uniq_clicks' }
                    ]
                });
            }
        }

        const dashboardRequest = () => {
            let start = document.querySelector('#from-date').value;
            let stop = document.querySelector('#to-date').value;

            $.ajax('/get_cards', {
                dataType: 'json',
                type: 'GET',
                data: {
                    start: start,
                    stop: stop,
                    formId: document.querySelector('#form').value
                },
                success: function (response) {
                    document.querySelector('#uniq-clicks-period-value').textContent = response.uniqueClicksPeriod;
                    document.querySelector('#total-clicks-period-value').textContent = response.totalClicksPeriod;
                    document.querySelector('#uniq-visits-in-period-value').textContent = response.visits;
                    document.querySelector('#submits-in-period-value').textContent = response.submits;
                    document.querySelector('#uniq-visits-to-submit-value').textContent = response.visitsToSubmits;
                    document.querySelector('#unique-clicks-value').textContent = response.uniqueClicks;
                    document.querySelector('#total-clicks-value').textContent = response.totalClicks;
                    initTable(response.items);
                },
                error: function (jqXhr, textStatus, errorMessage) {
                    $('p').append('Error: ' + errorMessage);
                }
            });
        };
    </script>
@endpush
