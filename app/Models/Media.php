<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = [
        'file_path', 'file_type', 'mediable_id', 'mediable_type'
    ];

    public function mediable()
    {
        return $this->morphTo();
    }

    public function getUrlAttribute()
    {
        return asset('storage/' . $this->file_path);
    }

}
