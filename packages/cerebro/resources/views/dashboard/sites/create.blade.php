<?php
/**
 * @var $forms array
 * @var $servers array
 *
 */
?>
@extends('layouts.base')

@section('content')
    <div class="container-fluid">
        <div class="fade-in">
            <div class="card">
                <div class="card-header">Create</div>
                <div class="card-body">
                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <!-- <button class="nav-link" id="nav-loan-tab" data-bs-toggle="tab"
                                    data-bs-target="#nav-loan" type="button" role="tab" aria-controls="nav-loan"
                                    aria-selected="false">LoanSites
                            </button> -->
                            <button class="nav-link active" id="nav-card-tab" data-bs-toggle="tab"
                                    data-bs-target="#nav-card" type="button" role="tab" aria-controls="nav-card"
                                    aria-selected="true">CardFormSites
                            </button>
                        </div>
                    </nav>
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade bg-white" id="nav-loan" role="tabpanel"
                             aria-labelledby="nav-loan-tab">
                            @include('dashboard.sites.forms.loanForm')
                            @vite('resources/js/sitesManagement/createLoanForms.js')
                        </div>
                        <div class="tab-pane fade show bg-white active" id="nav-card" role="tabpanel"
                             aria-labelledby="nav-card-tab">
                            @include('dashboard.sites.forms.cardForm')
                            @vite('resources/js/sitesManagement/createCardForms.js')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection





