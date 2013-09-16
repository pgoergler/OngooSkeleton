<?php

return array(
    'all' => array(
        'Ongoo' => array(),
    ),
    'dev' => array(
        'Ongoo' => array(
            'session_path' => '/tmp/'
        ),
    ),
    'prod' => array(
        'Ongoo' => array(
            'session_path' => __ROOT_DIR . '/sessions',
        ),
    ),
    'test' => array(),
);