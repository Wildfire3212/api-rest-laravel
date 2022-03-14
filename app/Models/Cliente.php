<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'cliente';
    public $timestamps = false;

    public function clientes(){
        return $this->hasMany('App\Models\Clientes');
    }

    public function pedidos(){
        return $this->hasMany('App\Models\Pedido', 'id_remitente_fk', 'id');
    }
}
