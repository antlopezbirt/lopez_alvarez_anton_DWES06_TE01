<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Item;
use App\Models\ExternalId;
use App\Models\DTOs\ItemDTO;

use TypeError;

class ItemController extends Controller
{

    public function __construct() {}


    // Obtiene todos los ítems de la colección y los devuelve en una respuesta JSON
    public function getAll() {

        // Recupera todos los ítems de la BD
        $itemModels = Item::all();

        // Devuelve la respuesta con un array de DTOs o un 404
        if(count($itemModels)>0) {

            $itemsDTO = [];
            foreach($itemModels as $itemModel) {
                // Obtiene un DTO a partir del modelo
                $itemDTO = $this->getItemDTOByModel($itemModel);
                $itemsDTO[] = $itemDTO;
            }

            // Ya no necesitamos la utilidad ApiJsonResponse, Laravel tiene incorporada esta función
            return response()->json([
                'status' => 'OK',
                'code' => 200,
                'description' => 'Todos los ítems (' . count($itemsDTO) . ')',
                'data' => $itemsDTO
            ]);
        } else {
            return response()->json([
                'status' => 'Not Found',
                'code' => 404,
                'description' => 'No hay ítems',
                'data' => null
            ]);
        }

    }

    // Busca un item por ID y lo devuelve en la respuesta
    public function getById(int $id, Request $request) {

        // Validación: si el ID no existe en la tabla se devuelve directamente un 404
        $validator = Validator::make($request->route()->parameters(), [
            'id' => ['required', 'exists:items']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'Not Found',
                'code' => 404,
                'description' => 'No existe un ítem con ese ID',
                'data' => null
            ]);
        }

        $itemModel = Item::find($id);

