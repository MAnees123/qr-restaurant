<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <title>Thermal Invoice - {{ $order->order_number }}</title>
    <style>
      body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background: #f0f0f0;
      }
      .bill {
        padding: 10px;
        background: white;
        margin: 20px auto;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
      }
      h2,
      h3 {
        text-align: center;
        margin: 2px 0;
      }
      .small {
        font-size: 8px;
        text-align: left;
      }
      .small1 {
        font-size: 10px;
        text-align: center;
      }
      .small2 {
        font-size: 10px;
        text-align: left;
        line-height: 1.6;
      }
      table {
        width: 100%;
        border-collapse: collapse;
        font-size: 11px;
      }
      th,
      td {
        padding: 4px 0;
      }
      .right {
        text-align: right;
      }
      .center {
        text-align: center;
      }
      hr {
        border: none;
        border-top: 1px dashed black;
        margin: 10px 0;
      }
      .btn {
        margin-top: 8px;
        width: 100%;
        padding: 10px;
        background: black;
        color: white;
        border: none;
        cursor: pointer;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
      }
      .flex {
        display: flex;
        justify-content: space-between;
        font-size: 11px;
      }
      .flex1 {
        display: flex;
        font-size: 11px;
      }
      
      .no-print-area {
        max-width: 300px;
        margin: 20px auto;
        display: flex;
        flex-direction: column;
        gap: 10px;
      }

      @media print {
        body {
            background: white;
        }
        .btn, .no-print-area {
          display: none;
        }
        .bill {
          width: 80mm !important;
          margin: 0;
          box-shadow: none;
        }
        @page {
          size: auto;
          margin: 0;
        }
      }
    </style>
  </head>
  <body>
    <div class="no-print-area">
        <button class="btn" onclick="window.print()">Print Receipt</button>
        <button class="btn" onclick="window.close()" style="background: #444;">Close Window</button>
    </div>

    <div class="bill" id="invoice" style="width: 300px">
      <p class="small1">Purchase Slip</p>
      <h3>{{ strtoupper($order->restaurant->name) }}</h3>
      <p class="small1">{{ strtoupper($order->restaurant->address) }}</p>
      <p class="small1">PH# {{ $order->restaurant->phone }}</p>

      <hr />

      <div class="flex">
        <span>Date: {{ $order->created_at->format('d-m-Y') }}</span>
        <span>Time: {{ $order->created_at->format('H:i') }}</span>
      </div>
      <div class="flex">
          <span>Table: {{ $order->table ? $order->table->table_number : 'N/A' }}</span>
          <span>Mop: {{ strtoupper($order->payment_status === 'paid' ? ($order->payments->last()->method ?? 'Cash') : 'Pending') }}</span>
      </div>

      <p style="text-align: left; font-size: 11px;">Receipt #: {{ $order->order_number }}</p>

      <hr />

      <table>
        <thead>
          <tr>
            <th style="text-align: left;">Sr.</th>
            <th style="text-align: left;">Product</th>
            <th class="right">Price</th>
            <th class="center">Qty</th>
            <th class="right">Total</th>
          </tr>
          <tr>
            <td colspan="5">
              <hr style="border-top: 1px dashed black; margin: 2px 0;">
            </td>
          </tr>
        </thead>
        <tbody>
            @foreach($order->orderItems as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->menuItem->name ?? 'Item' }}</td>
                <td class="right">{{ number_format($item->unit_price, 2) }}</td>
                <td class="center">{{ $item->quantity }}</td>
                <td class="right">{{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
      </table>

      <hr />

      <div class="flex">
        <span>Gross Total:</span>
        <span class="flex1">{{ number_format($order->subtotal, 2) }}</span>
      </div>
      <div class="flex">
        <span>Discount:</span>
        <span>{{ number_format($order->discount_amount, 2) }}</span>
      </div>
      <div class="flex">
        <span>Adjustment:</span>
        <span>0.00</span>
      </div>
      <hr />
      <div class="flex">
        <strong>Net Total:</strong>
        <strong>Rs {{ number_format($order->total_amount, 2) }}</strong>
      </div>

      <hr />
      <p class="small2">
        Thank you for your visit. <br />
        Please check your items before leaving.<br />
        Software by Antigravity AI.<br />
        Thank you.
      </p>
    </div>

    <script>
        // Auto-print on load if needed
        // window.onload = function() { window.print(); }
    </script>
  </body>
</html>
