    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create('hr_vacations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade'); 
                $table->foreignId('vacation_type_id')->constrained('hr_vacation_types')->onDelete('cascade'); 
                $table->dateTime('from'); 
                $table->dateTime('to'); 
                $table->float('days_taken')->default(0); 
                $table->enum('take_with', ['days', 'hours'])->default('days'); 
                $table->enum('request_type', ['full_day', 'half_day'])->nullable();
                $table->date('date')->nullable(); 
                $table->time('from_time')->nullable(); 
                $table->time('to_time')->nullable(); 
                $table->text('reason')->nullable();
                $table->enum('approval_status', [
                    'new', 
                    'approved_by_manager', 
                    'approved_by_hr', 
                    'approved', 
                    'refused'
                ])->default('new');
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
            Schema::dropIfExists('hr_vacations_model');
        }
    };
