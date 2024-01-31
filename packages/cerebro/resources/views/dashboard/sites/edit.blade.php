<?php
/**
 * @var $forms array
 * @var $servers array
 * @var $site array
 */
?>
@extends('layouts.base')

@section('content')
    <div class="container-fluid">
        <div class="fade-in">
            <div class="card">
                <div class="card-header">Update</div>
                <div class="card-body">
                    @if($site->site_type == 'card')
                        @include('dashboard.sites.forms.cardForm')
                        @vite('resources/js/sitesManagement/editCardForms.js')
                    @else
                        @include('dashboard.sites.forms.loanForm')
                        @vite('resources/js/sitesManagement/editLoanForms.js')
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="modal" tabindex="-1" id="sitesImagePreviewModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Image Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body w-100">
                    <img class="mw-100" id="preview-image" src="#" alt="">
                </div>
            </div>
        </div>
    </div>
@endsection





