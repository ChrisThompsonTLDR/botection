<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuthorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('authors', function (Blueprint $table) {
            $table->string('id')->nullable();
            $table->string('username');
            $table->boolean('hide_from_robots')->nullable();
            $table->bigInteger('link_karma')->nullable();
            $table->bigInteger('comment_karma')->nullable();
            $table->boolean('is_gold')->nullable();
            $table->boolean('is_mod')->nullable();
            $table->boolean('verified')->nullable();
            $table->boolean('has_verified_email')->nullable();
            $table->integer('seconds_to_respond')->nullable()->unsigned();

            $table->text('raw')->nullable();

            $table->timestamps();

            $table->unique('id');
            $table->primary('username');
        });

        Schema::create('author_history', function (Blueprint $table) {
            $table->increments('id');

            $table->string('author_id')->nullable();
            $table->string('username');
            $table->boolean('hide_from_robots')->nullable();
            $table->bigInteger('link_karma')->nullable();;
            $table->bigInteger('comment_karma')->nullable();;
            $table->boolean('is_gold')->nullable();;
            $table->boolean('is_mod')->nullable();;
            $table->boolean('verified')->nullable();;
            $table->boolean('has_verified_email')->nullable();
            $table->integer('seconds_to_respond')->nullable()->unsigned();

            $table->text('raw')->nullable();

            $table->timestamps();

            $table->index('author_id');
            $table->index('username');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('authors');
        Schema::dropIfExists('author_history');
    }
}
