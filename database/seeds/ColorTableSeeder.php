<?php

use Illuminate\Database\Seeder;

class ColorTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
          $faker = Faker\Factory::create();
          Color::truncate();
          for($i=0;$i<30;i++){
            $color=Color::create(array(
              //'color_id' => rand(0,100),
              'color_name' => $faker->realText(rand(5,10)),
              'color_code' => $faker->realText(rand(5,8)),
              //'created_date'=>$faker=>dateTime($max = 'now', $timezone = null),
              //'created_by'=>rand(0,10),
              //'updated_date'=>$faker=>dateTime($max = 'now', $timezone = null),
              //'updated_by '=>rand(0,10),
              'status'=>rand(0,1),




            ));


          }
    }
}
