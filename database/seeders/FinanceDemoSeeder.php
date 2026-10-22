<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Demo data for Finance modules (amounts in PKR, "Rs"): suppliers, purchase orders + items,
 * expense categories, expenses, tax rates, invoices, chart of accounts, journal entries + lines.
 * Re-runnable (keyed upserts only).
 */
class FinanceDemoSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Restaurant::orderBy('id')->pluck('id') as $rid) {
            mt_srand($rid * 4217 + 3);
            $taxId = $this->taxRates($rid);
            $suppliers = $this->suppliers($rid);
            $this->purchaseOrders($rid, $suppliers);
            $this->expenses($rid, $suppliers);
            $this->invoices($rid);
            $this->accountsAndJournals($rid);
        }
    }

    private function up(string $table, array $keys, array $vals, bool $ts = true): int
    {
        $now = now();
        $row = DB::table($table)->where($keys)->first();
        if ($row) {
            DB::table($table)->where('id', $row->id)->update($vals + ($ts ? ['updated_at' => $now] : []));
            return $row->id;
        }
        return DB::table($table)->insertGetId(array_merge($ts ? ['created_at' => $now, 'updated_at' => $now] : [], $keys, $vals));
    }

    private function taxRates(int $rid): void
    {
        $rates = [
            ['GST 16%', 16, 'exclusive', true, true],
            ['Punjab Restaurant Tax 5%', 5, 'exclusive', true, false],
            ['Sindh Services Tax 13%', 13, 'inclusive', true, false],
            ['Service Charge 10%', 10, 'exclusive', false, false],
        ];
        foreach ($rates as [$name, $rate, $type, $active, $default]) {
            $this->up('tax_rates', ['restaurant_id' => $rid, 'name' => $name], [
                'rate' => $rate, 'type' => $type, 'is_active' => $active, 'is_default' => $default,
            ]);
        }
    }

    private function suppliers(int $rid): array
    {
        $defs = [
            ['Al-Fatah Fresh Produce', 'Muhammad Aslam', 'Vegetables & fruit', 'Net 7'],
            ['National Poultry Farms', 'Rana Tahir', 'Chicken & eggs', 'Net 15'],
            ['Karachi Meat Traders', 'Haji Yousuf', 'Beef & mutton', 'Net 7'],
            ['Haji Dairy & Cream', 'Shabbir Hussain', 'Milk, cream & cheese', 'Cash on delivery'],
            ['Shan Spice Mart', 'Irfan Khan', 'Spices & masalas', 'Net 30'],
            ['Dalda Oil Distributors', 'Naveed Akhtar', 'Cooking oil & ghee', 'Net 30'],
            ['Pak Beverages Depot', 'Salman Farooq', 'Soft drinks & juices', 'Net 15'],
            ['Metro Packaging Solutions', 'Adnan Rauf', 'Boxes, bags & cutlery', 'Net 30'],
            ['Sui Gas Cylinders Co.', 'Zahid Mehmood', 'LPG cylinders', 'Cash on delivery'],
            ['Bake Master Flour Mills', 'Tariq Jameel', 'Flour & bakery needs', 'Net 15'],
        ];
        $ids = [];
        foreach ($defs as $i => [$name, $contact, $notes, $terms]) {
            $ids[$i] = $this->up('suppliers', ['restaurant_id' => $rid, 'name' => $name], [
                'contact_person' => $contact, 'phone' => '0321' . sprintf('%07d', 4410000 + $i * 3571 + $rid),
                'email' => 'sales' . ($i + 1) . '@' . strtolower(preg_replace('/[^a-z]/i', '', explode(' ', $name)[0])) . '.pk',
                'address' => 'Shop ' . (10 + $i * 3) . ', Wholesale Market, ' . ['Lahore', 'Karachi', 'Islamabad'][$i % 3],
                'payment_terms' => $terms, 'opening_balance' => $i % 3 === 0 ? mt_rand(5, 30) * 1000 : 0,
                'is_active' => $i !== 8 || $rid !== 3, 'notes' => $notes,
                'created_at' => now()->subDays(60 - $i),
            ]);
        }
        return $ids;
    }

    private function purchaseOrders(int $rid, array $suppliers): void
    {
        $catalog = [
            0 => [['Tomatoes (kg)', 180], ['Onions (kg)', 110], ['Fresh coriander (bunch)', 40], ['Lettuce (kg)', 220]],
            1 => [['Chicken breast (kg)', 620], ['Chicken whole (kg)', 440], ['Eggs (dozen)', 360]],
            2 => [['Beef boneless (kg)', 1350], ['Mutton (kg)', 2100], ['Beef mince (kg)', 1100]],
            3 => [['Fresh milk (litre)', 190], ['Cooking cream (litre)', 650], ['Mozzarella (kg)', 1450]],
            4 => [['Red chilli powder (kg)', 900], ['Garam masala (kg)', 1600], ['Black pepper (kg)', 2200]],
            5 => [['Cooking oil (5L)', 2450], ['Desi ghee (kg)', 1750]],
            6 => [['Cola 1.5L (carton)', 1250], ['Mango juice (carton)', 1800], ['Mineral water (carton)', 700]],
            7 => [['Burger boxes (100 pcs)', 1800], ['Paper bags (100 pcs)', 950], ['Cutlery sets (100 pcs)', 1200]],
            8 => [['LPG cylinder 45kg', 14500]],
            9 => [['Flour (20kg bag)', 2600], ['Burger buns (dozen)', 380], ['Yeast (kg)', 850]],
        ];
        $statuses = ['received', 'received', 'received', 'ordered', 'received', 'cancelled'];
        for ($n = 1; $n <= 14; $n++) {
            $si = ($n * 3) % 10;
            $date = now()->subDays(59 - ($n - 1) * 4)->startOfDay();
            $status = $n > 12 ? 'ordered' : $statuses[$n % count($statuses)];
            $lines = [];
            foreach (array_slice($catalog[$si], 0, mt_rand(2, 4)) as [$desc, $cost]) {
                $qty = mt_rand(2, 25);
                $lines[] = [$desc, $qty, $cost, round($qty * $cost, 2)];
            }
            $total = array_sum(array_column($lines, 3));
            $paid = $status === 'cancelled' ? 0 : ($status === 'received' ? ($n % 3 === 0 ? round($total / 2, 2) : $total) : 0);
            $poId = $this->up('purchase_orders', ['restaurant_id' => $rid, 'po_number' => sprintf('PO-2026-%04d', $n)], [
                'supplier_id' => $suppliers[$si], 'order_date' => $date->toDateString(), 'status' => $status,
                'total_amount' => $total, 'paid_amount' => $paid,
                'received_at' => $status === 'received' ? $date->copy()->addDays(2)->setTime(11, 30) : null,
                'notes' => $status === 'cancelled' ? 'Cancelled - supplier out of stock' : 'Regular weekly stock',
                'created_at' => $date,
            ]);
            foreach ($lines as [$desc, $qty, $cost, $lt]) {
                $this->up('purchase_order_items', ['purchase_order_id' => $poId, 'description' => $desc], [
                    'quantity' => $qty, 'unit_cost' => $cost, 'line_total' => $lt,
                ]);
            }
        }
    }

    private function expenses(int $rid, array $suppliers): void
    {
        $cats = ['Rent', 'Utilities', 'Groceries & Ingredients', 'Gas & Fuel', 'Repairs & Maintenance', 'Marketing & Ads',
            'Packaging', 'Cleaning Supplies', 'Staff Meals', 'Licenses & Fees'];
        $catIds = [];
        foreach ($cats as $i => $name) {
            $catIds[$i] = $this->up('expense_categories', ['restaurant_id' => $rid, 'name' => $name], []);
        }
        // [category idx, supplier idx|null, min, max, note]
        $tpl = [
            [0, null, 180000, 180000, 'Monthly shop rent'], [1, null, 38000, 62000, 'Electricity bill (K-Electric/LESCO)'],
            [1, null, 6500, 9000, 'Internet & PTCL bill'], [2, 0, 12000, 40000, 'Fresh vegetables restock'],
            [2, 2, 25000, 80000, 'Meat purchase'], [3, 8, 14500, 29000, 'LPG cylinders'],
            [4, null, 3000, 18000, 'AC / fridge service'], [5, null, 8000, 25000, 'Facebook & Instagram ads'],
            [6, 7, 4000, 12000, 'Takeaway boxes & bags'], [7, null, 2500, 7000, 'Detergent and sanitizer'],
            [8, null, 5000, 11000, 'Staff lunch for the week'], [9, null, 3500, 15000, 'Food authority licence renewal'],
        ];
        for ($n = 1; $n <= 26; $n++) {
            [$ci, $si, $min, $max, $note] = $tpl[($n - 1) % count($tpl)];
            $amount = $min === $max ? $min : mt_rand((int) ($min / 100), (int) ($max / 100)) * 100;
            $this->up('expenses', ['restaurant_id' => $rid, 'reference' => sprintf('EXP-%d-%03d', $rid, $n)], [
                'expense_category_id' => $catIds[$ci], 'supplier_id' => $si !== null ? $suppliers[$si] : null,
                'expense_date' => now()->subDays(59 - (int) floor(($n - 1) * 59 / 25))->toDateString(),
                'amount' => $amount, 'notes' => $note,
            ]);
        }
    }

    private function invoices(int $rid): void
    {
        $orders = DB::table('orders')->where('restaurant_id', $rid)->where('payment_status', 'paid')->orderBy('id')->limit(30)->get();
        $rate = 16;
        foreach ($orders as $i => $o) {
            $sub = round((float) $o->total_amount, 2);
            $tax = round($sub * $rate / 100, 2);
            $seq = $i + 1;
            $date = Carbon::parse($o->created_at);
            // order_id is unique per restaurant, so free it from any stale invoice that took a different sequence
            DB::table('invoices')->where('restaurant_id', $rid)->where('order_id', $o->id)->where('sequence', '!=', $seq)->delete();
            $this->up('invoices', ['restaurant_id' => $rid, 'sequence' => $seq], [
                'order_id' => $o->id, 'invoice_number' => sprintf('INV-2026-%04d', $seq),
                'invoice_date' => $date->toDateString(), 'subtotal' => $sub, 'tax_rate' => $rate,
                'tax_name' => 'GST 16%', 'tax_type' => 'exclusive', 'tax_amount' => $tax, 'total' => $sub + $tax,
                'customer_name' => $o->customer_name ?? null, 'created_at' => $date,
            ]);
        }
    }

    private function accountsAndJournals(int $rid): void
    {
        $chart = [
            ['1000', 'Cash in Hand', 'asset'], ['1010', 'Bank - Meezan Current Account', 'asset'],
            ['1100', 'Accounts Receivable', 'asset'], ['1200', 'Inventory - Food & Beverage', 'asset'],
            ['2000', 'Accounts Payable - Suppliers', 'liability'], ['2100', 'Sales Tax Payable', 'liability'],
            ['2200', 'Salaries Payable', 'liability'],
            ['4000', 'Food Sales', 'income'], ['4010', 'Beverage Sales', 'income'], ['4100', 'Delivery Charges Income', 'income'],
            ['5000', 'Cost of Goods Sold', 'expense'], ['5100', 'Salaries & Wages', 'expense'], ['5200', 'Shop Rent', 'expense'],
            ['5300', 'Utilities', 'expense'], ['5400', 'Marketing & Advertising', 'expense'],
            ['5500', 'Repairs & Maintenance', 'expense'], ['5600', 'Packaging Supplies', 'expense'],
        ];
        $acc = [];
        foreach ($chart as [$code, $name, $type]) {
            $acc[$code] = $this->up('accounts', ['restaurant_id' => $rid, 'code' => $code], ['name' => $name, 'type' => $type]);
        }

        for ($n = 1; $n <= 24; $n++) {
            $date = now()->subDays(59 - (int) floor(($n - 1) * 59 / 23))->startOfDay();
            $kind = $n % 5;
            $lines = [];
            switch ($kind) {
                case 1: // daily sales
                    $sub = mt_rand(450, 1400) * 100;
                    $tax = round($sub * 0.16, 2);
                    $memo = 'Day sales summary';
                    $lines = [['1000', $sub + $tax, 0], ['4000', 0, round($sub * 0.8, 2)], ['4010', 0, $sub - round($sub * 0.8, 2)], ['2100', 0, $tax]];
                    break;
                case 2: // credit purchase
                    $amt = mt_rand(150, 900) * 100;
                    $memo = 'Stock purchased on credit';
                    $lines = [['1200', $amt, 0], ['2000', 0, $amt]];
                    break;
                case 3: // operating expense
                    $pick = [['5200', 'Shop rent paid', 180000], ['5300', 'Utility bills paid', 52000], ['5400', 'Social media ads', 15000], ['5500', 'Equipment repair', 9500], ['5600', 'Packaging purchase', 7800]][$n % 5];
                    $memo = $pick[1];
                    $lines = [[$pick[0], $pick[2], 0], ['1000', 0, $pick[2]]];
                    break;
                case 4: // payroll
                    $amt = mt_rand(6, 9) * 100000;
                    $memo = 'Staff salaries paid';
                    $lines = [['5100', $amt, 0], ['1010', 0, $amt]];
                    break;
                default: // supplier payment
                    $amt = mt_rand(100, 600) * 100;
                    $memo = 'Supplier payment by bank transfer';
                    $lines = [['2000', $amt, 0], ['1010', 0, $amt]];
            }
            $entryId = $this->up('journal_entries', ['restaurant_id' => $rid, 'memo' => sprintf('JV-%d-%03d: %s', $rid, $n, $memo)], [
                'entry_date' => $date->toDateString(), 'created_at' => $date->copy()->setTime(18, 0),
            ]);
            DB::table('journal_lines')->where('journal_entry_id', $entryId)->delete();
            foreach ($lines as [$code, $dr, $cr]) {
                DB::table('journal_lines')->insert([
                    'journal_entry_id' => $entryId, 'account_id' => $acc[$code], 'debit' => $dr, 'credit' => $cr,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        }
    }
}
