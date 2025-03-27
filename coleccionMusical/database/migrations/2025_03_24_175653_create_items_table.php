<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->id();
            $table->string('title', 100); # VARCHAR de longitud 100
            $table->string('artist', 45); # VARCHAR de longitud 45
            $table->string('format', 15); # VARCHAR de longitud 15
            $table->year('year', 4); # YEAR de longitud 4
            $table->year('origyear', 4); # YEAR de longitud 4
            $table->string('label', 45); # VARCHAR de longitud 45
            $table->tinyInteger('rating'); # TinyInt de longitud 1
            $table->tinyText('comment'); # TinyText
            $table->decimal('buyprice', 6, 2); # Decimal de longitud 6 y 2 decimales
            $table->string('condition', 2); # VARCHAR de longitud 2
            $table->decimal('sellprice', 6, 2)->default(0.00); # Decimal de longitud 6 y 2 decimales con valor 0.00 por defecto

            $table->timestamps();
        });

        // INSERCIONES DE DATOS INICIALES

        DB::table('items')->insert([
            ['title' => 'Tommy', 'artist' => 'The Who', 'format' => 'CD', 'year' => '2005', 'origyear' => '1969', 'label' => 'Polydor', 'rating' => '10', 'comment' => 'Edición deluxe. De mis discos favoritos', 'buyprice' => '20.00', 'condition' => 'NM', 'sellprice' => '25.00'],
            ['title' => 'Leave Home', 'artist' => 'Ramones', 'format' => 'CD', 'year' => '2017', 'origyear' => '1977', 'label' => 'Rhino Records', 'rating' => '8', 'comment' => 'Buena reedición', 'buyprice' => '22.00', 'condition' => 'M', 'sellprice' => '20.00'],
            ['title' => 'Killing Joke', 'artist' => 'Killing Joke', 'format' => 'CD', 'year' => '2005', 'origyear' => '1980', 'label' => 'Virgin', 'rating' => '8', 'comment' => 'Reedición de este disco histórico', 'buyprice' => '17.00', 'condition' => 'VG', 'sellprice' => '20.00'],
            ['title' => 'Heaven or Las Vegas', 'artist' => 'Cocteau Twins', 'format' => 'CD', 'year' => '1999', 'origyear' => '1990', 'label' => '4AD', 'rating' => '10', 'comment' => 'Edición japonesa muy buscada, disco excelente', 'buyprice' => '30.00', 'condition' => 'NM', 'sellprice' => '35.00'],
            ['title' => 'Quadrophenia', 'artist' => 'The Who', 'format' => 'CD', 'year' => '1996', 'origyear' => '1973', 'label' => 'Polydor', 'rating' => '10', 'comment' => 'Edición inglesa, excelente estado', 'buyprice' => '20.00', 'condition' => 'E', 'sellprice' => '25.00'],
            ['title' => 'Das Hohelied Salomos', 'artist' => 'Popol Vuh', 'format' => 'CD', 'year' => '1992', 'origyear' => '1975', 'label' => 'Spalax', 'rating' => '9', 'comment' => 'Obra maestra de krautrock, edición francesa', 'buyprice' => '25.00', 'condition' => 'E', 'sellprice' => '30.00'],
            ['title' => 'Hosianna Mantra', 'artist' => 'Popol Vuh', 'format' => 'CD', 'year' => '2019', 'origyear' => '1972', 'label' => 'BMG', 'rating' => '10', 'comment' => 'Un clásico bien remasterizado', 'buyprice' => '22.00', 'condition' => 'NM', 'sellprice' => '30.00'],
            ['title' => 'Short Stories in Impossible Spaces', 'artist' => 'Aliceffekt', 'format' => 'Digital', 'year' => '2014', 'origyear' => '2014', 'label' => 'self-release', 'rating' => '9', 'comment' => 'Maravillosa banda sonora', 'buyprice' => '5.00', 'condition' => 'M', 'sellprice' => '25.00'],
            ['title' => 'Who’s Next', 'artist' => 'The Who', 'format' => 'CD', 'year' => '1998', 'origyear' => '1971', 'label' => 'MCA', 'rating' => '10', 'comment' => 'Edición canadiense, obra maestra', 'buyprice' => '20.00', 'condition' => 'NM', 'sellprice' => '27.00'],
            ['title' => 'Bizarre Love Triangle', 'artist' => 'New Order', 'format' => 'Maxi', 'year' => '1986', 'origyear' => '1986', 'label' => 'Factory', 'rating' => '8', 'comment' => 'Edición original que incluye el famoso remix de Shep Pettibone', 'buyprice' => '10.00', 'condition' => 'VG', 'sellprice' => '15.00'],
            ['title' => 'Ella tiene el cabello rubio', 'artist' => 'Albert Band', 'format' => 'Single', 'year' => '1970', 'origyear' => '1970', 'label' => 'Belter', 'rating' => '10', 'comment' => 'Edición original, joya buscadísima', 'buyprice' => '40.00', 'condition' => 'NM', 'sellprice' => '70.00'],
            ['title' => 'Rocket to Russia', 'artist' => 'Ramones', 'format' => 'Digital', 'year' => '2005', 'origyear' => '1977', 'label' => 'Rhino Records', 'rating' => '10', 'comment' => 'Edicion deluxe, 77 pistas!!', 'buyprice' => '20.00', 'condition' => 'M', 'sellprice' => '20.00'],
            ['title' => 'Ramones Mania', 'artist' => 'Ramones', 'format' => '2LP', 'year' => '1988', 'origyear' => '1988', 'label' => 'Sire', 'rating' => '9', 'comment' => 'Otra joya recopilatoria de los Ramones', 'buyprice' => '40.00', 'condition' => 'NM', 'sellprice' => '40.00']
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
