<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            // $table->string('status')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->decimal('account_balance', 13, 2)->nullable();
            $table->bigInteger('sales')->nullable();
            // $table->string('api_token')->nullable();

            $table->boolean('active')->default(false);
            $table->string('activation_token')->nullable();
            
            $table->rememberToken();
            $table->timestamps();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admins');
    }
}
