<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScholarshipRule extends Model
{
    protected $fillable = [
        'rule_type',
        'keyword',
        'min_value',
        'max_value',
        'result'
    ];
}
