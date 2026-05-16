<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class SantriWali extends Pivot
{
    protected $table = 'santri_wali';

    public $timestamps = false;

    protected $fillable = ['santri_id', 'wali_id', 'hubungan'];
}
