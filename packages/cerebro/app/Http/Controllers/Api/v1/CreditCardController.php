<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiCreditCardStoreRequest;
use App\Http\Requests\ApiUnsubscribeCardRequest;
use App\Models\CardSiteItems;
use App\Models\ClickCard;
use App\Models\CreditCard;
use App\Models\Sites;
use App\Models\VisitorCard;
use App\Services\FocusMarketingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CreditCardController extends Controller
{
    /**
     * Create resource
     *
     * @param ApiCreditCardStoreRequest $request
     * @return JsonResponse
     */
    public function store(ApiCreditCardStoreRequest $request)
    {
        $data = $request->all();
        $data['referral_id'] = auth()->user()->id;
        $creditCard = CreditCard::updateOrCreate([
            'email' => $request->input('email')
        ],$data);

        if(config('dnm.sendFM')){
            $response = array('status' => 'creditCard');
            $fm = new FocusMarketingService();
            $fm->sendRequest($creditCard, $response);
        }

        return response()->json($creditCard->id, 200);
    }
    public function get_card_items()
    {
        $user = auth()->user();
        $site = Sites::where('form_id',$user['id'])->first();
        $items = CardSiteItems::where('site_id',$site->id)->get();
        foreach ($items as &$item) {
            $item['image'] = Storage::disk('sitesResources')->url($item['image']);
        }
        return response()->json($items, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ApiUnsubscribeCardRequest $request
     * @return JsonResponse|void
     */
    public function unsubscribe(ApiUnsubscribeCardRequest $request)
    {
        $creditCard = CreditCard::where('email',$request->email)->first();
        if (empty($creditCard)) {
            return response()->json(['errors' => ['email' => ['No data found']]], 422);
        }
        $creditCard->delete();
    }

    public function visitorCardHandler(Request $request)
    {
        if($request->input('click_id') === 'null'){
            $request->query->remove('click_id');
        }
        $visitor = VisitorCard::firstOrCreate(
            [
                'ip_address' => $request->input('ip_address'),
                'click_id' => $request->input('click_id'),
                'referral_id' => $request->user()->id,
                'user_agent' => $request->input('user_agent'),
                'date' => today(),
            ],
            [
                'url' => $request->input('url'),
                'visits_amount' => 1,
                'sub_ids'=> json_decode($request->input('sub_ids'), true, JSON_THROW_ON_ERROR),
                'source_url' => $request->input('source_url'),
            ]
        );
        if ($visitor->wasRecentlyCreated === false) {
            if($request->input('sub_ids')){
                $visitor->sub_ids = json_decode($request->input('sub_ids'), true, JSON_THROW_ON_ERROR);
                $visitor->save();
            }
            $visitor->increment('visits_amount');
        }
        return response()->json($visitor->id, 200);
    }

    public function clickCardHandler(Request $request)
    {
        if($request->input('click_id') === 'null'){
            $request->query->remove('click_id');
        }
        $click = ClickCard::firstOrCreate(
            [
                'referral_id' => $request->user()->id,
                'visitor_card_id' => $request->input('visit_id'),
                'customer_credit_card_id' => $request->input('customer_id'),
                'card_site_item_id' => $request->input('item_id'),
                'date' => today(),
            ],
            [
                'click_amount' => 1,
            ]
        );
        if ($click->wasRecentlyCreated === false) {
            $click->increment('click_amount');
        }
        return response()->json([], 200);
    }
}
