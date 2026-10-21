<?php

return [
    ['feature' => 'api_access',        'label' => 'API Access',      'route' => 'admin.api-tokens.index', 'pattern' => 'admin.api-tokens.*'],
    ['feature' => 'mobile_app',        'label' => 'Mobile App',      'route' => 'admin.pwa.index',        'pattern' => 'admin.pwa.*'],
    ['feature' => 'backup',            'label' => 'Backups',         'route' => 'admin.backups.index',    'pattern' => 'admin.backups.*'],
    ['feature' => 'export_import',     'label' => 'Import / Export', 'route' => 'admin.data.index',       'pattern' => 'admin.data.*'],
    ['feature' => 'advanced_security', 'label' => 'Security',        'route' => 'admin.security.index',   'pattern' => 'admin.security.*'],
    ['feature' => 'ai_features',       'label' => 'Smart Tools',     'route' => 'admin.ai.index',         'pattern' => 'admin.ai.*'],
];
