<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class News extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'content', 'category_id', 'user_id', 'image_url', 'views'];
    public function category() {
    return $this->belongsTo(Category::class);
}

public function author() {
    return $this->belongsTo(User::class, 'user_id');
}
}
