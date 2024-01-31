@extends('layouts.authBase')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card-group">
                    <div class="card p-4">
                        <div class="card-body">
                            <h1>Validation</h1>
                            <p class="text-muted">Please enter Two Factor Validation Password</p>
                            <form method="POST" action="{{ route('validate2fa') }}">
                                @csrf
                                <div class="input-group input-group-lg mb-3">
                                    <div class="input-group-prepend">
                                      <span class="input-group-text">
                                        <svg class="c-icon">
                                          <use xlink:href="assets/icons/coreui/free-symbol-defs.svg#cui-lock-locked"></use>
                                        </svg>
                                      </span>
                                    </div>
                                    <input class="form-control l" type="text" placeholder="{{ __('XXXXXX') }}"
                                           name="one_time_password" required autofocus>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <button class="btn btn-primary px-4 float-right" type="submit">{{ __('Validate') }}</button>
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
