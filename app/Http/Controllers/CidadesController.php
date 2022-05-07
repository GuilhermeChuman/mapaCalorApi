<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cidades;

class CidadesController extends Controller
{
    public function index()
    {
        $data = Cidades::all()->toArray();

        return $data;
    }
}
