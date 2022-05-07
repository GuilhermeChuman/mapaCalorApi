<?php

namespace App\Http\Controllers;

use \stdClass;

use Illuminate\Http\Request;
use App\Models\Bairros;

class BairrosController extends Controller
{
    public function index()
    {
        $data = Bairros::all()->toArray();

        return $data;
    }

    public function import(Request $request)
    {
        $data = $request->all();

        $path = $request->file('file')->getRealPath();
        $records = array_map('str_getcsv', file($path));

        if (! count($records) > 0) {
           return 'Error...';
        }

        $entry = [];
        
        foreach ($records as $item){

            $dados;
            $dados['nome'] = $item[0]; 
            $dados['idCidade'] = 1;

            array_push($entry, $dados);
    
        }

        Bairros::insert($entry);
    }

    public function indexGeoJson()
    {
        $data = Bairros::all()->toArray();

        $data = json_encode($this->setGeoJson($data));

        return $data;
    }

    public function setCoordinates($data){

        $geometry = new stdClass();

        $coord = explode(',', $data);
            $item = [];
            $coodArray = [];
            $i = 0;

            foreach ($coord as $valor){
                if(($i % 2 == 0) && ($i != 0)){
                    array_push($coodArray,  $item);
                    $item = [];
                }
                array_push($item, $valor);
                $i++;
            }

        $geometry->type = 'Polygon';
        $geometry->coordinates = [$coodArray];

        return $geometry;

    }
    
    public function setGeoJson($data){
        
        $dataObj = new stdClass();
        $itemFeature = [];

        foreach ($data as $dataItem){

            $properties = new stdClass();
            $features = new stdClass();

            $properties->Bairro = $dataItem['nome'];
            $properties->Casos = '';

            $features->type = "Feature";
            $features->properties = $properties;

            $features->geometry = $this->setCoordinates($dataItem['coordenadas']);

            array_push($itemFeature,  $features);

            unset($properties);
            unset($features);
        }

        $dataObj->type = "FeatureCollection";
        $dataObj->features = $itemFeature;

        return $dataObj;
    }
}
