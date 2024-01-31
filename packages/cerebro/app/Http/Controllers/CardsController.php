<?php

namespace app\Http\Controllers;

use App\Http\Requests\CardsRequest;
use App\Models\CardSiteItems;
use App\Models\Sites;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Redis;

class CardsController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function store(CardsRequest $request)
    {
        $site = new Sites($request->all());

        $site->uploadImages($request);

        $user = User::find($site->form_id);
        $site->token = User::regenerateToken($user);
        $site->save();

        Redis::set($site->domain_name, json_encode($site));

        $cardItems = $request->card_item;
        foreach ($cardItems as $item) {

            $cardItem = new CardSiteItems($item);
            $cardItem->site_id = $site->id;
            $cardItem->save();
            $cardItem->uploadImage();
        }

        return response()->json(
            [
                'status' => 200
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param integer $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CardsRequest $request, int $id)
    {
        $site = Sites::find($id);

        $site->uploadImages($request);

        $site->theme = $request->theme;
        $site->title = $request->title;
        $site->header = $request->header;
        $site->sub_header = $request->sub_header;
        $site->sub_header_color = $request->sub_header_color;
        $site->sub_header_color_text = $request->sub_header_color_text;
        $site->card_button_text = $request->card_button_text;
        $site->card_button_color_first = $request->card_button_color_first;
        $site->card_button_color_second = $request->card_button_color_second;

        $cardItems = $request->card_item;
        $oldCardItems = CardSiteItems::select('id')->where(['site_id' => $site->id ])->get()->toArray();
        $oldCardItemsIds = [];

        foreach ($oldCardItems as $item) {
            array_push($oldCardItemsIds, $item['id']);
        }
        $submittedIds = [];
        foreach ($cardItems as $item) {
            if($item['id']) {
                array_push($submittedIds, $item['id']);
                $cardItem = CardSiteItems::find($item['id']);
                $cardItem->update($item);
            } else {
                $cardItem = new CardSiteItems($item);
                $cardItem->site_id = $site->id;
            }

            $cardItem->save();

            if ($cardItem->image instanceof UploadedFile) {
                $cardItem->uploadImage();
            }
        }
        $diff = array_diff($oldCardItemsIds, $submittedIds);
        foreach ($diff as $deletedId) {
            $cardItem = CardSiteItems::find($deletedId);
            $cardItem->deleteItemImage();
            $cardItem->delete();
        }
        $site->update();
        Redis::set($site->domain_name, json_encode($site));

        return response()->json(
            ['status' => 200]
        );
    }
}
