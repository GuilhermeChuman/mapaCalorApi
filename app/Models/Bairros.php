<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Cidades;
use App\Models\Casos;

class Bairros extends Model
{
    use HasFactory;

    protected $table = 'bairros';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nome',
        'coordenadas',
        'idCidade'
    ];

    public function cidades()
    {
        return $this->hasOne(Cidades::class, 'idCidade', 'id');
    }

    public function casos()
    {
        return $this->belongsTo(Casos::class, 'idBairro', 'id');
    }
}
