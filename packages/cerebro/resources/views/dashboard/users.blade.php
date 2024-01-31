@extends('layouts.base')

@section('content')

    <div class="container-fluid">
        <div class="fade-in">
            <div class="card">
                <div class="card-header"><i class="fa fa-align-justify"></i> Forms</div>
                <div class="card-body">
                    <table class="table table-responsive-sm" id="users-table" style="width: 100%">
                        <thead>
                        <tr>
                            <th>id</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Post back amount</th>
                            <th>Personal min req</th>
                            <th>Actions</th>
                            <th>email_verified_at</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="usersModal" tabindex="-1" aria-labelledby="usersModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form id="usersForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="usersModalLabel"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="usersModalId" name="id">
                        <input type="hidden" id="usersModalEmailVerif" name="email_verified_at">
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="usersModalName" class="form-label">Name</label>
                                    <input type="text" maxlength="256" class="form-control" id="usersModalName"
                                           name="name" required>
                                    <div id="usersModalNameFeedback" class="invalid-feedback">

                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="usersModalEmail" class="form-label">Email address</label>
                                    <input type="email" maxlength="256" class="form-control" id="usersModalEmail"
                                           name="email" required>
                                    <div id="usersModalEmailFeedback" class="invalid-feedback">

                                    </div>
                                    <div id="resendVerifyEmailDiv">
                                        <span id="resendVerifyEmail" role="button" class="text-decoration-underline">Click here</span>
                                        to send a confirmation email.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="usersModalEmail" class="form-label">Email CCPA</label>
                                    <input type="email" maxlength="256" class="form-control" id="usersModalEmailCcpa"
                                           name="email_ccpa" required>
                                    <div id="usersModalEmailCcpaFeedback" class="invalid-feedback">

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="usersModalPassword" class="form-label">Password</label>
                                    <input type="password" maxlength="256" class="form-control" id="usersModalPassword"
                                           name="password" required>
                                    <div id="usersModalPasswordFeedback" class="invalid-feedback">

                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="usersModalPasswordConfirm" class="form-label">Confirm password</label>
                                    <input type="password" maxlength="256" class="form-control"
                                           id="usersModalPasswordConfirm" name="password_confirmation" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-3">
                                <div class="mb-3">
                                    <label class="form-label">Processing time</label>
                                    <div class="row">
                                        <div class="col-6">
                                            <input type="number"  class="form-control" id="usersModalProcessingTimeMin" placeholder="min" name="processing_time_min">
                                            <label for="usersModalProcessingTimeSec" class="form-label">min</label>
                                            <div id="usersModalProcessingTimeMinFeedback" class="invalid-feedback">

                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <input type="number"  class="form-control" id="usersModalProcessingTimeSec" placeholder="sec" name="processing_time_sec">
                                            <label for="usersModalProcessingTimeSec" class="form-label">sec</label>
                                            <div id="usersModalProcessingTimeSecFeedback" class="invalid-feedback">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mb-3">
                                    <label for="usersModalMinPrice" class="form-label">Min price</label>
                                    <input type="number" class="form-control" id="usersModalMinPrice" name="min_price">
                                    <div id="usersModalMinPriceFeedback" class="invalid-feedback">

                                    </div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mb-3">
                                    <label for="usersModalLeadMinPrice" class="form-label">Lead min price</label>
                                    <input type="text" class="form-control" id="usersModalLeadMinPrice" name="lead_min_price">
                                    <div id="usersModalLeadMinPriceFeedback" class="invalid-feedback">

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-3">
                                <div class="mb-3">
                                    <label for="usersModalRole" class="form-label">Role</label>
                                    <select class="form-select" id="usersModalRole" name="role">
                                        <option value="user" selected>User</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-3">
                                <div class="mb-3">
                                    <label for="usersModalPersonalChannelId" class="form-label">Personal channel
                                        id</label>
                                    <input type="text" class="form-control" id="usersModalPersonalChannelId"
                                           name="personal_channel_id">
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mb-3">
                                    <label for="usersModalLeadChannelId" class="form-label">Lead channel id</label>
                                    <input type="text" class="form-control" id="usersModalLeadChannelId"
                                           name="lead_channel_id">
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mb-3">
                                    <label for="usersModalPostBackAmount" class="form-label">Post back amount</label>
                                    <input type="text" class="form-control" id="usersModalPostBackAmount"
                                           name="post_back_amount">
                                    <div id="usersModalPostBackAmountFeedback" class="invalid-feedback">

                                    </div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mb-3">
                                    <label for="usersModalPersonalMinReq" class="form-label">Personal min req</label>
                                    <input type="text" class="form-control" id="usersModalPersonalMinReq"
                                           name="personal_min_req">
                                    <div id="usersModalPersonalMinReqFeedback" class="invalid-feedback">

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="usersModalPersonalPassword" class="form-label">Personal password</label>
                                    <input type="text" class="form-control" id="usersModalPersonalPassword"
                                           name="personal_password">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="usersModalLeadPassword" class="form-label">Lead password</label>
                                    <input type="text" class="form-control" id="usersModalLeadPassword"
                                           name="lead_password">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="usersModalPostBackUrl" class="form-label">
                                        Post back url
                                        <p><small>You can use next placeholders:[amount],[clickId] and [transactionId]</small></p>
                                    </label>
                                    <input type="text" class="form-control" id="usersModalPostBackUrl"
                                           name="post_back_url">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success float-left" id="btnModalRegenerate">
                            <i class="cil-reload"></i> Regenerate token
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="btnModalSave">Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="usersConfirmFormModal" tabindex="-1" aria-labelledby="usersConfirmFormModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="usersConfirmFormModalLabel">Confirm form</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    When the email is changed, all tokens cease to be valid, and the email must be verified again.
                    New tokens cannot be created without email confirmation.
                    Do you confirm saving the form?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="submitForm"
                            data-bs-dismiss="modal" aria-label="Close">Ok
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="usersDeleteModal" tabindex="-1" aria-labelledby="usersDeleteModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="usersDeleteModalLabel">Delete form</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="usersDeleteModalId" name="id">
                    Do you confirm that you want to delete this entry?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" id="usersDeleteModalSubmit">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="usersTokenModal" tabindex="-1" aria-labelledby="usersTokenModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="usersTokenModalLabel">Token</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Save this token. If you lose it, we cannot restore it.
                    <span id="userToken"></span>
                    <i class="fa-regular fa-copy" type="button" id="copyToken"></i>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal" aria-label="Close">Ok</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="usersConfirmRegenerateModal" tabindex="-1"
         aria-labelledby="usersConfirmRegenerateModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="usersConfirmRegenerateModalLabel">Regenerate token</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="usersRegenerateModalId" name="id">
                    Do you confirm that you want to regenerate token?
                    This action remove the remaining tokens.
                    <span id="warningNoConfirmEmail" class="text-danger">Email for this form is not confirmed!</span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="usersRegenerateModalSubmit">Ok</button>
                </div>
            </div>
        </div>
    </div>

    <div class="toast-container position-absolute top-0 end-0 p-3" style="z-index: 1100">
        <div class="toast" id="toastEmailSend" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto">Email</strong>
                <small></small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                Email confirmation sent.
            </div>
        </div>
        <div class="toast" id="toastCopy" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto">Email</strong>
                <small></small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                Your token copied.
            </div>
        </div>
    </div>
