<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mensagens;

class MensagensController extends Controller
{
    public function index()
    {
        $data = Mensagens::all()->toArray();

        return $data;
    }

    public function aprove($id){

        $mensagem = Mensagens::find($id);

        $mensagem->aprovado = 'S';

        $mensagem->save();

    }

    public function getAproved(){

        $data = DB::table(DB::raw("(Select * from
            mensagens WHERE aprovado = 'S' "))->get();

        return $data;
    }

    public function getNonAproved(){

        $data = DB::table(DB::raw("(Select * from
            mensagens WHERE aprovado = 'N' "))->get();
            
        return $data;
    }



}
