<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->string('id');

            $table->string('parent_id')->nullable();
            $table->integer('lft')->nullable()->unsigned();
            $table->integer('rgt')->nullable()->unsigned();
            $table->bigInteger('depth')->nullable()->unsigned();

            $table->string('link_id')->nullable();
            $table->string('subreddit_id')->nullable();
            $table->string('subreddit')->nullable();

            $table->mediumInteger('score')->nullable();
            $table->mediumInteger('ups')->nullable();
            $table->mediumInteger('downs')->nullable();
            $table->decimal('upvote_ratio', 3, 2)->nullable();

            $table->string('author')->nullable();
            $table->string('name')->nullable();
            $table->string('title', 1000)->nullable();
            $table->text('body')->nullable();
            $table->text('url')->nullable();

            $table->boolean('spam', false)->nullable();
            $table->boolean('stickied', false)->nullable();
            $table->boolean('removed', false)->nullable();
            $table->boolean('approved', false)->nullable();
            $table->boolean('locked', false)->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->boolean('ignore_reports', false)->nullable();
            $table->boolean('quarantine', false)->nullable();
            $table->mediumInteger('num_comments')->nullable();
            $table->string('approved_by')->nullable();
            $table->timestamp('edited_at')->nullable();
            $table->mediumInteger('view_count')->nullable();
            $table->string('wls')->nullable();
            $table->string('pwls')->nullable();

            $table->smallInteger('num_reports')->nullable();
            $table->integer('controversiality')->nullable();

            $table->integer('seconds_to_respond')->nullable()->unsigned();
            $table->text('raw')->nullable();

            $table->timestamps();
            $table->timestamp('failed_to_find')->nullable();

            $table->primary('id');
            $table->index('subreddit_id');
            $table->index('link_id');
            $table->index('author');
            $table->index('subreddit');
        });

        Schema::create('comment_history', function (Blueprint $table) {
            $table->increments('id');

            $table->string('comment_id');

            $table->string('parent_id')->nullable();
            $table->integer('lft')->nullable()->unsigned();
            $table->integer('rgt')->nullable()->unsigned();
            $table->bigInteger('depth')->nullable()->unsigned();

            $table->string('link_id')->nullable();
            $table->string('subreddit_id')->nullable();
            $table->string('subreddit')->nullable();

            $table->mediumInteger('score')->nullable();
            $table->mediumInteger('ups')->nullable();
            $table->mediumInteger('downs')->nullable();
            $table->decimal('upvote_ratio', 3, 2)->nullable();

            $table->string('author')->nullable();
            $table->string('name')->nullable();
            $table->string('title', 1000)->nullable();
            $table->text('body')->nullable();
            $table->text('url')->nullable();

            $table->boolean('spam', false)->nullable();
            $table->boolean('stickied', false)->nullable();
            $table->boolean('removed', false)->nullable();
            $table->boolean('approved', false)->nullable();
            $table->boolean('locked', false)->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->boolean('ignore_reports', false)->nullable();
            $table->boolean('quarantine', false)->nullable();
            $table->mediumInteger('num_comments')->nullable();
            $table->string('approved_by')->nullable();
            $table->timestamp('edited_at')->nullable();
            $table->mediumInteger('view_count')->nullable();
            $table->string('wls')->nullable();
            $table->string('pwls')->nullable();

            $table->smallInteger('num_reports')->nullable();
            $table->integer('controversiality')->nullable();

            $table->integer('seconds_to_respond')->nullable()->unsigned();
            $table->text('raw')->nullable();

            $table->timestamps();
            $table->timestamp('failed_to_find')->nullable();

            $table->index('comment_id');
            $table->index('subreddit_id');
            $table->index('link_id');
            $table->index('author');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
        Schema::dropIfExists('comment_history');
    }
}
