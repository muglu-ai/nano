<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('poster_drafts', function (Blueprint $table) {

            $table->id();
            $table->uuid('token')->unique(); // used to access preview step securely

            //Core fields
            $table->String('sector', 150);                  
            $table->enum('nationality', ['India', 'International']);    
            $table->String('title', 200);

            //lead Auther detail section start here
            // Lead Author
            $table->string('lead_name', 200);
            $table->string('lead_email', 200);
            $table->string('lead_org', 250);

            // lead_phone stored split in old PHP as "code-number"
            $table->string('lead_ccode', 5)->nullable(); // Hidden lead Country Code
            $table->string('lead_phone', 15);               // lead_phoneNumber


            $table->text('lead_addr');
            $table->string('lead_city', 120);
            $table->string('lead_state', 120);
            $table->string('lead_country', 120);
            $table->string('lead_zip', 30);

            //lead Auther detail section end here

            //$table->boolean('lead_presenter_same')->default(false);  // Is lead author and presenter same if true then copy lead author details to presenter details on UI

            // Poster Presenter
            $table->string('pp_name', 200);
            $table->string('pp_email', 200);
            $table->string('pp_org', 250);
            $table->string('pp_website', 255)->nullable();   

            // pp_phone stored split in old PHP as "code-number"
            $table->string('pp_ccode', 5)->nullable();  //hidden pp phone country code
            $table->string('pp_phone', 15);         // pp_phoneNumber


            $table->text('pp_addr');
            $table->string('pp_city', 120);
            $table->string('pp_state', 120);
            $table->string('pp_country', 120);
            $table->string('pp_zip', 30);

            //poster Presenter detail section end here

            // Co-Authors
            $table->string('co_auth_name_1', 200)->nullable();
            $table->string('co_auth_name_2', 200)->nullable();
            $table->string('co_auth_name_3', 200)->nullable();
            $table->string('co_auth_name_4', 200)->nullable();

            //$table->boolean('co_auth_same')->default(false);    // Is co-author same as presenter if true then copy presenter details to co-author details on UI

            // Accompanying Co-Authors
            $table->string('acc_co_auth_name_1', 200)->nullable();
            $table->string('acc_co_auth_name_2', 200)->nullable();
            $table->string('acc_co_auth_name_3', 200)->nullable();
            $table->string('acc_co_auth_name_4', 200)->nullable();

            // Theme (your UI currently forces one value)
            $table->string('theme', 150)->nullable();

            // Abstract text (250 words limit on UI, but store as text)
            $table->text('abstract_text');

            // Files (store paths + optional metadata)
            $table->string('sess_abstract_path', 500)->nullable(); // name="sess_abstract"
            $table->string('sess_abstract_original_name', 255)->nullable();
            $table->unsignedBigInteger('sess_abstract_size')->nullable();
            $table->string('sess_abstract_mime', 100)->nullable();

            $table->string('lead_auth_cv_path', 500)->nullable(); // name="lead_auth_cv"
            $table->string('lead_auth_cv_original_name', 255)->nullable();
            $table->unsignedBigInteger('lead_auth_cv_size')->nullable();
            $table->string('lead_auth_cv_mime', 100)->nullable();

            // Payment
            $table->string('paymode', 50)->nullable(); // Credit Card / Paypal / Bank Transfer etc.
            $table->enum('currency', ['INR', 'USD'])->nullable();  // hidden field value has be set on UI based on nationality
            $table->decimal('base_amount', 15, 2)->default(0);          // hidden field value has be set on UI based on nationality
            $table->string('discount_code', 25)->nullable();   // hidden field value has be set on UI based on nationality
            $table->decimal('discount_amount', 15, 2)->default(0);          // hidden field value has be set on UI based on nationality
            $table->decimal('gst_amount', 15, 2)->default(0);       // hidden field value has be set on UI based on nationality
            $table->decimal('processing_fee', 15, 2)->default(0); // hidden field value has be set on UI based on nationality
            $table->decimal('total_amount', 15, 2)->default(0);         // hidden field value has be set on UI based on nationality

            // Accompanying charges tracking
            $table->unsignedTinyInteger('acc_count')->default(0);          // number of accompanying co-authors
            $table->decimal('acc_unit_cost', 10, 2)->default(0);           // cost per accompanying (for now = base_amount)
            $table->decimal('additional_charge', 10, 2)->default(0);       // acc_unit_cost * acc_count

            

            // Operational / audit
            // $table->string('ip', 45)->nullable();
            // $table->string('user_agent', 500)->nullable();

            // Optional: status tracking (useful later)
            $table->enum('status', ['draft', 'submitted', 'paid', 'rejected', 'approved'])->default('draft');

            $table->timestamps();

            $table->index(['nationality', 'status', 'created_at']);
            $table->index(['lead_email']);
            $table->index(['pp_email']);
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('poster_drafts');
    }
};
