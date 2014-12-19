<?php
return [
    "route" => [
        'default' => array(
            'template' => '^\/(\w+)(\/?)(\w*)(\/?)(\w*)$',
            //{some_digit} means there will be filled  data from regexp. {0} contains string(or substring) that matched to this template
            'controller' => '{1}', //dynamic
            'action' => '{3}',//dynamic
            'params' => array(
                'another_value' => "{5}"//dynamic again
            ),
        ),
        'main' => array(
            'template' => '^$',
            'controller' => 'home', //static
            'action' => 'index',//static
            'params' => array()
        )
    ]
];