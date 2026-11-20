<?php

use Illuminate\Database\Seeder;

class ItemTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('items')->insert([
        [
            'id' => '1',
            'item_name' => 'オールドスパイス ピュアスポーツ デオドラント 68g',
            'item_price' => '799',
            'stock' => '3',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
         ],
        [
            'id' => '2',
            'item_name' => 'JILLSTUART ムーンライトマジック50ml',
            'item_price' => '5000',
            'stock' => '1',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ],
        [
            'id' => '3',
            'item_name' => 'ブルガリ アクアプールオム100ml メンズ香水',
            'item_price' => '5500',
            'stock' => '5',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ],
    ]);
    }
}
