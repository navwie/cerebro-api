@extends('layouts.base')

@section('content')
    <div class="container-fluid">
        <div class="fade-in">
            <div class="card">
                <div class="card-header">&nbsp;</div>
                <div class="card-body">
                    <table class="table" id="sites-table" style="width: 100%">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Domain Name</th>
                            <th>Form Name</th>
                            <th>Server</th>
                            <th>Theme</th>
                            <th>Certificate</th>
                            <th>CertStatus</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="sitesDeleteModal" tabindex="-1" aria-labelledby="sitesDeleteModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sitesDeleteModalLabel">Delete site?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="sitesDeleteModalId" name="id">
                    Do you confirm that you want to delete this entry?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" id="sitesDeleteModalSubmit">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="sitesCertificateModal" tabindex="-1" aria-labelledby="sitesCertificateModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sitesCertificateModalLabel">CNAME Record Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="sitesCertificateModalClose">OK</button>
                </div>
            </div>
        </div>
    </div>

    <div class="toast-container position-absolute top-0 end-0 p-3" style="z-index: 1100">
        <div class="toast" id="toastCnameNameCopy" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto">CNAME Name</strong>
                <small></small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                Your CNAME Name copied.
            </div>
        </div>
        <div class="toast" id="toastCnameValueCopy" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto">CNAME Value</strong>
                <small></small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                Your CNAME Value copied.
            </div>
        </div>
        <div class="toast" id="toastDomainNameCopy" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto">Domain Name</strong>
                <small></small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                Domain Name copied.
            </div>
        </div>
    </div>
@endsection

