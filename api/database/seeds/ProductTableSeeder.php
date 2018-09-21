<?php

use Illuminate\Database\Seeder;

class ProductTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('product')->insert([ 'id' => 'A101', 'description' => 'Screwdriver', 'category' => '1', 'price' => '9.75' ]);
        DB::table('product')->insert([ 'id' => 'A102', 'description' => 'Electric screwdriver', 'category' => '1', 'price' => '49.50' ]);
        DB::table('product')->insert([ 'id' => 'B101', 'description' => 'Basic on-off switch', 'category' => '2', 'price' => '4.99' ]);
        DB::table('product')->insert([ 'id' => 'B102', 'description' => 'Press button', 'category' => '2', 'price' => '4.99' ]);
        DB::table('product')->insert([ 'id' => 'B103', 'description' => 'Switch with motion detector', 'category' => '2', 'price' => '12.95' ]);
    }
}
