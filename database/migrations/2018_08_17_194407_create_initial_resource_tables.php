<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInitialResourceTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('recipe', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('space_id')->nullable()->index();
            $table->string('name');
            $table->string('image_url');
            $table->text('recipe_instructions');

            $table->text('description')->nullable();  // aka summary
            $table->text('recipe_ingredient')->nullable();
            $table->string('keywords')->nullable(); // winter apple pie, nutmeg crust
            $table->string('recipe_cuisine')->nullable(); // Asian, Italian, ...
            $table->string('recipe_category')->nullable(); // dinner, entry, dessert, ...

            $table->string('recipe_diet')->nullable(); // paleo, keto, etc..

            $table->string('recipe_yield')->nullable(); // serving, bites, etc...

            $table->float('rating_value')->nullable();
            $table->unsignedInteger('rating_count')->nullable();
            $table->unsignedInteger('review_count')->nullable();
            $table->timestamp('rating_at')->nullable();

            // these should be in minutes
            $table->unsignedInteger('prep_time')->nullable();
            $table->unsignedInteger('cook_time')->nullable();
            $table->unsignedInteger('rest_time')->nullable();
            $table->unsignedInteger('total_time')->nullable();

            $table->string('author_name')->nullable();
            $table->string('date_published')->nullable();

            $table->unsignedInteger('skill_level')->default(2); // 1 to 5
            $table->text('recipe_tip')->nullable();
            $table->text('recipe_note')->nullable();
            $table->string('cook_method')->nullable(); // bake, grill, etc...
            $table->string('serving_size')->nullable();
            $table->text('nutrition')->nullable();

            $table->string('legacy_id')->nullable();
            $table->string('export_id')->nullable();
            $table->string('export_to')->nullable();
            $table->string('export_rating_value')->nullable();
            $table->timestamp('export_rating_at')->nullable();
            $table->string('export_category1')->nullable();
            $table->string('export_category2')->nullable();
            $table->string('export_category3')->nullable();
            $table->string('export_category4')->nullable();

            $table->string('src_recipe_id')->nullable();
            $table->string('src_image_url')->nullable();
            $table->string('src_url')->nullable();

            $table->string('submitted_by')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->string('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->string('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();

            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recipe');
    }
}
