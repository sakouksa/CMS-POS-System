<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $fillable = ['name', 'code', 'is_default'];

    public function translations()
    {
        return $this->hasMany(Translation::class, 'lang_id');
    }
}
