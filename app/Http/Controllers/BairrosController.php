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

        $data = json_encode($this->setCoordinates($data));

        return $data;
    }

    public function setCoordinates($data){
        
        $dataObj = new stdClass();
        $itemFeature = [];

        foreach ($data as $dataItem){

            $properties = new stdClass();
            $features = new stdClass();
            $geometry = new stdClass();

            $properties->Bairro = $dataItem['nome'];
            $properties->Casos = '';

            $coord = explode(',', $dataItem['coordenadas']);
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

            $features->type = "Feature";
            $features->properties = $properties;
            $features->geometry = $geometry;

            array_push($itemFeature,  $features);

            unset($properties);
            unset($features);
            unset($geometry);

        }

        $dataObj->type = "FeatureCollection";
        $dataObj->features = $itemFeature;

        return $dataObj;
    }
}
