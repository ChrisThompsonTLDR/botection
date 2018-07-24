<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuthorStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('author_stats', function (Blueprint $table) {
            $table->string('author');
            $table->timestamps();

            $table->string('reddit_id');
            $table->string('subreddit');
            $table->smallInteger('depth')->unsigned();
            $table->integer('score');
            $table->integer('seconds_to_respond')->nullable()->unsigned();
            $table->text('body')->nullable();

            $table->index('author');
            $table->unique('reddit_id');
            $table->index('subreddit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('author_stats');
    }
}
