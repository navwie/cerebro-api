@extends('layouts.base')

@section('content')

    <div class="container-fluid">
        <div class="fade-in">
            <div class="card">
                <div class="card-header">&nbsp;</div>
                <div class="card-body">
                    <table class="table table-responsive-sm" id="servers-table" style="width: 100%">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>IP Address</th>
                            <th>Is Active</th>
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

    <div class="modal fade" id="serversModal" tabindex="-1" aria-labelledby="serversModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="serversForm" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="serversModalLabel"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="serversModalId" name="id">
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="serversModalName" class="form-label">Name</label>
                                    <input type="text" maxlength="256" class="form-control" id="serversModalName"
                                           name="name" required>
                                    <div id="serversModalNameFeedback" class="invalid-feedback"></div>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="serversModalIPAddress" class="form-label">IP Address</label>
                                    <input type="text" maxlength="256" class="form-control" id="serversModalIPAddress"
                                           name="ip_address" required>
                                    <div id="serversModalIPAddressFeedback" class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="btnserversModalSave">Save
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="serversDeleteModal" tabindex="-1" aria-labelledby="serversDeleteModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="serversDeleteModalLabel">Delete server?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="serversDeleteModalId" name="id">
                    Do you confirm that you want to delete this entry?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" id="serversDeleteModalSubmit">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')

    <script type="module">
        'use strict';

        const serversModal = new Modal(document.getElementById('serversModal'), {
            backdrop: false
        });
        const serversDeleteModal = new Modal(document.getElementById('serversDeleteModal'), {
            backdrop: false
        });

        $(document).ready(function () {
            $('#servers-table thead th').each(function (i) {
                if (i === 0 || i === 3 || i === 4) {
                    return true;
                }
                let title = $(this).text();
                $(this).html('<input class="form-control individualSearch" type="text" placeholder="' + title + '" />');

            });
            //prevent order on search field click
            $('input.individualSearch', this).on('click', function () {
                return false;
            });

            $('#servers-table').dataTable({
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
                    $('#servers-table_length').append('<button type="button" class="btn btn-primary btn-create float-right">+ Create</button>');
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
                //language: { "processing": '<div class="d-flex justify-content-center" style="z-index: 1090;"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>' },
                serverSide: true,
                stateSave: true,
                ajax: {
                    url: '{{url('servers-crud')}}',
                    type: 'GET',
                },
                columnDefs: [
                    {
                        targets: [0],
                        visible: false
                    },
                    {
                        targets: 4,
                        visible: true,
                        searchable: false,
                        sortable: false,
                        render: function (data, type, row, meta) {
                            const deleteButton = '<i class="fa-solid fa-trash btn-delete" data-id="' + row[0] + '"></i>';
                            const editButton = '<i class="fa-solid fa-pencil btn-edit me-2" role="button" data-id="' + row[0] + '"></i>';
                            return editButton + deleteButton;
                        }
                    },
                ],
            });

            $(document).on('click', '.btn-delete', function () {
                const id = $(this).attr('data-id');
                $('#serversDeleteModalId').val(id);
                serversDeleteModal.show();
            });

            $(document).on('click', '#serversDeleteModalSubmit', function () {
                const id = $('#serversDeleteModalId').val();
                sendDeleteRequest(id);
            });

            $(document).on('click', '.btn-create', function () {
                $('#serversModalLabel').html('Create front server');
                $('#serversForm input:not([name="_token"])').val('');
                $('#serversForm input[type="password"]').attr('required', true);
                resetErrors();
                serversModal.show();
            });

            $(document).on('click', '.btn-edit', function () {
                const id = $(this).attr('data-id');
                $('#serversModalId').val(id);
                $('#serversForm input:not([name="_token"])').val('');
                $('#serversModalLabel').html('Edit from');
                resetErrors();
                getServerRequest(id);
                serversModal.show();
            });

            $(document).on('click', '#btnserversModalSave', function () {
                const id = $('#serversModalId').val();

                if (id === '') {
                    sendCreateRequest();
                } else {
                    sendEditRequest(id);
                }
            });
        });


        function resetErrors() {
            $('#serversForm input').each(function () {
                $(this).removeClass('is-invalid');
                $("#" + $(this).attr('id') + "Feedback").html('');
            });
        }

        function getServerRequest(id) {
            $.ajax({
                url: '{{url('servers-crud')}}/' + id,
                type: 'GET',
            }).done(function (response) {
                if (response.status === 200) {
                    let element;
                    for (let field in response.data) {
                        if (field === 'private_key' || field === 'public_key') {
                            continue;
                        }
                        element = $('input[name="' + field + '"]');
                        if (element.length) {
                            element.val(response.data[field]);
                        }
                    }
                }
            }).fail(function (error) {
                console.log(error);
            });
        }

        function sendCreateRequest() {
            const formData = prepareFormData();
            $.ajax({
                url: '{{url('servers-crud')}}',
                type: 'POST',
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                data: formData,
                cache: false,
                dataType: false,
                processData: false,
                contentType: false
            }).done(function (response) {
                if (response.status === 200) {
                    serversModal.hide();
                    $('#servers-table').DataTable().ajax.reload();
                }
            }).fail(function (error) {
                resetErrors();
                setErrors(error.responseJSON.errors);
            });
        }

        function sendEditRequest(id) {
            const formData = prepareFormData();
            $.ajax({
                url: '{{url('servers-crud')}}/' + id,
                type: 'POST',
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                data: formData,
                cache: false,
                dataType: false,
                processData: false,
                contentType: false,
            }).done(function (response) {
                if (response.status === 200) {
                    serversModal.hide();
                    $('#servers-table').DataTable().ajax.reload();
                }
            }).fail(function (error) {
                resetErrors();
                setErrors(error.responseJSON.errors);
            });
        }

        function sendDeleteRequest(id) {
            $.ajax({
                url: '{{url('servers-crud')}}/' + id,
                type: 'DELETE',
                data: {
                    "_token": "{{ csrf_token() }}"
                }
            }).done(function (response) {
                if (response.status === 200) {
                    serversDeleteModal.hide();
                    $('#servers-table').DataTable().ajax.reload();
                }
            }).fail(function (error) {
                console.log(error);
            });
        }

        function prepareFormData() {
            return new FormData(document.getElementById('serversForm'));
        }

        function setErrors(errors) {
            let input;
            for (let field in errors) {
                input = $('#serversForm input[name="' + field + '"]');
                input.addClass('is-invalid');
                for (let i = 0; i < errors[field].length; i++) {
                    $("#" + input.attr('id') + "Feedback").append(errors[field][i]);
                }
            }
        }
    </script>
@endpush

