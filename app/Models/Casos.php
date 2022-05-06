<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Bairros;

class Casos extends Model
{
    use HasFactory;

    protected $table = 'casos';
    protected $primaryKey = 'id';

    protected $fillable = [
        'dataOcorrencia',
        'idBairro'
    ];

    public function bairros()
    {
        return $this->hasOne(Bairros::class, 'idBairro', 'id');
    }
}
