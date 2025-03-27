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
        Schema::create('external_ids', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->id();
            $table->string('supplier', 45); # VARCHAR de tamaño 45
            $table->string('value', 100); # VARCHAR de tamaño 100
            $table->unsignedBigInteger('item_id'); # FK
            
            $table->timestamps(); // Se incluyen las columnas de timestamps, pueden ser útiles

            # Constraint de la FK
            $table->foreign('item_id')->references('id')->on('items')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        // INSERCIONES iniciales

        DB::table('external_ids')->insert([
            ['supplier' => 'Discogs', 'value' => '1798436', 'item_id' => '1'],
            ['supplier' => 'MusicBrainz', 'value' => '4147c0d3-7939-3484-bde6-b6fbefd7bc3b', 'item_id' => '1'],
            ['supplier' => 'Discogs', 'value' => '10589268', 'item_id' => '2'],
            ['supplier' => 'MusicBrainz', 'value' => 'c5923e07-a09b-469a-af1a-bc5e67e402ec', 'item_id' => '2'],
            ['supplier' => 'Discogs', 'value' => '502215', 'item_id' => '3'],
            ['supplier' => 'MusicBrainz', 'value' => '0f6924c2-5812-36d8-8a34-df69744ce5e0', 'item_id' => '3'],
            ['supplier' => 'Discogs', 'value' => '10644727', 'item_id' => '4'],
            ['supplier' => 'MusicBrainz', 'value' => '224445b7-35ea-43c2-b8df-0e918292f5ba', 'item_id' => '4'],
            ['supplier' => 'Discogs', 'value' => '1085136', 'item_id' => '5'],
            ['supplier' => 'MusicBrainz', 'value' => 'e0e2c19a-d9a9-3b59-b69b-6afc14551599', 'item_id' => '5'],
            ['supplier' => 'Discogs', 'value' => '737253', 'item_id' => '6'],
            ['supplier' => 'MusicBrainz', 'value' => '5bcde2f7-80ac-38fa-994b-ce51df4d50e3', 'item_id' => '6'],
            ['supplier' => 'Discogs', 'value' => '13812171', 'item_id' => '7'],
            ['supplier' => 'MusicBrainz', 'value' => 'e4efe882-c8b6-45c1-a429-fb7f350225f9', 'item_id' => '7'],
            ['supplier' => 'MusicBrainz', 'value' => '681ad693-9074-4216-aded-d1babfede242', 'item_id' => '8'],
            ['supplier' => 'Discogs', 'value' => '7131107', 'item_id' => '9'],
            ['supplier' => 'MusicBrainz', 'value' => '48289c07-fb4c-3c7a-b185-fd800e822325', 'item_id' => '9'],
            ['supplier' => 'Discogs', 'value' => '121190', 'item_id' => '10'],
            ['supplier' => 'MusicBrainz', 'value' => 'de53b49d-be39-4daf-afe2-d650269862d7', 'item_id' => '10'],
            ['supplier' => 'Discogs', 'value' => '3374017', 'item_id' => '11'],
            ['supplier' => 'Discogs', 'value' => '17593621', 'item_id' => '12'],
            ['supplier' => 'MusicBrainz', 'value' => 'f018af26-cf5e-4c76-93f0-c5faa80fd371', 'item_id' => '12']
        ]);


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_ids');
    }
};
