<?php

return [
    'pagination' => [

        /**
         * Default per page value, if null then Model's default settings will be taken.
         * Can be overridden by passing "per_page" query parameter.
         *
         * Example: ?per_page=30
         */
        'per_page' => 20,

    ],

    /*
     * Request filters that will be applied by default, allowing to use them in each query
     */
    'filtering' => [

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
                'queryParameter' => 'filter',
                'uses' => \Noitran\Repositories\Criteria\OrderBy::class
            ],

            /*
             * Default criteria for limiting results
             */
            [
                'queryParameter' => 'limit',
                'uses' => \Noitran\Repositories\Criteria\LimitBy::class
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
             * Default page in requests in not specified
             */
            'page' => 1,

        ],
    ],
];
