<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResortPricings extends Model
{
    use HasFactory;

    protected $table = "resort_pricing";

    protected $primaryKey = "id";

    public $timestamps = false;

    public function userCreated()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}
