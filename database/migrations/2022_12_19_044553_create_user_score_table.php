<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    private $scoreTypes = [
        //username field
        'name' => ['delimiter' => '_', 'score' => 1],
        'mail' => ['delimiter' => '.', 'score' => 2],
        //full name field
        'value' => ['delimiter' => ' ', 'score' => 4],
    ];

    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('user_score')) {
            Schema::create('user_score', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id')->unsigned();
                $table->foreign('user_id')->references('uid')->on('demo_users');
                $table->string('value');
                $table->tinyInteger('score');
                $table->index('value');
            });
        }

        \DB::transaction(function () {
            $rows = \DB::table('demo_users')->leftJoin('demo_profile_values', function ($join) {
                $join->on('demo_profile_values.uid', '=', 'demo_users.uid')
                    ->where('fid',3);
            })->get();

            foreach($rows as $row) {                
                foreach($this->scoreTypes as $key => $type) {
                    $value = $key === 'mail' ? strtok($row->{$key}, '@') : $row->{$key};
                    foreach (explode($type['delimiter'], $value) as $splitVal) {                
                        if (!$splitVal) {
                            continue;
                        }

                        \DB::table('user_score')->insert([
                            'user_id' => $row->uid,
                            'value' => $splitVal,
                            'score' => $type['score']
                        ]);
                    }
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_score');
    }
};
