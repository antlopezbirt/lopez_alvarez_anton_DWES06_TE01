<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        if($itemModels) {

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
                'status' => 'Internal Server Error',
                'code' => 500,
                'description' => 'No hay ítems',
                'data' => null
            ]);
        }

    }

    // Busca un item por ID y lo devuelve en la respuesta
    public function getById(int $id) {

        $itemModel = Item::find($id);

        // Devuelve el DTO o un 404
        if ($itemModel) {

            $itemDTO = $this->getItemDTOByModel($itemModel);

            return response()->json([
                'status' => 'OK',
                'code' => 200,
                'description' => 'Ítem con ID ' . $id,
                'data' => $itemDTO
            ]);
        } else {
            return response()->json([
                'status' => 'Not Found',
                'code' => 404,
                'description' => 'No existe un ítem con ID ' . $id,
                'data' => null
            ]);
        }
    }

    // Busca los ítems de un artista y los devuelve en la respuesta
    public function getByArtist(string $artist) {

        $artist = ucwords(str_replace('-', ' ', $artist));
        $itemModels = Item::where('artist', $artist)->get();

        // Si hay ocurrencias, se devuelven, en caso contrario un 404.
        if($itemModels) {

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
        if($itemModels) {

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
    public function sortByKey($columna, $orden) {

        $itemModels = Item::orderBy($columna, $orden)->get();

        // Devuelve la respuesta con un array de DTOs o un 404
        if($itemModels) {

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

        /*
            Validación de los valores del payload.

            Se usan los mismos criterios que en el método de validación
            desarrollado para la versión anterior de la API (chequearValores)
        */

        $request['arrayConditions'] = array("M","NM","E","VG","G","P");

        $validado = $request->validate([
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
        
        // Si los datos validan, se procede con la lógica de persistencia del Item
        if($validado) {

            // Modela los datos recibidos a un Item y lo guarda en la BD
            $itemModel = Item::create(
                [
                    'title' => $validado['title'],
                    'artist' => $validado['artist'], 
                    'format' => $validado['format'],
                    'year' => $validado['year'],
                    'origyear' => $validado['origYear'],
                    'label' => $validado['label'],
                    'rating' => $validado['rating'], 
                    'comment' => $validado['comment'], 
                    'buyprice' => $validado['buyPrice'],
                    'condition' => $validado['condition'],
                    'sellprice' => $validado['sellPrice']
                ]
            );

            // Modela los externalIds y los guarda en la BD
            foreach($validado['externalIds'] as $supplier => $value) {

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

        // Si no valida, se devuelve el error con un 400
        } else {
            return response()->json([
                'status' => 'Bad Request',
                'code' => 400,
                'description' => 'No se pudo crear el ítem: los datos están mal formados',
                'data' => $request->getContent()
            ]);
        }
    }

    // Actualiza datos de un item existente. No tienen por que recibir todos los campos, solo los que cambian.
    public function update(Request $request) {

        /*
            Validación de los valores del payload.

            En primer lugar se valida el ID, que es el único valor requerido.
            El resto de valores son opcionales

            Se usan los mismos criterios que en el método de validación
            desarrollado para la versión anterior de la API (chequearValores)

        */

        $conIdValidado = $request->validate([
            'id' => ['required', 'exists:items']
        ]);

        if($conIdValidado) {

            $request['arrayConditions'] = array("M","NM","E","VG","G","P");

            $validado = $request->validate([
                'id' => ['required', 'exists:items'],
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

            if($validado) {

                // Recupera el modelo de la BD
                $itemModel = Item::find($validado['id']);

                // Aplica los cambios
                foreach($validado as $propiedad => $valorActualizado) {
                    // No se debe editar el ID, y externalIds no pertenece a esta entidad
                    if($propiedad != 'id' && $propiedad != 'externalIds') {
                        $propiedadMins = strtolower($propiedad);
                        $itemModel->$propiedadMins = $valorActualizado;
                    }
                }

                // Actualiza el modelo
                $itemModel->save();

                // En el caso de recibir externalIds, deben borrarse de la BD los que existan previamente
                if(array_key_exists('externalIds', $validado)) {

                    ExternalId::where('item_id', $validado['id'])->delete();

                    // A continuación se insertan los que se hayan recibido del cliente
                    foreach($validado['externalIds'] as $supplier => $value) {

                        ExternalId::create(
                            [
                                'supplier' => $supplier,
                                'value' => $value,
                                'item_id' => $validado['id'],
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

            // Alguno de los datos no ha validado
            } else {
                return response()->json([
                    'status' => 'Bad Request',
                    'code' => 400,
                    'description' => 'No se pudo actualizar el ítem: los datos están mal formados',
                    'data' => $request->getContent()
                ]);
            }

        // No ha encontrado el item con ese ID
        } else {
            return response()->json([
                'status' => 'Bad Request',
                'code' => 404,
                'description' => 'No se pudo actualizar el ítem: no existe ese ID',
                'data' => $request->getContent()
            ]);
        }
    }


    // Elimina un ítem a partir del ID recibido en el body de la petición
    public function delete(Request $request) {

        $validado = $request->validate([
            'id' => ['required', 'exists:items']
        ]);

        // Si existe el ítem a eliminar, procede con la eliminación
        if($validado['id']) {

            // Primero obtiene el DTO para la respuesta
            $itemDTOAEliminar = $this->getItemDTOByModel(Item::find($validado['id']));

            // Los modelos ExternalId que pertenezcan a este Item se eliminarán también por ON DELETE CASCADE
            $itemEliminado = Item::destroy($validado['id']);

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
        
        // Si el ítem no existía, devuelve un 404
        } else {
            return response()->json([
                'status' => 'Not Found',
                'code' => 404,
                'description' => 'No existe un ítem con ese ID',
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