        // Devuelve el DTO
        $itemDTO = $this->getItemDTOByModel($itemModel);
        return response()->json([
            'status' => 'OK',
            'code' => 200,
            'description' => 'Ítem con el ID solicitado',
            'data' => $itemDTO
        ]);
    }

    // Busca los ítems de un artista y los devuelve en la respuesta
    public function getByArtist(string $artist) {

        $artist = ucwords(str_replace('-', ' ', $artist));
        $itemModels = Item::where('artist', $artist)->get();

        // Si hay ocurrencias, se devuelven, en caso contrario un 404.
        if(count($itemModels)>0) {

            $itemsDTO = [];
            foreach($itemModels as $itemModel) {
                // Obtiene un itemDTO a partir del modelo devuelto por el ORM
                $itemDTO = $this->getItemDTOByModel($itemModel);
                $itemsDTO[] = $itemDTO;
            }

            return response()->json([
                'status' => 'OK',
                'code' => 200,
                'description' => 'Todos los ítems del artista ' . $artist . ' (' . count($itemsDTO) . ')',
                'data' => $itemsDTO
            ]);
        } else {
            return response()->json([
                'status' => 'Not Found',
                'code' => 404,
                'description' => 'No hay ítems de ese artista (' . $artist . ')',
                'data' => null
            ]);
        }
    }

    // Busca los ítems de un formato y los devuelve en la respuesta
    public function getByFormat(string $format) {

        $itemModels = Item::where('format', $format)->get();

        // Si hay ocurrencias, se devuelven, en caso contrario un 404.
        if(count($itemModels)>0) {

            $itemsDTO = [];
            foreach($itemModels as $itemModel) {
                // Obtiene un itemDTO a partir del modelo devuelto por el ORM
                $itemDTO = $this->getItemDTOByModel($itemModel);
                $itemsDTO[] = $itemDTO;
            }

            return response()->json([
                'status' => 'OK',
                'code' => 200,
                'description' => 'Todos los ítems con formato ' . $format . ' (' . count($itemsDTO) . ')',
                'data' => $itemsDTO
            ]);
        } else {
            return response()->json([
                'status' => 'Not Found',
                'code' => 404,
                'description' => 'No hay ítems con ese formato (' . $format . ')',
                'data' => null
            ]);
        }
    }

    // Ordena y devuelve todos los ítems según el criterio recibido (columna y sentido del orden)
    public function sortByKey(string $columna, string $orden, Request $request) {

        // Validación: si la columna no existe o el orden no es asc|desc, se devuelve un 400 con el mensaje de error
        $ordenables = ['id','title','artist','format','year','origYear','label','rating', 'comment','buyPrice', 'condition','sellPrice'];
        $validator = Validator::make($request->route()->parameters(), [
            'key' => ['required', 'in:' . implode(',', $ordenables)],
            'order' => ['required', 'regex:/^asc|desc$/i']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'Bad Request',
                'code' => 400,
                'description' => 'Los parámetros son incorrectos, revise la documentación',
                'data' => $request->route()->parameters()
            ]);
        }

        $columna = strtolower($columna);
        $orden = strtolower($orden);

        $itemModels = Item::orderBy($columna, $orden)->get();

        // Devuelve la respuesta con un array de DTOs o un 404
        if(count($itemModels)>0) {

            $itemsDTO = [];
            foreach($itemModels as $itemModel) {
                // Obtiene un DTO a partir del modelo
                $itemDTO = $this->getItemDTOByModel($itemModel);
                $itemsDTO[] = $itemDTO;
            }

            // Ya no necesitamos la utilidad ApiJsonResponse, Laravel tiene incorporada esta función
            return response()->json([
                'status' => 'OK',
                'code' => 200,
                'description' => 'Todos los ítems ordenados según el criterio solicitado (' . $columna . ', ' . $orden . ')',
                'data' => $itemsDTO
            ]);
        } else {
            return response()->json([
                'status' => 'Internal Server Error',
                'code' => 500,
                'description' => 'No hay ítems',
                'data' => null
            ]);
        }
    }


    // Guarda un nuevo item en la BD y en caso de exito lo devuelve con un 201
    public function create(Request $request) {

        $parametros = json_decode($request->getContent(), 1);

        // Validación del payload (mismos criterios que en la versión de PHP nativo)
        $request['arrayConditions'] = array("M","NM","E","VG","G","P");
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string'],
            'artist' => ['required', 'string'], 
            'format' => ['required', 'string'],
            'year' => ['required', 'integer', 'gt:1900', 'lt:2156'],
            'origYear' => ['required', 'integer', 'gte:1900', 'lt:2156'],
            'label' => ['required', 'string'],
            'rating' => ['required', 'gte:1', 'lte:10'], 
            'comment' => ['required', 'string'], 
            'buyPrice' => ['required', 'decimal:0,2', 'gte:0'],
            'condition' => ['required', 'in_array:arrayConditions.*'],
            'sellPrice' => ['decimal:0,2', 'gte:0'],
            'externalIds' => ['array']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'Bad Request',
                'code' => 400,
                'description' => 'Los parámetros son incorrectos, revise la documentación',
                'data' => $parametros
            ]);
        }
        
        // Si los datos validan, se procede con la lógica de persistencia del Item
        // Modela los datos recibidos a un Item y lo guarda en la BD
        $itemModel = Item::create(
            [
                'title' => $parametros['title'],
                'artist' => $parametros['artist'], 
                'format' => $parametros['format'],
                'year' => $parametros['year'],
                'origyear' => $parametros['origYear'],
                'label' => $parametros['label'],
                'rating' => $parametros['rating'], 
                'comment' => $parametros['comment'], 
                'buyprice' => $parametros['buyPrice'],
                'condition' => $parametros['condition'],
                'sellprice' => $parametros['sellPrice']
            ]
        );

        // Modela los externalIds y los guarda en la BD
        foreach($parametros['externalIds'] as $supplier => $value) {

            ExternalId::create(
                [
                    'supplier' => $supplier,
                    'value' => $value,
                    'item_id' => $itemModel->id,
                ]
            );
        }

        // Si se ha logrado insertar el ítem, se devuelve el DTO con un 201, si no un 500.
        if ($itemModel) {

            // Obtiene el DTO del ítem creado
            $itemDTO = $this->getItemDTOByModel($itemModel);

            return response()->json([
                'status' => 'Created',
                'code' => 201,
                'description' => 'Ítem guardado',
                'data' => $itemDTO
            ]);
        } else {
            return response()->json([
                'status' => 'Internal Server Error',
                'code' => 500,
                'description' => 'No se pudo guardar el ítem',
                'data' => null
            ]);
        }
    }

    // Actualiza datos de un item existente. No tienen por que recibir todos los campos, solo los que cambian.
    public function update(int $id, Request $request) {

        $parametros = json_decode($request->getContent(), 1);

        // Validación: En primer lugar se valida el ID, único valor requerido
        $validator = Validator::make($request->route()->parameters(), [
            'id' => ['required', 'exists:items']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'Not Found',
                'code' => 404,
                'description' => 'No existe un ítem con ese ID',
                'data' => null
            ]);
        }

        // Validación: A continuación se valida el resto de parámetros
        $request['arrayConditions'] = array("M","NM","E","VG","G","P");
        $validator = Validator::make($request->all(), [
            'title' => ['string'],
            'artist' => ['string'], 
            'format' => ['string'],
            'year' => ['integer', 'gt:1900', 'lt:2156'],
            'origYear' => ['integer', 'gt:1900', 'lt:2156'],
            'label' => ['string'],
            'rating' => ['gte:1', 'lte:10'], 
            'comment' => ['string'], 
            'buyPrice' => ['decimal:0,2', 'gte:0'],
            'condition' => ['in_array:arrayConditions.*'],
            'sellPrice' => ['decimal:0,2', 'gte:0'],
            'externalIds' => ['array']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'Bad Request',
                'code' => 400,
                'description' => 'No se pudo actualizar el ítem: los datos están mal formados',
                'data' => $parametros
            ]);
        }

        // Recupera el modelo de la BD
        $itemModel = Item::find($id);

        // Aplica los cambios
        foreach($parametros as $columna => $valorActualizado) {

            // Si la columna no existe o no está permitida (ID), se saca un 400
            $columnasValidas = array('title','artist','format','year','origYear','label','rating', 'comment','buyPrice', 'condition','sellPrice', 'externalIds');
            if(!in_array($columna, $columnasValidas)) {
                return response()->json([
                    'status' => 'Bad Request',
                    'code' => 400,
                    'description' => 'No se pudo actualizar el ítem: los datos están mal formados',
                    'data' => $parametros
                ]);
            }

            // Los externalIds se actualizan en su propia tabla, no aquí
            if($columna != 'externalIds') {
                $columnaMins = strtolower($columna);
                $itemModel->$columnaMins = $valorActualizado;
            }
        }

        // Actualiza el modelo
        $itemModel->save();

        // En el caso de recibir externalIds, deben borrarse de la BD los que existan previamente
        if(array_key_exists('externalIds', $parametros)) {

            ExternalId::where('item_id', $id)->delete();

            // A continuación se insertan los que se hayan recibido del cliente
            foreach($parametros['externalIds'] as $supplier => $value) {

                ExternalId::create(
                    [
                        'supplier' => $supplier,
                        'value' => $value,
                        'item_id' => $id,
                    ]
                );
            }
        }
        

        // Por último se crea el itemDTO con los datos actualizados y se devuelve
        $itemDTOActualizado = $this->getItemDTOByModel($itemModel);

        // Envía la respuesta
        return response()->json([
            'status' => 'No Content',
            'code' => 204,
            'description' => 'Ítem actualizado',
            'data' => $itemDTOActualizado
        ]);

    }


    // Elimina un ítem a partir del ID recibido en el body de la petición
    public function delete(Request $request) {

        $parametros = json_decode($request->getContent(), 1);

        // Validación: En primer lugar se valida el ID, que es el único valor requerido.
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'exists:items']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'Not Found',
                'code' => 404,
                'description' => 'No existe un ítem con ese ID',
                'data' => null
            ]);
        }

        // Antes de borrar el ítem, se crea con él un DTO para la respuesta
        $itemModelAEliminar = Item::find($parametros['id']);
        $itemDTOAEliminar = $this->getItemDTOByModel($itemModelAEliminar);

        // Los modelos ExternalId que pertenezcan a este Item se eliminarán también por ON DELETE CASCADE
        $itemEliminado = Item::destroy($parametros['id']);

        // Envía las respuestas correspondientes
        if ($itemEliminado) {
            return response()->json([
                'status' => 'No Content',
                'code' => 204,
                'description' => 'Item eliminado',
                'data' => $itemDTOAEliminar
            ]);
        } else {
            return response()->json([
                'status' => 'Internal Server Error',
                'code' => 500,
                'description' => 'No se pudo eliminar el ítem',
                'data' => null
            ]);
        }
        
    }


    // ------------------------- Funciones auxiliares -------------------------


    // Mapea un modelo Item a un DTO
    private function getItemDTOByModel(Item $itemModel): ItemDTO {

        // Obtiene los modelos externalIds pertenecientes al ítem
        $externalIdModels = Item::find($itemModel->id)->externalIds;

        // Se extraen las columnas que nos interesan de ExternalId y se guardan en un array para añadirlo al DTO
        $externalIdsArray = [];

        foreach($externalIdModels as $externalId) {
            $externalIdsArray[$externalId->supplier] = $externalId->value;
        }

        $itemDTO = new ItemDTO(
            $itemModel->title,
            $itemModel->artist,
            $itemModel->format,
            $itemModel->year,
            $itemModel->origyear,
            $itemModel->label,
            $itemModel->rating,
            $itemModel->comment,
            $itemModel->buyprice,
            $itemModel->condition,
            $itemModel->sellprice,
            $externalIdsArray
        );

        return $itemDTO;
    }
}