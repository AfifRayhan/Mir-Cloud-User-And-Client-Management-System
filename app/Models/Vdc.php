<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vdc extends Model
{
    protected $fillable = [
        'customer_id',
        'vdc_name',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
