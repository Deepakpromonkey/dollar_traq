<?php

return [

    /*
    Backend
        Baseurl: http://127.0.0.1:3000/
        API path: api/
        Handle Prefix: handle/
        Endpoint: backend/visa/countries/list
    */

    'backend/carrier/search' => [
        'type' => 'custom',
        'model' => ['model' => 'App\Models\CarriersModel\Carrier', 'method' => 'search'],
    ],
    'backend/carrier/detail' => [
        'type' => 'custom',
        'model' => ['model' => 'App\Models\CarriersModel\Carrier', 'method' => 'detail'],
    ],

];
