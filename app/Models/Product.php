<?php

namespace App\Models;

use App\Models\Category;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        // 'category_id',
        'location_id',
        'title',
        'description',
        'price',
        'condition',
        'status',
        'quantity',
        'image',
    ];

    protected $appends = ['image_url'];

public function getImageUrlAttribute()
{
    return $this->image
        ? asset('storage/' . $this->image)
        : null;
}

    public function seller()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function location()
{
    return $this->belongsTo(Location::class);
}
}
