<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaptureRequest extends Model
{
    use HasFactory;

    protected $table = "capture_request";

    protected $primaryKey = "id";

    public $timestamps = false;

    public function userCreated()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function resortInfo()
    {
        return $this->hasOne(Resorts::class, 'id', 'resort_id');
    }
}
