<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use GregoryDuckworth\Encryptable\EncryptableTrait;

class Journal extends Model
{
    use EncryptableTrait;

    //Table name
    protected $table = 'journals';
    //Primary Key
    public $primaryKey = 'id';
    //Timestamps
    public $timestamps = true;

	/**
	 * Encryptable Rules
	 *
	 * @var array
	 */
	protected $encryptable = [
		// 'id',
        'user_id',
        // 'user_name',
        'journal',
	];

    protected $fillable = [
        // 'id',
        'user_id',
        // 'user_name',
        'journal',

    ];

    public function user(){
        return $this->belongsTo('App\Models\User');
    }

    public function journals(){
        return $this->belongsTo('App\Models\User');
    }

}
