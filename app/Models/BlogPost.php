<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    // protected $collection = "";
    protected $fillable = ['title', 'content', 'image', 'published', 'user_id'];
    use HasFactory;
    protected $content;
    public $timestamps = true;
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
