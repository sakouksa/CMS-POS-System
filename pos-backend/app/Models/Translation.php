<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    protected $fillable = ['lang_id', 'table_name', 'table_id', 'field_name', 'translated_value'];

    public function language()
    {
        return $this->belongsTo(Language::class, 'lang_id');
    }
}
