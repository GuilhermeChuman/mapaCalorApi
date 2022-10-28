<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mensagens extends Model
{
    use HasFactory;

    protected $table = 'mensagens';
    protected $primaryKey = 'id';

    protected $fillable = [
        'destinatario',
        'mensagem',
        'aprovado'
    ];
    
}
