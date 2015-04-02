<?php
return array(
    'username' => array(
        'isRequire' => 0, /* 必须要的字段 */
        'format' => 0,
    ),
    'email' => array(
        'isRequire' => 1,
        'format' => 0,
    ),
    'mobile' => array(
        'isRequire' => 0,
        'format' => 0
    ),
    'gender' => array(
        'isRequire' => 0,
        'format' => 0,
    ),
    'province' => array(
        'isRequire' => 0,
        'format' => 0,
    ),
    'city' => array(
        'isRequire' => 0,
        'format' => 0,
    ),
    'brithday' => array(
        'isRequire' => 0,
        'format' => 'date',
        'formatString' => "yyyy-MM-dd HH:mm:ss"
    ),
    'register_date' => array(
        'isRequire' => 0,
        'format' => 'date', /* 需要一date方式格式化 */
        'formatString' => "yyyy-MM-dd HH:mm:ss"
    ),
);
