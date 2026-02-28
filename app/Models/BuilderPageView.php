<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BuilderPageView extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_id', 'user_id', 'session_id', 'device_type', 'referrer'
    ];

    public function page()
    {
        return $this->belongsTo(BuilderPage::class, 'page_id');
    }
}
