<?php

return [
    ['feature' => 'sms_notif',       'label' => 'SMS Notifications',      'route' => 'admin.comms.sms.index',       'pattern' => 'admin.comms.sms.*'],
    ['feature' => 'whatsapp_notif',  'label' => 'WhatsApp Notifications', 'route' => 'admin.comms.whatsapp.index',  'pattern' => 'admin.comms.whatsapp.*'],
    ['feature' => 'email_notif',     'label' => 'Email Notifications',    'route' => 'admin.comms.email.index',     'pattern' => 'admin.comms.email.*'],
    ['group' => 'branding', 'feature' => 'custom_branding', 'label' => 'Custom Branding',        'route' => 'admin.branding.edit',         'pattern' => 'admin.branding.*'],
    ['group' => 'branding', 'feature' => 'qr_logo',         'label' => 'QR Logo',                'route' => 'admin.qr-logo.edit',          'pattern' => 'admin.qr-logo.*'],
    ['group' => 'branding', 'feature' => 'printer',         'label' => 'Printer',                'route' => 'admin.printer.index',         'pattern' => 'admin.printer.*'],
    ['group' => 'branding', 'feature' => 'barcode',         'label' => 'Barcodes',               'route' => 'admin.barcodes.index',        'pattern' => 'admin.barcodes.*'],
];
