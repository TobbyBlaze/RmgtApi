<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    //Table name
    protected $table = 'trades';
    //Primary Key
    public $primaryKey = 'id';
    //Timestamps
    public $timestamps = true;

    protected $fillable = [
        // 'id',
        'user_id',
        'user_name',
        'trade',

    ];

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function trades(){
        return $this->belongsTo('App\User');
    }
}
