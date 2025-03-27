<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Item;
use App\Models\entities\ItemEntity;
use App\Models\DTOs\ItemDTO;
use App\Models\entities\ExternalIdsEntity;
use TypeError;

class ItemController extends Controller
{

    public function __construct() {}


    // Obtiene todos los ítems de la colección y los devuelve en una respuesta JSON
    public function getAll() {

        $itemModels = Item::all();

        $itemsDTO = [];

        foreach($itemModels as $itemModel) {

            $itemDTO = $this->getItemDTOByModel($itemModel);

            $itemsDTO[] = $itemDTO;
        }

        // Ya no necesitamos la utilidad ApiJsonResponse, Laravel tiene incorporada esta función
        if(isset($itemsDTO)) {
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

    // Busca un item por ID, recaba sus entidades, las mapea a un DTO y lo devuelve en la respuesta
    public function getById($id) {

        // Obtiene un DTO a partir de su ID, o false
        $itemDTO = $this->getItemDTOById($id);

        // Devuelve el DTO o un 404
        if ($itemDTO) {

            // Envía la respuesta
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


    public function getByArtist($artist) {

        $artist = ucwords(str_replace('-', ' ', $artist));

        $itemsCollection = DB::table('items')
            ->where('artist', $artist)
            ->get();

        $itemsDTO = [];

        foreach($itemsCollection as $itemFila) {

            // Obtiene un itemDTO a partir del ID del objeto devuelto por el ORM
            $itemDTO = $this->getItemDTOById($itemFila->id);

            $itemsDTO[] = $itemDTO;
        }

        // Si hay ocurrencias, se devuelven, en caso contrario un 404.
        if(count($itemsDTO)>0) {
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


    public function getByFormat($format) {

        $itemsCollection = DB::table('items')
            ->where('format', $format)
            ->get();

        $itemsDTO = [];

        foreach($itemsCollection as $itemFila) {

            // Obtiene un itemDTO a partir del ID del objeto devuelto por el ORM
            $itemDTO = $this->getItemDTOById($itemFila->id);

            $itemsDTO[] = $itemDTO;
        }

        // Si hay ocurrencias, se devuelven, en caso contrario un 404.
        if(count($itemsDTO)>0) {
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




    public function sortByKey($clave, $orden) {

        $itemsCollection = DB::table('items')
            ->orderBy($clave, $orden)
            ->get();

        $itemsDTO = [];

        foreach($itemsCollection as $itemFila) {

            // Obtiene un itemDTO a partir del ID del objeto devuelto por el ORM
            $itemDTO = $this->getItemDTOById($itemFila->id);

            $itemsDTO[] = $itemDTO;
        }

        // Ya no necesitamos la utilidad ApiJsonResponse, Laravel tiene incorporada esta función
        if(isset($itemsDTO)) {
            return response()->json([
                'status' => 'OK',
                'code' => 200,
                'description' => 'Listado de ítems ordenados según el criterio solicitado (' . $clave . ', ' . $orden . ')',
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

            // Modela los datos a una entidad Item
            $itemEntidad = new ItemEntity (
                0, $validado['title'], $validado['artist'],
                $validado['format'], $validado['year'], $validado['origYear'],
                $validado['label'], $validado['rating'], $validado['comment'],
                $validado['buyPrice'], $validado['condition'], $validado['sellPrice']
            );

            // Inserta la entidad Item en la tabla `items`, obteniendo el ID resultante
            $itemId = DB::table('items')->insertGetId(
                [
                    'title' => $itemEntidad->getTitle(), 'artist' => $itemEntidad->getArtist(),
                    'format' => $itemEntidad->getFormat(), 'year' => $itemEntidad->getYear(),
                    'origyear' => $itemEntidad->getOrigYear(), 'label' => $itemEntidad->getLabel(),
                    'rating' => $itemEntidad->getRating(), 'comment' => $itemEntidad->getComment(),
                    'buyprice' => $itemEntidad->getBuyPrice(), 'condition' => $itemEntidad->getCondition(),
                    'sellprice' => $itemEntidad->getSellPrice()
                ]
            );

            // Modela la parte de externalIds a entidades ExternalIdEntity, acumulándolas en un array

            $externalIdsEntityArray = [];

            foreach($validado['externalIds'] as $supplier => $value) {
                $externalIdsEntity = new ExternalIdsEntity(
                    0, $supplier, $value, $itemId
                );

                $externalIdsEntityArray[] = $externalIdsEntity;
            }

            // Inserta en la tabla `externalids` las entidades generadas
            foreach($externalIdsEntityArray as $externalIdsEntity) {
                DB::table('externalids')->insert(

                    [
                        'supplier' => $externalIdsEntity->getSupplier(), 
                        'value' => $externalIdsEntity->getValue(), 
                        'itemid' => $externalIdsEntity->getItemid()
                    ]

                );
            }

            // Si se ha logrado insertar el ítem, se devuelve el DTO con un 201, si no un 500.
            if ($itemId) {

                // Obtiene el DTO del ítem creado
                $itemDTO = $this->getItemDTOById($itemId);

            // Envía la respuesta
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

                // Recupera la entidad original de la BD
                $itemEntidad = $this->getItemEntityByItemId($validado['id']);

                // Aplica los cambios sobre ella
                foreach($validado as $propiedad => $valorActualizado) {
                    // No se debe editar el ID, y externalIds no pertenece a esta entidad
                    if($propiedad != 'id' && $propiedad != 'externalIds') {
                        $setter = 'set' . ucwords($propiedad);
                        $itemEntidad->$setter($valorActualizado);
                    }
                }

                // Se actualiza la tabla 'items' con los datos de la entidad
                DB::table('items')
                    ->where('id', $validado['id'])
                    ->update([
                        'title' => $itemEntidad->getTitle(),
                        'artist' => $itemEntidad->getArtist(),
                        'format' => $itemEntidad->getFormat(),
                        'year' => $itemEntidad->getYear(),
                        'origyear' => $itemEntidad->getOrigYear(),
                        'label' => $itemEntidad->getLabel(),
                        'rating' => $itemEntidad->getRating(),
                        'comment' => $itemEntidad->getComment(),
                        'buyprice' => $itemEntidad->getBuyPrice(),
                        'condition' => $itemEntidad->getCondition(),
                        'sellprice' => $itemEntidad->getSellPrice()
                    ]);


                // En el caso de recibir externalIds, deben borrarse de la BD los que existan previamente
                if(array_key_exists('externalIds', $validado)) {
                    DB::table('externalids')
                        ->where('itemid', $validado['id'])
                        ->delete();

                    // A continuación se insertan los que se hayan recibido del cliente
                    foreach($validado['externalIds'] as $supplier => $value) {
                        DB::table('externalids')
                            ->insert([
                                'supplier' => $supplier,
                                'value' => $value,
                                'itemid' => $validado['id']
                            ]);
                    }
                }
                

                // Por último se crea el itemDTO con los datos actualizados y se devuelve
                $itemDTOActualizado = $this->getItemDTOById($validado['id']);

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

            $idAEliminar = $validado['id'];


            $itemDTOAEliminar = $this->getItemDTOById($idAEliminar);

            // BORRADO en la tabla `items`
            $itemBorrado = DB::table('items')
                ->delete($idAEliminar);

            // BORRADO en la tabla `externalids`, aunque la restricción FK_EXTERNALID_TITLE_ID en teoría se encarga de ello
            DB::table('externalids')
                ->where('itemid', $idAEliminar)
                ->delete();

            // Envía las respuestas correspondientes
            if ($itemBorrado) {
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


    // Obtiene un itemDTO a partir de su ID y lo devuelve, o false si no existe
    private function getItemDTOById(int $itemId): ItemDTO|false {

        $itemEntity = $this->getItemEntityByItemId($itemId);

        // Si existe la entidad Item, busca las entidades externalIds
        if ($itemEntity) {


            $externalIdsEntities = $this->getExternalIdsByItemId($itemId);

            $externalIdsArray = [];

            foreach($externalIdsEntities as $externalIdsEntity) {
                $externalIdsArray[$externalIdsEntity->getSupplier()] = $externalIdsEntity->getValue();
            }

            $itemDTO = new ItemDTO(
                $itemEntity->getTitle(),
                $itemEntity->getArtist(),
                $itemEntity->getFormat(),
                $itemEntity->getYear(),
                $itemEntity->getOrigYear(),
                $itemEntity->getLabel(),
                $itemEntity->getRating(),
                $itemEntity->getComment(),
                $itemEntity->getBuyprice(),
                $itemEntity->getCondition(),
                $itemEntity->getSellPrice(),
                $externalIdsArray
            );

            return $itemDTO;

        // Si no hay resultados, devuelve false
        } else return false;
    }

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

    // Obtiene la entidad del ítem a partir de su ID o false si no existe
    private function getItemEntityByItemId(int $itemId): ItemEntity|false {

        // Obtiene la fila correspondiente al ítem buscado por ID
        $itemFila = DB::table('items')
            ->where('id', $itemId)
            ->first();

        // Si existe genera las entidades correspondientes y las mapea al DTO
        if ($itemFila) {
            // Modela la fila a una entidad Item
            $itemEntity = new ItemEntity(
                $itemFila->id, $itemFila->title, $itemFila->artist,
                $itemFila->format, $itemFila->year, $itemFila->origyear,
                $itemFila->label, $itemFila->rating, $itemFila->comment,
                $itemFila->buyprice, $itemFila->condition, $itemFila->sellprice
            );

            return $itemEntity;
        } else return false;
    }


    // Obtiene los externalIds de un ítem mediante su ID y los devuelve en un array (puede estar vacío)
    private function getExternalIdsByItemId(int $itemId): array {

        // Obtiene una colección de filas con los externalIds correspondientes al ID del ítem
        $externalIdsCollection = DB::table('externalids')
            ->where('itemid', $itemId)
            ->get();

        $externalIdsArray = [];

        // Se modelan a entidades y se guardan en un array los datos que interesan para el DTO
        foreach($externalIdsCollection as $externalIdsFila) {
            $externalIdsEntity = new ExternalIdsEntity(
                $externalIdsFila->id, $externalIdsFila->supplier,
                $externalIdsFila->value, $externalIdsFila->itemid
            );

            $externalIdsArray[] = $externalIdsEntity;
        }

        return $externalIdsArray;
    }

}
