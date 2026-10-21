<?php

return [
    ['feature' => 'reports', 'label' => 'Reports', 'route' => 'admin.reports.index', 'pattern' => 'admin.reports.*'],
    ['feature' => 'billing', 'label' => 'Invoices', 'route' => 'admin.invoices.index', 'pattern' => 'admin.invoices.*'],
    ['feature' => 'tax_mgmt', 'label' => 'Tax Rates', 'route' => 'admin.taxes.index', 'pattern' => 'admin.taxes.*'],
    ['feature' => 'supplier_mgmt', 'label' => 'Suppliers', 'route' => 'admin.suppliers.index', 'pattern' => 'admin.suppliers.*'],
    ['feature' => 'purchase_mgmt', 'label' => 'Purchases', 'route' => 'admin.purchases.index', 'pattern' => 'admin.purchases.*'],
    ['feature' => 'expense_tracking', 'label' => 'Expenses', 'route' => 'admin.expenses.index', 'pattern' => 'admin.expenses.*'],
    ['feature' => 'accounting', 'label' => 'Accounting', 'route' => 'admin.journals.index', 'pattern' => 'admin.journals.*'],
    ['feature' => 'accounting', 'label' => 'Chart of Accounts', 'route' => 'admin.accounts.index', 'pattern' => 'admin.accounts.*'],
    ['feature' => 'accounting', 'label' => 'Profit & Loss', 'route' => 'admin.accounting.profit-loss', 'pattern' => 'admin.accounting.*'],
];
