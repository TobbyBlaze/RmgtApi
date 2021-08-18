<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    //Table name
    protected $table = 'journals';
    //Primary Key
    public $primaryKey = 'id';
    //Timestamps
    public $timestamps = true;

    protected $fillable = [
        // 'id',
        'user_id',
        'user_name',
        'journal',

    ];

    public function user(){
        return $this->belongsTo('App\Models\User');
    }

    public function journals(){
        return $this->belongsTo('App\Models\User');
    }

}
