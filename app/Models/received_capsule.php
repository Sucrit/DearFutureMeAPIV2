<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class received_capsule extends Model
{
    use HasFactory;

    protected $fillable = [
         'received_capsule_id',
         'user_id'
    ];

    public function capsules()
    {
        return $this->belongsTo(capsules::class, 'received_capsule_id'); // Foreign key to capsules
    }
    
    public function user() {
        return $this->belongsToMany(User::class);
    }
}
