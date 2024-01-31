<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class CardSiteItems extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'image',
        'description',
        'btn_color_first',
        'btn_color_second',
        'btn_text',
        'btn_url',
        'stars',
        'benefits'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */

    protected $casts = [
        'benefits' => 'array'
    ];

    protected $dates = [
    ];

    protected $attributes = [
    ];

    public function site()
    {
        return $this->belongsTo(Sites::class, 'site_id', 'id');
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(ClickCard::class,'card_site_item_id','id');
    }

    public function uploadImage()
    {
            $ext = '.' . $this->image->extension();
            $this->image = $this->image->storeAs($this->site->domain_name, 'card_items/card_item_image_' . $this->id . $ext, ['disk' => 'sitesResources']);
            $this->save();
    }

    public function deleteItemImage()
    {
        if (!empty($this->image) && Storage::disk('sitesResources')->exists($this->image)) {
            Storage::disk('sitesResources')->delete($this->image);
        }
    }
}
