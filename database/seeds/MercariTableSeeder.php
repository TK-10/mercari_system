<?php

use Illuminate\Database\Seeder;

class MercariTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * ※ 以下はデモ・検証用の架空データです（実在の人物・取引情報ではありません）。
     *
     * @return void
     */
    public function run()
    {
        \DB::table('mercaris')->insert([
            [
                'id' => '1',
                'mid' => 'm831597900',
                'item_name' => 'オールドスパイス ピュアスポーツ デオドラント 68g',
                'item_price' => '799',
                'buyer_name' => '島田早紀',
                'shorikubun' => '1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
             ],
            [
                'id' => '2',
                'mid' => 'm831597900',
                'item_name' => 'アルミニウムフリー OLD SPICE オールドスパイス',
                'item_price' => '799',
                'buyer_name' => '山田健太',
                'shorikubun' => '1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => '3',
                'mid' => 'm831597900',
                'item_name' => 'オールドスパイス Swagger スワガー',
                'item_price' => '799',
                'buyer_name' => '山本裕次郎',
                'shorikubun' => '1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => '4',
                'mid' => 'm831597900',
                'item_name' => 'パウダースプレー',
                'item_price' => '799',
                'buyer_name' => '山本りんこ',
                'shorikubun' => '2',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => '5',
                'mid' => 'm831597900',
                'item_name' => 'エージーデオ24 パウダースプレー ',
                'item_price' => '799',
                'buyer_name' => '佐々木ゆうき',
                'shorikubun' => '3',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ]);
    }
}
