<?php
header('Content-Type: application/json; charset=utf-8');

$response = [
    'success' => true,
    'results' => [
        [
            'id' => 5,
            'tipo' => 'departamento',
            'descripcion' => 'Departamento renovado con vista al mar.',
            'precio_clp' => 95000000,
            'provincia' => 'Elqui',
            'comuna' => 'Coquimbo',
            'sector' => 'Peñuelas',
            'foto' => 'prop_5_6a147b6abb7bb.jpg'
        ]
    ]
];

echo json_encode($response);
