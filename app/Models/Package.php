<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    
     /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'package_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'height',
        'long',
        'width',
        'dimension',
        'extra'
    ];

    protected $table = 'packages';

    public function user(){ return $this->belongsTo('App\Models\User'); }
    
}
