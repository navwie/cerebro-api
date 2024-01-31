@extends('layouts.base')

@section('content')
    <div class="container-fluid">
        <div class="fade-in">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header"><strong>Filter</strong></div>
                        <div class="card-body py-2">
                            <div class="row">
                                <div class="col-xs-1 col-md-3 col-xxl-2">
                                    <div class="form-group">
                                        <label for="to-date">From</label>
                                        <div class="input-group date datepicker">
                                            <input class="form-control" name="from_date" id="from-date" type="text"
                                                   value="{!! date($phpTimeFormat) !!} "/>
                                            <span class="input-group-append">
                                              <span class="input-group-text bg-light d-block">
                                                <i class="fa fa-calendar"></i>
                                              </span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-1 col-md-3 col-xxl-2">
                                    <div class="form-group">
                                        <label for="to-date">To</label>
                                        <div class="input-group date datepicker">
                                            <input class="form-control" name="to_date" id="to-date" type="text"
                                                   value="{!! date($phpTimeFormat) !!}"/>
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
                                            <option data-from="{!! date($phpTimeFormat) !!}" data-to="{!! date($phpTimeFormat) !!}">
                                                Today
                                            </option>
                                            <option data-from="{!! date($phpTimeFormat, strtotime('-1 day')) !!}"
                                                    data-to="{!! date($phpTimeFormat, strtotime('-1 day')) !!}">Yesterday
                                            </option>
                                            <option
                                                data-from="{!! date($phpTimeFormat, strtotime('-' . date('w') . ' days')) !!}"
                                                data-to="{!! date($phpTimeFormat, strtotime('+' . (6 - date('w')) . ' days')) !!}">
                                                This week
                                            </option>
                                            <option
                                                data-from="{!! date($phpTimeFormat, strtotime('-' . (date('w') + 7) . ' days')) !!}"
                                                data-to="{!! date($phpTimeFormat, strtotime('-' . (date('w') + 1) . ' days')) !!}">
                                                Last week
                                            </option>
                                            <option data-from="{!! date('m/01/Y') !!}"
                                                    data-to="{!! date($phpTimeFormat, strtotime('last day of this month')) !!}">
                                                This month
                                            </option>
                                            <option
                                                data-from="{!! date($phpTimeFormat, strtotime('first day of previous month')) !!}"
                                                data-to="{!! date($phpTimeFormat, strtotime('last day of previous month')) !!}">
                                                Last month
                                            </option>
                                            <option
                                                data-from="{!! date($phpTimeFormat, strtotime('first day of january this year')) !!}"
                                                data-to="{!! date($phpTimeFormat, strtotime('last day of december this year')) !!}">
                                                This year
                                            </option>
                                            <option
                                                data-from="{!! date($phpTimeFormat, strtotime('first day of january previous year')) !!}"
                                                data-to="{!! date($phpTimeFormat, strtotime('last day of december previous year')) !!}">
                                                Last year
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xs-1 col-md-2 col-xxl-1 width-125px-xl">
                                    <div class="form-group">
                                        <label for="leadTypeToggle">Lead&nbsp;Type</label><br>
                                        <input name="lead_type" id="leadTypeToggle" type="checkbox" data-toggle="toggle" checked data-style="fast"
                                               data-onstyle="outline-dark" data-offstyle="outline-secondary" tristate>
                                    </div>
                                </div>
                                <div class="col-xs-1 col-md-2 col-xxl-1 width-125px-xl">
                                    <div class="form-group">
                                        <label for="actionTypeToggle">Action&nbsp;Type</label><br>
                                        <input name="lead_type" id="actionTypeToggle" type="checkbox" data-toggle="toggle" data-style="fast"
                                               data-onstyle="outline-dark" data-offstyle="outline-secondary" tristate>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                @if(auth()->user()->hasRole('admin'))
                                    <div class="col-xs-1 col-md-3">
                                        <div class="form-group">
                                            <label for="form">Flow</label>
                                            <select class="form-control" id="flow">
                                                <option value=""> -- Select flow --</option>
                                                @foreach ($flows as $flow)
                                                    <option value="{{$flow->id}}" title="{{$flow->description}}">
                                                        {{$flow->title}}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xs-1 col-md-3">
                                        <div class="form-group">
                                            <label for="form">Form</label>
                                            <select class="form-control" id="form">
                                                <option value=""> -- Select form --</option>
                                                <optgroup label="Themes">
                                                    <option value="lendingsource">LendingSource</option>
                                                    <option value="loan5000">Loan 5000</option>
                                                    <option value="loan10000">Loan 10000</option>
                                                    <option value="lendingnext">LendingNext</option>
                                                </optgroup>
                                                <optgroup label="Forms">
                                                    @foreach ($forms as $form)
                                                        <option value="{{$form->id}}">
                                                            {{$form->name}}
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                            </select>
                                        </div>
                                    </div>
                                @else
                                    <div class="col-xs-1 col-md-4">

                                    </div>
                                @endif
                                    <div class="col-xs-12 col-md-2 d-flex align-items-center ml-auto mt-2">
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
                            <h2>Total income statistic</h2>
                            <div class="c-chart-wrapper">
                                <canvas id="social-box-chart-1" height="90"></canvas>
                            </div>
                        </div>
                        <div class="card-body row text-center">
                            <div class="col">
                                <div class="text-value-xl"><span id="total-income-value">0</span></div>
                                <div class="text-uppercase text-muted small">Selected Period</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <div class="card">
                        <div class="card-header content-center">
                            <h2>Avg. EPC</h2>
                            <div class="c-chart-wrapper">
                                <canvas id="social-box-chart-2" height="90"></canvas>
                            </div>
                        </div>
                        <div class="card-body row text-center">
                            <div class="col">
                                <div class="text-value-xl"><span id="epc-all-time-value">0</span></div>
                                <div class="text-uppercase text-muted small">All time</div>
                            </div>
                            <div class="c-vr ps-0 pe-0"></div>
                            <div class="col">
                                <div class="text-value-xl"><span id="epc-value">0</span></div>
                                <div class="text-uppercase text-muted small">Selected Period</div>
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
                                <div class="text-uppercase text-muted small">Unique clicks</div>
                            </div>
                            <div class="c-vr ps-0 pe-0"></div>
                            <div class="col">
                                <div class="text-value-xl"><span id="total-clicks-value">0</span></div>
                                <div class="text-uppercase text-muted small">Total clicks</div>
                            </div>
                            <div class="c-vr ps-0 pe-0"></div>
                            <div class="col">
                                <div class="text-value-xl"><span id="total-earnings-value">0</span></div>
                                <div class="text-uppercase text-muted small">Total earnings</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 col-lg-4">
                    <div class="card">
                        <div class="card-header content-center">
                            <h2>Redirects Rate</h2>
                            <div class="c-chart-wrapper">
                                <canvas id="social-box-chart-1" height="90"></canvas>
                            </div>
                        </div>
                        <div class="card-body row text-center">
                            <div class="col">
                                <div class="text-value-xl"><span id="redirect-rate-value">0</span>%</div>
                                <div class="text-uppercase text-muted small">Selected Period Percentage</div>
                            </div>
                            <div class="c-vr ps-0 pe-0"></div>
                            <div class="col">
                                <div class="text-value-xl"><span id="redirects-value">0</span></div>
                                <div class="text-uppercase text-muted small">Selected Period Absolute Value</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <div class="card">
                        <div class="card-header content-center">
                            <h2>Clicks to submit</h2>
                            <div class="c-chart-wrapper">
                                <canvas id="social-box-chart-3" height="90"></canvas>
                            </div>
                        </div>
                        <div class="card-body row text-center">
                            <div class="col">
                                <div class="text-value-xl"><span class="clicks-in-period-value">0</span></div>
                                <div class="text-uppercase text-muted small">Clicks</div>
                            </div>
                            <div class="c-vr ps-0 pe-0"></div>
                            <div class="col">
                                <div class="text-value-xl"><span class="submits-in-period-value">0</span></div>
                                <div class="text-uppercase text-muted small">Submits</div>
                            </div>
                            <div class="c-vr ps-0 pe-0"></div>
                            <div class="col">
                                <div class="text-value-xl"><span id="clicks-to-submits-value">0</span>%</div>
                                <div class="text-uppercase text-muted small">Clicks To Submits</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <div class="card">
                        <div class="card-header content-center">
                            <h2>Total leads</h2>
                            <div class="c-chart-wrapper">
                                <canvas id="social-box-chart-1" height="90"></canvas>
                            </div>
                        </div>
                        <div class="card-body row text-center">
                            <div class="col">
                                <div class="text-value-xl"><span id="total-leads-value">0</span></div>
                                <div class="text-uppercase text-muted small">Selected Period</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 col-lg-4">
                    <div class="card">
                        <div class="card-header content-center">
                            <h2>EPL</h2>
                            <div class="c-chart-wrapper">
                                <canvas id="social-box-chart-1" height="90"></canvas>
                            </div>
                        </div>
                        <div class="card-body row text-center">
                            <div class="col">
                                <div class="text-value-xl"><span id="epl-with-value">0</span></div>
                                <div class="text-uppercase text-muted small">With</div>
                            </div>
                            <div class="c-vr ps-0 pe-0"></div>
                            <div class="col">
                                <div class="text-value-xl"><span id="epl-without-value">0</span></div>
                                <div class="text-uppercase text-muted small">Without</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <div class="card">
                        <div class="card-header content-center">
                            <h2>Solds to submit</h2>
                            <div class="c-chart-wrapper">
                                <canvas id="social-box-chart-3" height="90"></canvas>
                            </div>
                        </div>
                        <div class="card-body row text-center">
                            <div class="col">
                                <div class="text-value-xl"><span class="submits-in-period-value">0</span></div>
                                <div class="text-uppercase text-muted small">Submits</div>
                            </div>
                            <div class="c-vr ps-0 pe-0"></div>
                            <div class="col">
                                <div class="text-value-xl"><span id="solds-in-period-value">0</span></div>
                                <div class="text-uppercase text-muted small">Solds</div>
                            </div>
                            <div class="c-vr ps-0 pe-0"></div>
                            <div class="col">
                                <div class="text-value-xl"><span id="sold-to-submit-value">0</span>%</div>
                                <div class="text-uppercase text-muted small">Solds To Submits</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <div class="card">
                        <div class="card-header content-center">
                            <h2>Visits to clicks</h2>
                            <div class="c-chart-wrapper">
                                <canvas id="social-box-chart-3" height="90"></canvas>
                            </div>
                        </div>
                        <div class="card-body row text-center">
                            <div class="col">
                                <div class="text-value-xl"><span id="visits-in-period-value">0</span></div>
                                <div class="text-uppercase text-muted small">Visits</div>
                            </div>
                            <div class="c-vr ps-0 pe-0"></div>
                            <div class="col">
                                <div class="text-value-xl"><span class="clicks-in-period-value">0</span></div>
                                <div class="text-uppercase text-muted small">Clicks</div>
                            </div>
                            <div class="c-vr ps-0 pe-0"></div>
                            <div class="col">
                                <div class="text-value-xl"><span id="visits-to-clicks-value">0</span>%</div>
                                <div class="text-uppercase text-muted small">Visits To Clicks</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 col-lg-4">
                    <div class="card">
                        <div class="card-header content-center">
                            <h2>Denied</h2>
                            <div class="c-chart-wrapper">
                                <canvas id="social-box-chart-1" height="90"></canvas>
                            </div>
                        </div>
                        <div class="card-body row text-center">
                            <div class="col">
                                <div class="text-value-xl"><span id="denied-total">0</span></div>
                                <div class="text-uppercase text-muted small">Total</div>
                            </div>
                            <div class="c-vr ps-0 pe-0"></div>
                            <div class="col">
                                <div class="text-value-xl"><span id="dnm-errors">0</span></div>
                                <div class="text-uppercase text-muted small">Errors from DNM</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <div class="card">
                        <div class="card-header content-center">
                            <h2>IP risk</h2>
                            <div class="c-chart-wrapper">
                                <canvas id="social-box-chart-1" height="90"></canvas>
                            </div>
                        </div>
                        <div class="card-body row text-center">
                            <div class="col">
                                <div class="text-value-xl"><span id="average-risk">0</span></div>
                                <div class="text-uppercase text-muted small">Average risk</div>
                            </div>
                            <div class="c-vr ps-0 pe-0"></div>
                            <div class="col">
                                <div class="text-value-xl"><span id="risk-submits">0</span></div>
                                <div class="text-uppercase text-muted small">Risk submits</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <div class="card">
                        <div class="card-header content-center">
                            <h2>Search reapplies</h2>
                            <div class="c-chart-wrapper">
                                <canvas id="social-box-chart-3" height="90"></canvas>
                            </div>
                        </div>
                        <div class="row card-body row text-center pb-0 pt-1">
                            <div class="col">
                                <div class="text-value-xl"> Email </div>
                            </div>
                            <div class="col">
                                <div class="text-value-xl"> Phone </div>
                            </div>
                        </div>
                        <div class="card-body row text-center pt-0">
                            <div class="col">
                                <div class="text-value-xl"><span id="reapply-search-email-total">0</span></div>
                                    <div class="text-uppercase text-muted small">Total</div>
                            </div>
                            <div class="c-vr ps-0 pe-0"></div>
                            <div class="col">
                                <div class="text-value-xl"><span id="reapply-search-email-found">0</span></div>
                                <div class="text-uppercase text-muted small">Found</div>
                            </div>
                            <div class="c-vr ps-0 pe-0" style="width: 3px!important;"></div>
                            <div class="col">
                                <div class="text-value-xl"><span id="reapply-search-phone-total">0</span></div>
                                <div class="text-uppercase text-muted small">Total</div>
                            </div>
                            <div class="c-vr ps-0 pe-0"></div>
                            <div class="col">
                                <div class="text-value-xl"><span id="reapply-search-phone-found">0</span></div>
                                <div class="text-uppercase text-muted small">Found</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 col-lg-4">
                    <div class="card">
                        <div class="card-header content-center">
                            <h2>Request id feature</h2>
                            <div class="c-chart-wrapper">
                                <canvas id="social-box-chart-1" height="90"></canvas>
                            </div>
                        </div>
                        <div class="card-body row text-center">
                            <div class="col">
                                <div class="text-value-xl"><span id="request_id_mark">0</span></div>
                                <div class="text-uppercase text-muted small">Total</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <div class="card">
                        <div class="card-header content-center">
                            <h2>Cookie</h2>
                            <div class="c-chart-wrapper">
                                <canvas id="social-box-chart-1" height="90"></canvas>
                            </div>
                        </div>
                        <div class="card-body row text-center">
                            <div class="col">
                                <div class="text-value-xl"><span id="cookie-clicks">0</span></div>
                                <div class="text-uppercase text-muted small">Clicks</div>
                            </div>
                            <div class="c-vr ps-0 pe-0"></div>
                            <div class="col">
                                <div class="text-value-xl"><span id="cookie-submits">0</span></div>
                                <div class="text-uppercase text-muted small">Submits</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">Chart</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-5">
                            <h4 class="card-title mb-0"></h4>
                            <div class="small text-muted"></div>
                        </div>
                    </div>
                    <div class="c-chart-wrapper" style="height:300px;margin-top:40px;">
                        <canvas class="" id="main-chart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')

    <script type="module">
        'use strict';

        document.addEventListener('DOMContentLoaded', () => {

            document.querySelector('#leadTypeToggle').addEventListener('change', event => {
                if (event.target.parentNode.classList.contains('indeterminate')) {
                    event.target.value = 'all'
                } else if (event.target.parentNode.classList.contains('off')) {
                    event.target.value = 'personal'
                } else {
                    event.target.value = 'payday'
                }
            });

            document.querySelector('#leadTypeToggle').bootstrapToggle({
                on: 'Payday',
                off: 'Personal',
                onvalue: 'payday',
                offvalue: 'personal',
                tristate: true,
                indeterminate: true
            });

            document.querySelector('#actionTypeToggle').addEventListener('change', event => {
                if (event.target.parentNode.classList.contains('indeterminate')) {
                    event.target.value = 'all'
                } else if (event.target.parentNode.classList.contains('off')) {
                    event.target.value = 'full'
                } else {
                    event.target.value = 'reapply'
                }
            });

            document.querySelector('#actionTypeToggle').bootstrapToggle({
                on: 'Reapply',
                off: 'Full',
                onvalue: 'reapply',
                offvalue: 'full',
                tristate: true
            });
            document.querySelector('#actionTypeToggle').bootstrapToggle('indeterminate');

            new TempusDominus(document.getElementById('to-date'), {
                localization: {
                    format: '<?= $jsTimeFormat ?>',
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
                    format: '<?= $jsTimeFormat ?>',
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
                dashboarChartRequest();
            });

            document.querySelector('#period').addEventListener('change', event => {
                let from = event.target.options[event.target.selectedIndex].dataset.from;
                let to = event.target.options[event.target.selectedIndex].dataset.to;
                document.querySelector("#from-date").value = from;
                document.querySelector("#to-date").value = to;
            });
            dashboardRequest();
        });

        const mainChart = new Chart(document.getElementById('main-chart'), {
            type: 'line',
            data: {
                datasets: [{
                    label: 'visitors to submit',
                    backgroundColor: 'transparent',
                    borderColor: 'red',
                    pointBackgroundColor: 'red',
                    pointBorderColor: 'red',
                    //cubicInterpolationMode: 'monotone',
                    tension: 0.4

                }, {
                    label: 'solds to submit',
                    backgroundColor: 'transparent',
                    borderColor: 'green',
                    pointBackgroundColor: 'green',
                    pointBorderColor: 'green',
                    cubicInterpolationMode: 'monotone',
                    tension: 0.4

                }, {
                    label: 'redirect rates',
                    backgroundColor: 'transparent',
                    borderColor: 'blue',
                    pointBackgroundColor: 'blue',
                    pointBorderColor: 'blue',
                    cubicInterpolationMode: 'monotone',
                    tension: 0.4
                }]
            },
            options: {
                responsive: false,
                scales: {
                    y: {
                        suggestedMin: 0,
                        suggestedMax: 100
                    }
                }
            },
        });

        const dashboarChartRequest = () => {
            let start = document.querySelector('#from-date').value;
            let stop = document.querySelector('#to-date').value;

            $.ajax('/dashboard-chart', {
                dataType: 'json',
                type: 'GET',
                data: {
                    start: start,
                    stop: stop,
                    leadType: document.querySelector('#leadTypeToggle').value,
                    actionType: document.querySelector('#actionTypeToggle').value,
                    formId: document.querySelector('#form') ? document.querySelector('#form').value : '',
                    flowId: document.querySelector('#flow') ? document.querySelector('#flow').value : '',
                },
                success: function (response) {
                    mainChart.data.labels = response.visitors.labels
                    mainChart.data.datasets[0].data = response.visitors.data;
                    mainChart.data.datasets[1].data = response.submits.data;
                    mainChart.data.datasets[2].data = response.redirects.data;
                    mainChart.update();
                },

                error: function (jqXhr, textStatus, errorMessage) {
                    $('p').append('Error: ' + errorMessage);
                }
            });
        };

        const dashboardRequest = () => {
            let start = document.querySelector('#from-date').value;
            let stop = document.querySelector('#to-date').value;

            $.ajax('/dashboard', {
                dataType: 'json',
                type: 'GET',
                data: {
                    start: start,
                    stop: stop,
                    leadType: document.querySelector('#leadTypeToggle').value,
                    actionType: document.querySelector('#actionTypeToggle').value,
                    formId: document.querySelector('#form') ? document.querySelector('#form').value : '',
                    flowId: document.querySelector('#flow') ? document.querySelector('#flow').value : '',
                },
                success: function (response) {
                    document.querySelector('#total-income-value').textContent = response.total_income;
                    document.querySelector('#epc-all-time-value').textContent = response.epc['all_time'];
                    document.querySelector('#epc-value').textContent = response.epc['period'];
                    document.querySelector('#unique-clicks-value').textContent = response.unique_visits;
                    document.querySelector('#total-clicks-value').textContent = response.total_visits;
                    document.querySelector('#total-earnings-value').textContent = response.total_earnings;
                    document.querySelector('#redirect-rate-value').textContent = response.redirectRate;
                    document.querySelector('#redirects-value').textContent = response.redirects;
                    document.querySelector('#clicks-to-submits-value').textContent = response.clicks_to_sub;
                    document.querySelector('#epl-with-value').textContent = response.epl['with'];
                    document.querySelector('#total-leads-value').textContent = response.total_leads;
                    document.querySelector('#epl-without-value').textContent = response.epl['without'];
                    document.querySelector('#sold-to-submit-value').textContent = response.sold_to_submit;
                    document.querySelector('#solds-in-period-value').textContent = response.solds_in_period;
                    document.querySelector('#visits-in-period-value').textContent = response.visits_in_period;
                    document.querySelector('#visits-to-clicks-value').textContent = response.visits_to_clicks;

                    document.querySelector('#dnm-errors').textContent = response.dnm_errors;
                    document.querySelector('#denied-total').textContent = response.denied_total;

                    document.querySelector('#risk-submits').textContent = response.risk_submits;
                    document.querySelector('#average-risk').textContent = response.average_risk;

                    document.querySelector('#reapply-search-email-total').textContent = response.search_reapply_email_total;
                    document.querySelector('#reapply-search-email-found').textContent = response.search_reapply_email_found;
                    document.querySelector('#reapply-search-phone-total').textContent = response.search_reapply_phone_total;
                    document.querySelector('#reapply-search-phone-found').textContent = response.search_reapply_phone_found;

                    document.querySelector('#request_id_mark').textContent = response.request_id_mark;

                    document.querySelector('#cookie-clicks').textContent = response.cookie_clicks;
                    document.querySelector('#cookie-submits').textContent = response.cookie_submits;

                    document.querySelectorAll('.clicks-in-period-value').forEach(el => {
                        el.textContent = response.clicks_in_period
                    });
                    document.querySelectorAll('.submits-in-period-value').forEach(el => {
                        el.textContent = response.submits_in_period
                    });
                },
                error: function (jqXhr, textStatus, errorMessage) {
                    $('p').append('Error: ' + errorMessage);
                }
            });
        };
    </script>
@endpush
