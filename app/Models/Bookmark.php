<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;         
use App\Models\Scholarship;  

class Bookmark extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'scholarship_id',
        'notified_at',
    ];

    public function scholarship()
    {
        return $this->belongsTo(Scholarship::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
