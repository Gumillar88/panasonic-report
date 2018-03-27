<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, Mandrill, and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => '',
        'secret' => '',
    ],

    'mandrill' => [
        'secret' => '',
    ],

    'ses' => [
        'key'    => env('AWS_S3_KEY'),
        'secret' => env('AWS_S3_SECRET'),
        'region' => 'us-west-2',
    ],

    'stripe' => [
        'model'  => App\User::class,
        'key'    => '',
        'secret' => '',
    ],
    
    'loggly' => array(
		'key'	=> env('LOGGLY_KEY'),
		'tag'	=> 'panasonic_promoter_' .env('APP_ENV'),
	),

];
