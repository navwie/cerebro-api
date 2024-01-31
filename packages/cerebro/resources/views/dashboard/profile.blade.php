@extends('layouts.base')

@section('content')
    <div class="container-fluid">
        <form id="usersForm" action="/users/{{auth()->user()->id}}" method="POST">
            @method('PUT')
            @csrf
            <div class="row">
                <div class="col-4">
                    <div class="mb-3">
                        <label for="userName" class="form-label">Name</label>
                        <input type="text" maxlength="256" class="form-control @error('name') is-invalid @enderror"
                               id="userName" name="name" required value="{{old('name') ?? auth()->user()->name}}">
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-4">
                    <div class="mb-3">
                        <label for="userEmail" class="form-label">Email</label>
                        <input type="text" maxlength="256" class="form-control @error('email') is-invalid @enderror"
                               id="userEmail" name="email" required value="{{old('email') ?? auth()->user()->email}}">
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <div class="mb-3">
                        <label for="userPassword" class="form-label">Password</label>
                        <input type="password" maxlength="256"
                               class="form-control @error('password') is-invalid @enderror"
                               id="userPassword" name="password">
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-4">
                    <div class="mb-3">
                        <label for="userPasswordConfirm" class="form-label">Confirm password</label>
                        <input type="password" maxlength="256" class="form-control"
                               id="userPasswordConfirm" name="password_confirmation">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-2">
                    <div class="mb-3">
                        <label for="usersPersonalChannelId" class="form-label">Personal channel
                            id</label>
                        <input type="text" class="form-control" id="usersPersonalChannelId"
                               name="personal_channel_id"
                               value="{{old('personal_channel_id') ?? auth()->user()->personal_channel_id}}">
                    </div>
                </div>
                <div class="col-2">
                    <div class="mb-3">
                        <label for="usersLeadChannelId" class="form-label">Lead channel id</label>
                        <input type="text" class="form-control" id="usersLeadChannelId"
                               name="lead_channel_id"
                               value="{{old('lead_channel_id') ?? auth()->user()->lead_channel_id}}">
                    </div>
                </div>
                <div class="col-2">
                    <div class="mb-3">
                        <label for="usersPostBackAmount" class="form-label">Post back amount</label>
                        <input type="text" class="form-control @error('post_back_amount') is-invalid @enderror"
                               id="usersPostBackAmount"
                               name="post_back_amount"
                               value="{{old('post_back_amount') ?? auth()->user()->post_back_amount}}">
                        @error('post_back_amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-2">
                    <div class="mb-3">
                        <label for="usersPersonalMinReq" class="form-label">Personal min req</label>
                        <input type="text" class="form-control @error('personal_min_req') is-invalid @enderror"
                               id="usersPersonalMinReq"
                               name="personal_min_req"
                               value="{{old('personal_min_req') ?? auth()->user()->personal_min_req}}">
                        @error('personal_min_req')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <div class="mb-3">
                        <label for="usersPersonalPassword" class="form-label">Personal password</label>
                        <input type="text" class="form-control" id="usersPersonalPassword"
                               name="personal_password"
                               value="{{old('personal_password') ?? auth()->user()->personal_password}}">
                    </div>
                </div>
                <div class="col-4">
                    <div class="mb-3">
                        <label for="usersLeadPassword" class="form-label">Lead password</label>
                        <input type="text" class="form-control" id="usersLeadPassword"
                               name="lead_password" value="{{old('lead_password') ?? auth()->user()->lead_password}}">
                    </div>
                </div>
            </div>
            @if(!auth()->user()->hasVerifiedEmail())
                <div>Without email verification, it is impossible to create a token.
                    <span id="resendVerifyEmail" role="button" class="text-decoration-underline">Click here</span> to send a confirmation email.</div><br>
            @endif
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#confirmFormModal">Save</button>
            <button type="button" id="regenerateToken" class="btn btn-success" data-bs-toggle="modal"
                    data-bs-target="#confirmRegenerateModal" {{!auth()->user()->hasVerifiedEmail() ? 'disabled' : ''}}>
                <i class="cil-reload"></i> Regenerate token
            </button>
        </form>
    </div>

    <div class="modal fade" id="tokenModal" tabindex="-1" aria-labelledby="tokenModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tokenModalLabel">Token</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Save this token. If you lose it, we cannot restore it.
                    <span id="userToken"></span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal" aria-label="Close">Ok</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmRegenerateModal" tabindex="-1" aria-labelledby="confirmRegenerateModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmRegenerateModalLabel">Regenerate token</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="regenerateModalId" name="id">
                    Do you confirm that you want to regenerate token?
                    This action remove the remaining tokens.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="regenerateModalSubmit"
                            data-bs-dismiss="modal" aria-label="Close" data-bs-toggle="modal"
                            data-bs-target="#tokenModal">Ok
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmFormModal" tabindex="-1" aria-labelledby="confirmFormModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmRegenerateModalLabel">Confirm form</h5>
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
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        'use strict';
        $(document).ready(function () {
            const toastEmailSend = new bootstrap.Toast(document.getElementById('toastEmailSend'));
            $(document).on('click', '#resendVerifyEmail', function () {
                $.ajax({
                    url: '{{url('email/resend')}}',
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
            $(document).on('click', '#regenerateModalSubmit', function () {
                const id = {{ json_encode(auth()->user()->id) }};
                $.ajax({
                    url: '{{url('users')}}/' + id + '/regenerate',
                    type: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}"
                    }
                }).done(function (response) {
                    if (response.status == 200) {
                        $('#userToken').html(response.token);
                    }
                }).fail(function (error) {
                    console.log(error);
                });
            });
            $(document).on('click','#submitForm',function (){
                $('#usersForm').submit();
            });
        });
    </script>
@endpush
