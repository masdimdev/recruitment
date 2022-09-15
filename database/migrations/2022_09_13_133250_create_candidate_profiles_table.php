<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCandidateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('candidate_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number', 16);
            $table->string('address', 200);
            $table->date('date_of_birth');
            $table->tinyInteger('sex'); // 0 = Not known; 1 = Male; 2 = Female; 9 = Not applicable. See: https://en.wikipedia.org/wiki/ISO/IEC_5218
            $table->foreignIdFor(\App\Models\User::class);
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
        Schema::dropIfExists('candidate_profiles');
    }
}
