@extends('layouts.authBase')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card-group">
                    <div class="card p-4">
                        <div class="card-body">
                            <h1>Google 2FA</h1>
                            <p class="text-muted">Set up your two factor authentication by scanning the barcode below</p>
                            <p class="text-muted">Alternatively, you can use the code:</p>
                            <h4 class="text-black"><strong>{{ $secret }}</strong></h4>
                            <h4 class="text-danger"><strong>You must set up your Google Authenticator app before continuing. Otherwise, you'll be unable to login</strong></h4>
                            <form method="POST" action="{{ route('enable2fa') }}">
                                @csrf
                            <input type="hidden" name="secret" value="{{ $secret }}"/>
                                    {!! $QR_Image !!}
                                <div class="row">
                                    <div class="col-12">
                                        <button class="btn btn-primary px-4 float-right" type="submit">{{ __('Enable 2Fa') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
