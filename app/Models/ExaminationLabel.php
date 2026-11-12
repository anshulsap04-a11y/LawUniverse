<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExaminationLabel extends Model
{
    protected $fillable = ['name', 'label_type', 'description'];

    public function examinationsFirst(): HasMany
    {
        return $this->hasMany(Examination::class, 'first_label_id');
    }

    public function examinationsSecond(): HasMany
    {
        return $this->hasMany(Examination::class, 'second_label_id');
    }
}
