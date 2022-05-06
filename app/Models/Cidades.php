<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Bairros;

class Cidades extends Model
{
    use HasFactory;

    protected $table = 'cidades';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nome',
        'coordenadas'
    ];

    public function bairros()
    {
        return $this->belongsTo(Bairros::class, 'idCidade', 'id');
    }
    
}
