<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Contact;
use Illuminate\Database\Eloquent\SoftDeletes;

class Anagraphic extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'notes', 'photo', 'deleted'];

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class)->where('deleted', 0);
    }

    protected $dates = ['deleted_at'];
}
