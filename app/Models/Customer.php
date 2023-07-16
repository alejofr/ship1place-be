<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

     /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'customer_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'province_id',
        'country_id',
        'city_id',
        'name',
        'contact_name',
        'email',
        'address1',
        'address2',
        'zip',
        'phone',
        'is_active',
        'type_customer', // SENDER or RECEIVER
        'extra',
    ];

    protected $table = 'customers';

    public function country(){ return $this->belongsTo('App\Models\Country'); }
    public function province(){ return $this->belongsTo('App\Models\Province'); }
    public function city(){ return $this->belongsTo('App\Models\City'); }
    public function user(){ return $this->belongsTo('App\Models\User'); }
}
