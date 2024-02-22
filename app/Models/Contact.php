<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = ['contact', 'type', 'notes', 'deleted'];

    protected $hidden = [
        'created_at',
        'deleted_at',
        'updated_at',
        'deleted',
    ];

    public function anagraphic(): BelongsTo
    {
        return $this->belongsTo(Anagraphic::class);
    }

    //protected $dates = ['deleted_at'];
}
