<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Estimation extends Model
{
    //

    use HasFactory;

    protected $fillable = [
        'team_id',
        'position',
        'predicted_points',
    ];
}
