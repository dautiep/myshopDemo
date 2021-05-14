<?php

return [
    [
        'name' => 'Mains',
        'flag' => 'main.index',
    ],
    [
        'name'        => 'Create',
        'flag'        => 'main.create',
        'parent_flag' => 'main.index',
    ],
    [
        'name'        => 'Edit',
        'flag'        => 'main.edit',
        'parent_flag' => 'main.index',
    ],
    [
        'name'        => 'Delete',
        'flag'        => 'main.destroy',
        'parent_flag' => 'main.index',
    ],
];
