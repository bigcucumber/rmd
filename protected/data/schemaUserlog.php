<?php
return array(
    'email' => array(
        'isRequire' => 1, /* 必须要的字段 */
        'format' => 0,
    ),
    'item_id' => array(
        'isRequire' => 0,
        'format' => 0,
    ),
    'type' => array(
        'isRequire' => 0,
        'format' => 0
    ),
    'brand_di' => array(
        'isRequire' => 0,
        'format' => 0,
    ),
    'price_level' => array(
        'isRequire' => 0,
        'format' => 0,
    ),
    'tag_id' => array(
        'isRequire' => 0,
        'format' => 0,
    ),
    'action' => array(
        'isRequire' => 0,
        'format' => 0,
    ),
    'timestamp' => array(
        'isRequire' => 0,
        'format' => 'date', /* 需要一date方式格式化 */
        'formatString' => "yyyy/M/d  HH:mm:ss"
    ),
);
