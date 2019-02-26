<?php

return [

    /*
     * Request filters that will be applied by default, allowing to use them in each query
     */
    'filtering' => [

        /*
         * List allowed logical operators for data filtering and comparision
         */
        'allowed_expressions' => [
            '$eq', // equal or =
            '$notEq', // not equal or !=
            '$lt', // less than
            '$lte', // less than or equal
            '$gt', // greater than
            '$gte', // greater than or equal
            '$like',
            '$in',
            '$notIn',
            '$or',
            '$between',
        ],

        /*
         * What comparison operator should be used by default
         */
        'default_expression' => '$eq',

        /*
         * Available data types are treated in different ways.
         * List of allowed data types
         */
        'allowed_data_types' => [
            '$string',
            '$bool',
            '$int',
            '$date',
            '$datetime',
        ],

        /*
         * How will be search values processed by default
         */
        'default_data_type' => '$string',

        /*
         * List of default query filters
         */
        'default_filters' => [

            /*
             * Adds Criteria to support Json:Api filtering recommendation
             * https://jsonapi.org/recommendations/#filtering
             */
            [
                'queryParameter' => 'filter',
                'uses' => \Noitran\Repositories\Criteria\FilterBy::class,
            ],

            /*
             * Default criteria for ordering columns
             */
            [
                'queryParameter' => 'order_by',
                'uses' => \Noitran\Repositories\Criteria\OrderBy::class,
            ],

            /*
             * Default criteria for limiting results
             */
            [
                'queryParameter' => 'limit',
                'uses' => \Noitran\Repositories\Criteria\LimitBy::class,
            ],
        ],

        /*
         * Query parameters applied by default to each request
         */
        'default_settings' => [

            /*
             * Use pagination in requests by default
             */
            'paginate' => false,

            /*
             * What column should be used for ordering by default
             */
            'order_by' => 'created_at,desc',

            /*
             * Default page in requests if not specified
             */
            'page' => 1,

            /*
             * Default per page value, if null then Model's default settings will be taken.
             * Can be overridden by passing "per_page" query parameter.
             *
             * Example: ?per_page=30
             */
            'per_page' => 20,
        ],
    ],
];
