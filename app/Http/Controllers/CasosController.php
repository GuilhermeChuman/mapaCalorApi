<?php

namespace App\Http\Controllers;

use \stdClass;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Casos;



class CasosController extends Controller
{
    public function index()
    {
        $data = DB::table('casos')
            ->join('bairros', 'casos.idBairro', '=', 'bairros.id')
            ->select('casos.*', 'bairros.nome')
            ->get();

        return $data;
    }

    public function importCasos(Request $request)
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
            $dados['dataOcorrencia'] = $item[4]; 
            $dados['idBairro'] = $item[8];
            $dados['idade'] = $item[0];
            $dados['resultado'] = $item[7];

            array_push($entry, $dados);
    
        }

        Casos::insert($entry);
    }

    public function casosByBairro()
    {
        $data = DB::table('casos')
            ->join('bairros', 'casos.idBairro', '=', 'bairros.id')
            ->select(DB::raw('count(*) as nCasos, bairros.nome'))
            ->groupBy('bairros.nome')
            ->get();

        return $data;
    }

    public function casosByMes()
    {
        $ano = date('Y');

        $data = DB::table(DB::raw("(Select * from
                                    casos
                                    where dataOcorrencia between '".$ano."-01-01' and '".$ano."-12-31'
                                    and resultado = 'POSITIVO') as casos2022"))
            ->select(DB::raw(' Month(casos2022.dataOcorrencia) as Mes, count(Month(casos2022.dataOcorrencia)) as nCasos'))
            ->groupBy(DB::raw("month(casos2022.dataOcorrencia)"))
            ->get();

        return $data;
    }

    public function casosByIdade()
    {
        $ano = date('Y');

        $data = DB::table(DB::raw("(Select * from
                                    casos
                                    where dataOcorrencia between '".$ano."-01-01' and '".$ano."-12-31'
                                    and resultado = 'POSITIVO') as casos2022"))
            ->select(DB::raw('casos2022.idade as Idade, count(casos2022.idade) as nCasos'))
            ->groupBy(DB::raw("casos2022.idade"))
            ->get();

        return $data;
    }

    public function getCasosPeriodSemCoordenada($dataAnterior, $dataAtual){

        $data1 = DB::table('bairros')
            ->leftJoin('casos', 'casos.idBairro', '=', 'bairros.id')
            ->select(DB::raw('IFNULL( casos.id, 0) as nCasos, bairros.nome'))
            ->whereNull('dataOcorrencia');
        
        $data2 = DB::table('casos')
            ->leftJoin('bairros', 'casos.idBairro', '=', 'bairros.id')
            ->select(DB::raw('count(*) as nCasos, bairros.nome'))
            ->whereBetween('dataOcorrencia', [$dataAnterior, $dataAtual])
            ->groupBy('bairros.nome')->union($data1)->get();

        return $data2;
    }

    public function getCasosPeriod($dataAnterior, $dataAtual){

        $data1 = DB::table('bairros')
            ->leftJoin('casos', 'casos.idBairro', '=', 'bairros.id')
            ->select(DB::raw('IFNULL( casos.id, 0) as nCasos, bairros.nome, bairros.coordenadas'))
            ->whereNull('dataOcorrencia');
        
        $data2 = DB::table('casos')
            ->leftJoin('bairros', 'casos.idBairro', '=', 'bairros.id')
            ->select(DB::raw('count(*) as nCasos, bairros.nome, bairros.coordenadas'))
            ->where('resultado','=','POSITIVO')
            ->whereBetween('dataOcorrencia', [$dataAnterior, $dataAtual])
            ->groupBy('bairros.nome','bairros.coordenadas')->union($data1)->get();

        return $data2;
    }

    public function casosNoMesGeoJson(){

        $dataAtual = date("Y-m-d");
        $dataAnterior = strtotime('-1 month', strtotime($dataAtual));
        $dataAnterior = date("Y-m-d", $dataAnterior);

        $data = $this->getCasosPeriod($dataAnterior, $dataAtual);

        $data = $this->setGeoJson($data);

        return json_encode($data);
    }

    public function casosNoMes(){

        $dataAtual = date("Y-m-d");
        $dataAnterior = strtotime('-1 month', strtotime($dataAtual));
        $dataAnterior = date("Y-m-d", $dataAnterior);

        $data = $this->getCasosPeriodSemCoordenada($dataAnterior, $dataAtual);

        return $data;
    }


    public function casosByBairroGeoJson()
    {
        $data = DB::table('casos')
            ->join('bairros', 'casos.idBairro', '=', 'bairros.id')
            ->select(DB::raw('count(*) as nCasos, bairros.nome, bairros.coordenadas'))
            ->groupBy('bairros.nome', 'bairros.coordenadas')
            ->get();

        $response = json_encode($this->setGeoJson($data));

        return $response;
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

            $properties->Bairro = $dataItem->nome;
            $properties->Casos = $dataItem->nCasos;

            $features->type = "Feature";
            $features->properties = $properties;

            $features->geometry = $this->setCoordinates($dataItem->coordenadas);

            array_push($itemFeature,  $features);

            unset($properties);
            unset($features);
        }

        $dataObj->type = "FeatureCollection";
        $dataObj->features = $itemFeature;

        return $dataObj;
    }
}