@push('scripts')

    <script type="module">
        'use strict';

        const sitesDeleteModal = new Modal(document.getElementById('sitesDeleteModal'), {backdrop: false});
        const certificateModal = new Modal(document.getElementById('sitesCertificateModal'), {backdrop: false});
        const toastDomainNameCopy = new Toast(document.getElementById('toastDomainNameCopy'));


        document.addEventListener('DOMContentLoaded', () => {
            const closeCertificateModalButton = document.getElementById('sitesCertificateModalClose');
            const toastCnameNameCopy = new Toast(document.getElementById('toastCnameNameCopy'));
            const toastCnameValueCopy = new Toast(document.getElementById('toastCnameValueCopy'));

            $(document).on('click', '.get-cname', function () {
                let siteId = $(this).data('site-id');

                $.ajax({
                    url: '{{url('cname')}}/' + siteId,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    }
                }).done(function (response) {

                    if (response.status === 200) {

                        $('#sitesCertificateModal').find('.modal-body').html('<div class="row">' +
                            '<div class="col-12">' +
                            '<span>Here are the CNAME DNS record details, you need to put them into the domain records in your DNS management system.</span>' +
                            '</div>' +
                            '</div>');

                        response.data.forEach(el => {
                            $('#sitesCertificateModal').find('.modal-body').append('<div class="row mt-5">' +
                                '<div class="col-12">' +
                                '<label for="cnameName">CNAME Name:</label>' +
                                '<span class="cnameName">' + el.name + '</span>' +
                                '<i class="fa-regular fa-copy copyCnameName" type="button"></i>' +
                                '</div>' +
                                '</div>' +
                                '<div class="row">' +
                                '<div class="col-12">' +
                                '<label for="cnameValue">CNAME Value:</label>' +
                                '<span class="cnameValue">' + el.value + '</span>' +
                                '<i class="fa-regular fa-copy copyCnameValue" type="button"></i>' +
                                '</div>' +
                                '</div>');
                        });

                        document.querySelectorAll('.copyCnameName').forEach((el) => el.onclick = (event) => {
                            let cname = event.target.previousElementSibling.textContent;
                            navigator.clipboard.writeText(cname);
                            toastCnameNameCopy.show();
                        });

                        document.querySelectorAll('.copyCnameValue').forEach((el) => el.onclick = (event) => {
                            let cvalue = event.target.previousElementSibling.textContent;
                            navigator.clipboard.writeText(cvalue);
                            toastCnameValueCopy.show();
                        });

                        closeCertificateModalButton.dataset.siteId = response.id
                        certificateModal.show();
                    }

                    if (response.status === 500) {
                        alert(response.message);
                    }

                }).fail(function (error) {
                    console.log(error);
                });
            });

            $(document).on('click', '.request-cert', function () {
                let siteId = $(this).data('site-id');

                $.ajax({
                    url: '{{url('sites')}}/' + siteId,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    }
                }).done(function (response) {

                    if (response.status === 200) {
                        alert('Certificate has been requested successfully!')
                        $('#sites-table').DataTable().ajax.reload();
                    }

                    if (response.status === 500) {
                        alert(response.message);
                    }

                }).fail(function (error) {
                    console.log(error);
                });
            });

            closeCertificateModalButton.onclick = () => {
                let siteId = closeCertificateModalButton.dataset.siteId;
                console.log(siteId);
                certificateModal.hide();
                location.reload();
            }
        });

        $(document).ready(function () {
            $(document).on('click', '.copy-domain-name', function () {
                $(this)
                navigator.clipboard.writeText($(this).closest('td').find('.domain-name').text());
                toastDomainNameCopy.show();
            });

            $('#sites-table thead th').each(function (i) {
                if (i === 0 || i === 5 || i === 6 || i === 7) {
                    return true;
                }
                let title = $(this).text();
                $(this).html('<input class="form-control individualSearch" type="text" placeholder="' + title + '" />');
            });
            //prevent order on search field click
            $('input.individualSearch', this).on('click', function () {
                return false;
            });

            $('#sites-table').dataTable({
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
                    $('#sites-table_length').append('<a href="{{ route('create.site') }}"><button type="button" class="btn btn-primary btn-create float-right">+ Create</button>');
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
                    url: '{{url('sites-crud')}}',
                    type: 'GET',
                },
                columnDefs: [
                    {
                        targets: [0],
                        visible: false
                    },
                    {
                        targets: [1],
                        visible: true,
                        sortable: true,
                        searchable: true,
                        render: function (data, type, row) {
                            return '<span class="domain-name text-primary"><a href="http://' + data + '" target="_blank">' + data + '</a></span> <i class="fa-regular fa-copy copy-domain-name" type="button"></i>';
                        }
                    },
                    {
                        targets: [5],
                        visible: true,
                        sortable: false,
                        searchable: false,
                        width: '18%',
                        render: function (data, type, row) {
                            if (row[5] === 1) {
                                return '<button class="btn btn-outline-success" disabled="disabled">SSL-READY</button>';
                            }
                            if (row[7] === '' || row[7] === null) {
                                return '<button class="request-cert btn btn-outline-danger" data-site-id="' + row[0] + '">Request Cert</button>';
                            }

                            if (row[7] !== '' || row[7] !== null) {
                                return '<button class="get-cname btn btn-outline-primary" data-site-id="' + row[0] + '">Get CNAME Data</button>';
                            }
                        }
                    },
                    {
                        targets: [6],
                        visible: true,
                        sortable: true,
                        searchable: true
                    },
                    {
                        targets: [7],
                        visible: true,
                        sortable: false,
                        searchable: false,
                        render: function (data, type, row, meta) {
                            const deleteButton = '<i class="fa-solid fa-trash btn-delete me-2" data-id="' + row[0] + '"></i>';
                            const editButton = '<a href="/sites/' + row[0] + '"><i class="fa-solid fa-pencil-alt text-black btn-edit me-2" data-id="' + row[0] + '"></i></a>';
                            return editButton + deleteButton;
                        }
                    }
                ],
            });

            $(document).on('click', '.btn-delete', function () {
                const id = $(this).attr('data-id');
                $('#sitesDeleteModalId').val(id);
                sitesDeleteModal.show();

            });

            $(document).on('click', '#sitesDeleteModalSubmit', function () {
                const id = $('#sitesDeleteModalId').val();
                sendDeleteRequest(id);
            });

            $(document).on('click', '#btnSitesModalSave', function () {
                const id = $('#sitesModalId').val();

                if (id === '') {
                    sendCreateRequest();
                } else {
                    sendEditRequest(id);
                }
            });
        });

        function sendDeleteRequest(id) {
            $("div.spanner").addClass("show");
            $("div.overlay").addClass("show");
            $.ajax({
                url: '{{url('sites-crud')}}/' + id,
                type: 'DELETE',
                data: {
                    "_token": "{{ csrf_token() }}"
                }
            }).done(function (response) {
                if (response.status === 200) {
                    $("div.spanner").removeClass("show");
                    $("div.overlay").removeClass("show");
                    sitesDeleteModal.hide();
                    $('#sites-table').DataTable().ajax.reload();
                }
            }).fail(function (error) {
                console.log(error);
            });
        }

        function prepareFormData() {
            return new FormData(document.getElementById('sitesForm'));
        }

    </script>
@endpush

