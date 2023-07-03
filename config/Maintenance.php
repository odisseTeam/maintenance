<?php

return[
    /**
     * If you want to prevent adding an attribute more than ONE to a product
     * set this item TRUE
     * For example if you have an attribute names "COLOR", by set this config to true
     * you only can one color attribute for product.
     * By set this item to false you can have multiple color attribute ofr the product.
     */
    'simple_attribue_product' => true,
    'maintenance_file_path' => '../../systemfiles/maintenance_files/',

    'contractor_file_path' => '../../systemfiles/contractor_files/',

    'businesses_name'=>[
        [
            "id_saas_client_business" => 1,
            'business_name'=>'SDR Living' ,
            'maintenance_api_url' => env('LIVING_MAINTENANCE_API', 'https://living.odisse.local'),
        ],

        [
            "id_saas_client_business"=>2,
            'business_name'=>'ASC',
            'maintenance_api_url'=> env('ASC_MAINTENANCE_API', 'https://asc.odisse.local'),
        ],

        [
            "id_saas_client_business"=>3,
            'business_name'=>'ELC',
            'maintenance_api_url'=> env('ELC_MAINTENANCE_API', 'https://elc.odisse.local'),

        ],
        // 'Living',
        // 'ASC',
        // 'ELC',

     ],



];
