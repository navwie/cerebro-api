<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Google2FA;
class Google2faController extends Controller
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \PragmaRX\Google2FAQRCode\Exceptions\MissingQrCodeServiceException
     * @throws \PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException
     * @throws \PragmaRX\Google2FA\Exceptions\InvalidCharactersException
     * @throws \PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException
     */
    public function generateGoogle2fa()
    {
        $secret = Google2FA::generateSecretKey(32);

        $QR_Image = Google2FA::getQRCodeInline(
            config('app.name'),
            Auth::user()->email,
            $secret,
            300
        );

        return view('google2fa.generate', ['QR_Image' => $QR_Image, 'secret' => $secret]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function enable2fa(Request $request)
    {
        $authUser = Auth::user();

        if (!empty($request->secret)) {
            $authUser->google2fa_secret = $request->secret;
            $authUser->save();
        }

        return redirect('/');
    }
}
