<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use NunoMaduro\Collision\Contracts\Writer;
use PHPUnit\Util\Json;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;

class ColaboratorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $api_key;
    protected $api_private;
    protected $ts;
    public $hash;

    public function __construct()
    {
        $this->api_key = config('app.api_key_public');
        $this->api_private = config('app.api_key_private');
        $this->ts = config('app.ts');
        $this->hash = md5($this->ts . $this->api_private . $this->api_key);
    }
    public function getApi($url)
    {
        $response = Http::get($url);
        return json_decode($response);
        return $response;
    }
    public function index($character = '', $type = 'name')
    {
        try {
            if (empty($character)) {
                $url = 'http://gateway.marvel.com/v1/public/characters?ts=' . $this->ts . '&apikey=' . $this->api_key . '&hash=' . $this->hash . '';
                $response = $this->getApi($url);
                if ($response->code == 200) {
                    if (!empty($response->data->results)) {
                        $data = collect($response->data->results);
                        $data = $data->pluck('name');
                        return $data;
                    } else {
                        return response()->json(['message' => 'Sin resultados'], 409);
                    }
                } else {
                    return response()->json(['message' => 'Recurso no encontrado'], 404);
                }
            } else {

                $character = str_replace(" ", "%20", trim($character));
                $url = 'http://gateway.marvel.com/v1/public/characters?ts=' . $this->ts . '&apikey=' . $this->api_key . '&hash=' . $this->hash . '&' . $type . '=' . ($character);

                $response = $this->getApi($url);
                if ($response->code == 200) {

                    $comics = [];
                    //validar si hay resultados
                    empty($response->data->results) ? $response = $this->getApi($url) : '';
                    if (!empty($response->data->results)) {
                        foreach ($response->data->results as $item) {
                            $comics[] = $item->comics;
                        }

                        $urls_comics = collect($comics)->pluck('collectionURI');

                        $url_comic = $urls_comics->first();
                        $url_comic = $url_comic . '?ts=' . $this->ts . '&apikey=' . $this->api_key . '&hash=' . $this->hash;
                        //obenemos los comics con el id del persinaje
                        $list_comics = $this->getApi($url_comic);
                        //en una coleccion filtramos todos los items para dividir los editores y escritores
                        $list_creators = collect($list_comics->data->results)->pluck('creators.items')->collapse();
                        $editors = [];
                        $writers = [];
                        foreach ($list_creators as $creator) {
                            if ($creator->role == "writer") {
                                $writers[] = $creator->name;
                            }
                            if ($creator->role == "editor") {
                                $editors[] = $creator->name;
                            }
                        }
                        $colaborators = [
                            'editors' => $editors,
                            'writers' => $writers
                        ];
                        return $colaborators;
                    } else {
                        //encaso de que no hay resultados
                        return response()->json(['message' => 'No se encontro resultados '], 204);
                    }
                } else {
                    return response()->json(['message' => 'Recurso no encontrado'], 404);
                }
            }
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => 'Error en la peticion rest, validar parametros', 'code' => 409], 409);
        }
    }

    public function characters($character = '', $type = 'name')
    {
        try {
            if (empty($character)) {
                $url = 'http://gateway.marvel.com/v1/public/characters?ts=' . $this->ts . '&apikey=' . $this->api_key . '&hash=' . $this->hash . '';
                $response = $this->getApi($url);
                return $response;
            } else {


                $character = str_replace(" ", "%20", trim($character));
                $url = 'http://gateway.marvel.com/v1/public/characters?ts=' . $this->ts . '&apikey=' . $this->api_key . '&hash=' . $this->hash . '&' . $type . '=' . ($character);

                $response = $this->getApi($url);
                if ($response->code == 200) {

                    $comics = [];
                    //validar si hay resultados
                    empty($response->data->results) ? $response = $this->getApi($url) : '';
                    if (!empty($response->data->results)) {
                        foreach ($response->data->results as $item) {
                            $comics[] = $item->comics;
                        }

                        $urls_comics = collect($comics)->pluck('collectionURI');

                        $url_comic = $urls_comics->first();
                        $url_comic = $url_comic . '?ts=' . $this->ts . '&apikey=' . $this->api_key . '&hash=' . $this->hash;
                        //obenemos los comics con el id del persinaje
                        $list_comics = $this->getApi($url_comic);
                        $list_comics = collect($list_comics->data->results);
                        $list_characters = $list_comics->pluck('characters.items')->collapse();
                        $list_characters = $list_characters->unique('resourceURI');
                        $object_response = [];
                        $characters = [];
                        foreach ($list_characters as $character) {
                            $url = $character->resourceURI . '?ts=' . $this->ts . '&apikey=' . $this->api_key . '&hash=' . $this->hash;
                            $lists_character_comics = $this->getApi($url);
                            $list_comics_character = collect($lists_character_comics->data->results)->pluck('comics.items')->collapse();
                            $array_comics = $list_comics_character->pluck('name')->toArray();
                            $characters = [
                                'character' => $character->name,
                                'Comics' => $array_comics
                            ];
                            $object_response[] = $characters;
                        }

                        return $object_response;
                        //en una coleccion filtramos todos los items para dividir los editores y escritores
                        //$list_creators = collect($list_comics->data->results)->pluck('creators.items')->collapse();
                    } else {
                        //encaso de que no hay resultados
                        return response()->json(['message' => 'No se encontro resultados '], 404);
                    }
                } else {
                    return response()->json(['message' => 'Recurso no encontrado'], 404);
                }
            }
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['message' => 'Error en la peticion rest, validar parametros', 'code' => 409], 409);
        }
    }
}
