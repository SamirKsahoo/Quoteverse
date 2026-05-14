<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['name', 'description', 'center_lat', 'center_lng', 'zoom'];

    public function surveys()
    {
        return $this->hasMany(Survey::class);
    }
}