<?php

return [
    ['feature' => 'sms_notif',       'label' => 'SMS Notifications',      'route' => 'admin.comms.sms.index',       'pattern' => 'admin.comms.sms.*'],
    ['feature' => 'whatsapp_notif',  'label' => 'WhatsApp Notifications', 'route' => 'admin.comms.whatsapp.index',  'pattern' => 'admin.comms.whatsapp.*'],
    ['feature' => 'email_notif',     'label' => 'Email Notifications',    'route' => 'admin.comms.email.index',     'pattern' => 'admin.comms.email.*'],
    ['feature' => 'custom_branding', 'label' => 'Custom Branding',        'route' => 'admin.branding.edit',         'pattern' => 'admin.branding.*'],
    ['feature' => 'qr_logo',         'label' => 'QR Logo',                'route' => 'admin.qr-logo.edit',          'pattern' => 'admin.qr-logo.*'],
    ['feature' => 'printer',         'label' => 'Printer',                'route' => 'admin.printer.index',         'pattern' => 'admin.printer.*'],
    ['feature' => 'barcode',         'label' => 'Barcodes',               'route' => 'admin.barcodes.index',        'pattern' => 'admin.barcodes.*'],
];