@endsection

@push('scripts')

    <script type="module">
        'use strict';
        $(document).ready(function () {
            const currentId = {{ json_encode(auth()->user()->id) }};
            const toastEmailSend = new Toast(document.getElementById('toastEmailSend'));
            const toastCopy = new Toast(document.getElementById('toastCopy'));
            const usersModal = new Modal(document.getElementById('usersModal'), {
                backdrop: false
            });
            const usersDeleteModal = new Modal(document.getElementById('usersDeleteModal'), {
                backdrop: false
            });
            const usersTokenModal = new Modal(document.getElementById('usersTokenModal'), {
                backdrop: false
            });
            const usersConfirmFormModal = new Modal(document.getElementById('usersConfirmFormModal'), {
                backdrop: false
            });
            const usersConfirmRegenerateModal = new Modal(document.getElementById('usersConfirmRegenerateModal'), {
                backdrop: false
            });

            $('#users-table thead th').each(function (i) {
                let title = $(this).text();
                if (title == 'Actions') {
                    $(this).html('<span class="w-100 text-center">' + title + '</span>');
                } else {
                    $(this).html('<input class="form-control individualSearch" type="text" placeholder="' + title + '" />');
                }
            });

            $('input.individualSearch', this).on('click', function () {
                return false;
            });

            $('#users-table').dataTable({
                initComplete: function () {
                    // Apply the search
                    this.api().columns().every(function () {
                        let that = this;
                        $('input', this.header()).on('keyup change clear', function () {
                            if (that.search() !== this.value) {
                                that
                                    .search(this.value)
                                    .draw();
                            }
                        });
                    });
                    $('#users-table_length').append('<button type="button" class="btn btn-primary btn-create float-right">+ Create</button>');
                },
                lengthMenu: [5, 10, 20, 50, 100, 250, 500],
                pageLength: 10,
                dom: 'lrtip',
                buttons: [],
                columnDefs: [
                    {
                        targets: [0,6],
                        visible: false,
                        searchable: false,
                        sortable: false,
                    },
                    {
                        targets: 2,
                        render: function (data, type, row, meta) {
                            let returnData = data;
                            returnData += (row[6] == null) ? '' : ' <i class="cil-check-circle text-success"></i>';
                            return returnData;
                        }
                    },
                    {
                        targets: 5,
                        searchable: false,
                        sortable: false,
                        render: function (data, type, row, meta) {
                            const deleteIcon = (currentId == row[0]) ? ("") : ('<i role="button" class="cil-trash btn-delete me-2" data-id="' + row[0] + '"></i>');
                            return '<i class="cil-pencil btn-edit me-2" role="button" data-id="' + row[0] + '"></i>' + deleteIcon ;
                        }
                    },
                ],
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{url('users')}}',
                    type: 'GET',
                },
                scroller: {
                    loadingIndicator: true
                },
            });

            $(document).on('click', '.btn-create', function () {
                $('#usersModalLabel').html('Create form');
                $('#usersForm input').val('');
                $('#usersForm select').children().attr('selected', false);
                $('#usersForm select').children(':first-child').attr('selected', true);
                $('#usersForm input[type="password"]').attr('required', true);
                $('#resendVerifyEmailDiv').addClass('d-none');
                $('#btnModalRegenerate').addClass('d-none');
                resetErrors();
                usersModal.show();
            });

            $(document).on('click', '.btn-edit', function () {
                const id = $(this).attr('data-id');
                $('#usersModalId').val(id);
                $('#usersForm input').val('');
                $('#usersForm select').children().attr('selected', false);
                $('#usersModalLabel').html('Edit form');
                $('#usersForm input[type="password"]').attr('required', false);
                $('#btnModalRegenerate').removeClass('d-none');
                resetErrors();
                getUserRequest(id);
                usersModal.show();
            });

            $(document).on('click', '.btn-delete', function () {
                const id = $(this).attr('data-id');
                $('#usersDeleteModalId').val(id);
                usersDeleteModal.show();
            });

            $(document).on('click', '#usersDeleteModalSubmit', function () {
                const id = $('#usersDeleteModalId').val();
                sendDeleteRequest(id);
            });

            $(document).on('click', '#btnModalRegenerate', function () {
                const id = $('#usersModalId').val();
                const emailVerif = $('#usersModalEmailVerif').val();
                if (emailVerif == '') {
                    $('#warningNoConfirmEmail').removeClass('d-none');
                } else {
                    $('#warningNoConfirmEmail').addClass('d-none');
                }
                usersConfirmRegenerateModal.show();
            });

            $(document).on('click', '#btnModalSave', function () {
                const id = $('#usersModalId').val();
                if (id == '') {
                    sendCreateRequest();
                } else {
                    usersConfirmFormModal.show();
                }
            });

            $(document).on('click', '#usersRegenerateModalSubmit', function () {
                const id = $('#usersModalId').val();
                sendRegenerateRequest(id);
            });

            $(document).on('click', '#resendVerifyEmail', function () {
                const id = $('#usersModalId').val();
                $.ajax({
                    url: '{{url('email/resend')}}/' + id,
                    type: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}"
                    }
                }).done(function (response) {
                    toastEmailSend.show();
                }).fail(function (error) {
                    console.log(error);
                });
            });

            $(document).on('click', '#submitForm', function () {
                const id = $('#usersModalId').val();
                if (id == '') {
                    sendCreateRequest();
                } else {
                    sendEditRequest(id);
                }
            });

            $(document).on('click','#copyToken',function(){
                navigator.clipboard.writeText(document.getElementById("userToken").textContent);
                toastCopy.show();
            });

            function getUserRequest(id) {
                $.ajax({
                    url: '{{url('users')}}/' + id,
                    type: 'GET',

                }).done(function (response) {
                    if (response.status == 200) {
                        let element;
                        response.data['processing_time_sec'] = response.data['processing_time'] % 60;
                        response.data['processing_time_min'] = (response.data['processing_time'] - response.data['processing_time_sec'] )/60;
                        for (let field in response.data) {
                            element = $('input[name="' + field + '"]');
                            if (element.length) {
                                element.val(response.data[field]);
                            }
                        }
                        $('select[name="role"]').children('option[value="' + response.data['role'] + '"]').attr('selected', true);
                        if (response.data['email_verified_at'] == null) {
                            $('#resendVerifyEmailDiv').removeClass('d-none');
                        } else {
                            $('#resendVerifyEmailDiv').addClass('d-none');
                        }
                    }
                }).fail(function (error) {
                    console.log(error);
                });
            }

            function sendCreateRequest() {
                const data = prepareFormData();
                $.ajax({
                    url: '{{url('users')}}',
                    type: 'POST',
                    data
                }).done(function (response) {
                    if (response.status == 200) {
                        usersModal.hide();
                        $('#users-table').DataTable().ajax.reload();
                        $('#userToken').html(response.token);
                        //usersTokenModal.show();
                    }
                }).fail(function (error) {
                    setErrors(error.responseJSON.errors);
                });
            }

            function sendEditRequest(id) {
                const data = prepareFormData();
                $.ajax({
                    url: '{{url('users')}}/' + id,
                    type: 'PUT',
                    data
                }).done(function (response) {
                    if (response.status == 200) {
                        usersModal.hide();
                        $('#users-table').DataTable().ajax.reload();
                    }
                }).fail(function (error) {
                    setErrors(error.responseJSON.errors);
                });
            }

            function sendDeleteRequest(id) {
                $.ajax({
                    url: '{{url('users')}}/' + id,
                    type: 'DELETE',
                    data: {
                        "_token": "{{ csrf_token() }}"
                    }
                }).done(function (response) {
                    if (response.status == 200) {
                        usersDeleteModal.hide();
                        $('#users-table').DataTable().ajax.reload();
                    }
                }).fail(function (error) {
                    console.log(error);
                });
            }

            function sendRegenerateRequest(id) {
                $.ajax({
                    url: '{{url('users')}}/' + id + '/regenerate',
                    type: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}"
                    }
                }).done(function (response) {
                    if (response.status == 200) {
                        usersConfirmRegenerateModal.hide();
                        $('#userToken').html(response.token);
                        usersTokenModal.show();
                    }
                }).fail(function (error) {
                    console.log(error);
                });
            }

            function setErrors(errors) {
                let input;
                for (let field in errors) {
                    input = $('#usersForm input[name="' + field + '"]');
                    input.addClass('is-invalid');
                    for (let i = 0; i < errors[field].length; i++) {
                        $("#" + input.attr('id') + "Feedback").append(errors[field][i]);
                    }
                }
            }

            function prepareFormData() {
                let field;
                let data = {};
                $('#usersForm input, #usersForm select').each(function () {
                    field = $(this).attr('name');
                    data[field] = $(this).val();
                    $(this).removeClass('is-invalid');
                    $("#" + $(this).attr('id') + "Feedback").html('');
                });
                data._token = '{{ csrf_token() }}';
                return data;
            }

            function resetErrors() {
                $('#usersForm input').each(function () {
                    $(this).removeClass('is-invalid');
                    $("#" + $(this).attr('id') + "Feedback").html('');
                });
            }

            /*$('.modal').on('show.bs.modal', function (e) {
                if($('body').children('.modal-backdrop.fade.show').length){
                    $(this).attr('style','z-index:1055')
                    $('body').append('<div class="modal-backdrop fade show" style="z-index:1051"></div>');
                }else{
                    $('body').append('<div class="modal-backdrop fade show"></div>');
                }
            })

            $('.modal').on('hide.bs.modal', function (e) {
                $('body .modal-backdrop.fade.show:last-child').remove();
            })*/
        });
    </script>
@endpush
