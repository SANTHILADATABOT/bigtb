<?php

return [
    'name'        => 'Advanced WP PM',
    'slug'        => 'pm',
    'version'     => '1.2.0',
    'api'     	  => '2',
    'db_version'  => '2.5',
    'text_domain' => 'pm',
    'comment_per_page' => 200,
    'allowed_html' => [
        'a'      => [ 'href' => [], 'title' => [] ],
        'br'     => [],
        'em'     => [],
        'strong' => [],
        'span'   => [
            'style'           => [],
            'class'           => [],
            'id'              => [],
            'data-pm-user-id' => [],
            'data-pm-user'    => [],
            'name'            => [],
            'title'           => []
        ],
        'b'      => [],
        'em'     => [],
        'p'      => [],
        'code'   => [],
        'pre'    => [],
    ]
];
