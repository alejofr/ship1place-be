<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipement extends Model
{
    use HasFactory;

     /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'shipment_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'sender_id',
        'receiver_id',
        'service_name',  // dhl, fedex, purolator  information
        'price',
        'currency',
        'tracking_number',
        'service',
        'pieces',
        'shipment_response',
        'pickup_request',
        'pickup_response',
        'pickup_cancel',
        'status',  // pendiente por pagar ( outstanding ), pagada ( paid ) y  cancelada ( canceled )
        's_date',
    ];

    

    protected $table = 'shipments';

    public function user(){ return $this->belongsTo('App\Models\User'); }
}
