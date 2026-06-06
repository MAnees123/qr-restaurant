@extends('layouts.guest')
@section('title', 'Pay for Order - ' . $restaurant->name)

@section('content')
    @php
        $cart = session()->get('cart', []);
        $cartCount = 0;
        foreach ($cart as $item) {
            $cartCount += $item['quantity'];
        }
        $hasActiveOrder = session()->has('active_order_number');
    @endphp
    <div class="shell" x-data="paymentPage()" x-init="init()">
        <!-- Header (Back, Order, Bell) -->
        <div class="header">
            <button class="btn-back" @click="goBack()">
                <i class="fas fa-chevron-left"></i> Back
            </button>
            <span class="header-title">Checkout</span>
            <button class="btn-bell"><i class="far fa-bell"></i></button>
        </div>

        <!-- Credit Card Preview -->
        <div class="card-wrap">
            <div class="credit-card">
                <div class="cc-chip"></div>
                <div class="cc-type">
                    <i class="fas fa-credit-card"></i> Card / Gateway Info
                </div>
                <div class="cc-number" x-text="formattedCardNumber()" placeholder="0123 4567 8901 2345"></div>
                <div class="cc-name" x-text="Name" placeholder="Muhammad Anees "></div>
            </div>
        </div>

        <!-- Address section -->
        <div class="sec-label">Delivery Table & Address</div>
        <div class="addr-card">
            <div>
                <div class="addr-name">Table {{ $table->table_number }} • <span x-text="addressName" placeholder="Muhammad Anees"></span></div>
                <div class="addr-text">
                    <span x-text="addressStreet" placeholder="Ahmed Pur East"></span><br />
                    <span x-text="addressCity" placeholder="Any City, ST 12345"></span>
                </div>
            </div>
            <button class="btn-change" @click="openAddressModal()">
                Change Details
            </button>
        </div>

        <!-- Payment Method: ONLY SafePay + Bitcoin -->
        <div class="sec-label">Payment Method</div>
        <div class="pay-list">
            <!-- 1️⃣ Safe Pay (active by default, only secure payment) -->
            <div class="pay-opt" :class="paymentMethod === 'safepay' ? 'active' : ''" @click="paymentMethod = 'safepay'">
                <div class="pay-icon">
                    <svg width="46" height="33" viewBox="0 0 46 33" fill="none">
                        <rect width="46" height="33" rx="7" fill="#0C5F4E" />
                        <rect x="0" y="0" width="46" height="33" rx="7" fill="url(#safeGrad)" opacity="0.4" />
                        <defs>
                            <linearGradient id="safeGrad" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0%" stop-color="#D8B25C" />
                                <stop offset="100%" stop-color="#E8890C" />
                            </linearGradient>
                        </defs>
                        <text x="5" y="16" font-family="Poppins, sans-serif" font-size="9" font-weight="800" fill="#ffffff">Safe</text>
                        <text x="5" y="27" font-family="Poppins, sans-serif" font-size="7.8" font-weight="700" fill="#F6DE9C">Pay</text>
                        <rect x="32" y="12" width="9" height="9" rx="1.8" fill="white" opacity="0.9" />
                        <path d="M34.5 12.5 V10.2 A2.5 2.5 0 0 1 39.5 10.2 V12.5" stroke="white" stroke-width="1.4" fill="none" stroke-linecap="round" opacity="0.95" />
                        <circle cx="36.5" cy="16.5" r="1.2" fill="#0C5F4E" opacity="0.9" />
                    </svg>
                </div>
                <div class="pay-info">
                    <div class="pay-name">SafePay</div>
                    <div class="pay-sub">Secure Credit/Debit Gateway · 3D Secure</div>
                </div>
                <div class="pay-radio">
                    <div class="pay-radio-dot"></div>
                </div>
            </div>

            <!-- 2️⃣ Bitcoin (crypto, safe decentralized) -->
            <div class="pay-opt" :class="paymentMethod === 'bitcoin' ? 'active' : ''" @click="paymentMethod = 'bitcoin'">
                <div class="pay-icon">
                    <svg width="46" height="33" viewBox="0 0 46 33" fill="none">
                        <rect width="46" height="33" rx="7" fill="#14100a" />
                        <text x="12" y="24" font-family="Arial, sans-serif" font-size="18" font-weight="900" fill="#f7931a">₿</text>
                        <circle cx="38" cy="8" r="4" fill="#f7931a" opacity="0.25" />
                        <circle cx="34" cy="13" r="2.5" fill="#f7931a" opacity="0.15" />
                    </svg>
                </div>
                <div class="pay-info">
                    <div class="pay-name">Bitcoin</div>
                    <div class="pay-sub">Crypto · Instant BTC Lightning</div>
                </div>
                <div class="pay-radio">
                    <div class="pay-radio-dot"></div>
                </div>
            </div>
        </div>

        <!-- Special instructions block -->
        <!-- <div class="sec-label">Order Notes</div>
        <div style="padding: 0 18px;">
            <textarea x-model="notes" placeholder="Any special cooking instructions (e.g. less spicy)?"
                class="w-full bg-[#262626] text-white border border-[#383838] rounded-xl p-3 text-xs outline-none focus:border-[#e8890c]" rows="2"></textarea>
        </div> -->

        <div class="spacer"></div>

        <!-- Bottom total + confirm -->
        <div class="bottom">
            <div class="total-row">
                <span class="total-lbl">Total Payment</span>
                <span class="total-val">Rs {{ number_format($totalAmount, 2) }}</span>
            </div>
            <button class="btn-confirm" @click="processPaymentGateways()">
                Confirm Order & Pay
            </button>
        </div>

        <!-- Address Modal (change address) -->
        <div class="modal-bg" :class="isModalOpen ? 'open' : ''" @click.self="isModalOpen = false">
            <div class="modal-box">
                <div class="modal-ttl">Change Customer Details</div>
                <input class="modal-inp" type="text" placeholder="Full Name" x-model="tempName" />
                <input class="modal-inp" type="text" placeholder="Street Address" x-model="tempStreet" />
                <input class="modal-inp" type="text" placeholder="City, State, ZIP" x-model="tempCity" />
                <button class="modal-save" @click="saveAddr()">
                    Save Details
                </button>
            </div>
        </div>

        <!-- SafePay Checkout Modal -->
        <div class="modal-bg" :class="gatewayOpen && paymentMethod === 'safepay' ? 'open' : ''">
            <div class="modal-box gateway-modal">
                <div class="gw-header">
                    <div class="gw-header-left">
                        <span class="gw-badge-safe">SafePay</span>
                        <span class="gw-secured"><i class="fas fa-lock"></i> Secured</span>
                    </div>
                    <button class="gw-close" @click="closeGateway()" :disabled="gatewayState !== 'idle' && gatewayState !== 'failed'">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Idle state: Enter Card details -->
                <template x-if="gatewayState === 'idle' || gatewayState === 'failed'">
                    <div class="gw-body">
                        <div class="gw-center">
                            <p class="gw-label">Total Amount to Pay</p>
                            <p class="gw-amount">Rs {{ number_format($totalAmount, 2) }}</p>
                        </div>

                        <div class="gw-body-sm">
                            <div>
                                <label class="gw-label">Cardholder Name</label>
                                <input type="text" x-model="ccName" class="modal-inp sm" placeholder="Name on Card" />
                            </div>
                            <div>
                                <label class="gw-label">Card Number</label>
                                <div class="gw-inp-wrap">
                                    <input type="text" x-model="ccNumber" x-mask="9999 9999 9999 9999" class="modal-inp sm has-icon" placeholder="4000 1234 5678 9010" />
                                    <i class="fas fa-credit-card gw-inp-icon"></i>
                                </div>
                            </div>
                            <div class="gw-row">
                                <div>
                                    <label class="gw-label">Expiry Date</label>
                                    <input type="text" x-model="ccExpiry" x-mask="99/99" class="modal-inp sm" placeholder="MM/YY" />
                                </div>
                                <div>
                                    <label class="gw-label">CVV / CVC</label>
                                    <input type="password" x-model="ccCvv" x-mask="999" class="modal-inp sm" placeholder="***" />
                                </div>
                            </div>
                        </div>

                        <button class="gw-pay-btn" @click="startSafePayTransaction()">
                            <i class="fas fa-shield-halved"></i> Pay Rs {{ number_format($totalAmount, 2) }}
                        </button>
                    </div>
                </template>

                <!-- Processing States -->
                <template x-if="gatewayState !== 'idle' && gatewayState !== 'failed'">
                    <div class="gw-processing">
                        <!-- Loading / Spinner -->
                        <div x-show="gatewayState === 'connecting' || gatewayState === 'processing' || gatewayState === 'authorizing'" class="gw-spinner-wrap">
                            <div class="gw-spinner"></div>
                            <p class="gw-proc-text" x-text="gatewayMessage">Processing transaction...</p>
                            <p class="gw-proc-sub">Please do not refresh or close this window.</p>
                        </div>

                        <!-- Success Animation -->
                        <div x-show="gatewayState === 'success'" class="gw-spinner-wrap">
                            <div class="gw-success-icon">
                                <i class="fas fa-check"></i>
                            </div>
                            <p class="gw-success-title">Payment Authorized!</p>
                            <p class="gw-success-txid">Transaction ID: <span x-text="transactionId"></span></p>
                            <p class="gw-success-sub">Creating your order...</p>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Bitcoin Lightning Checkout Modal -->
        <div class="modal-bg" :class="gatewayOpen && paymentMethod === 'bitcoin' ? 'open' : ''">
            <div class="modal-box gateway-modal">
                <div class="gw-header">
                    <div class="gw-header-left">
                        <span class="gw-badge-btc">Bitcoin</span>
                        <span class="gw-live">
                            <span class="gw-live-dot">
                                <span class="ping"></span>
                                <span class="dot"></span>
                            </span>
                            Live Pool
                        </span>
                    </div>
                    <button class="gw-close" @click="closeGateway()" :disabled="bitcoinTxState !== 'idle' && bitcoinTxState !== 'scanning' && bitcoinTxState !== 'success'">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- BTC Checkout View -->
                <div class="gw-body">
                    <div class="gw-center">
                        <p class="gw-label">Total Crypto Amount</p>
                        <p class="gw-amount-btc" x-text="btcAmount.toFixed(8) + ' BTC'"></p>
                        <p class="gw-small">Rate: 1 BTC ≈ 18,000,000 PKR</p>
                    </div>

                    <!-- QR and Address -->
                    <div class="btc-qr-wrap" x-show="bitcoinTxState === 'scanning' || bitcoinTxState === 'idle'">
                        <div class="btc-qr-box">
                            <img :src="btcQrUrl + btcAmount" class="btc-qr-img" alt="Bitcoin QR Code" />
                        </div>

                        <div class="btc-addr-box">
                            <div class="btc-addr-info">
                                <p class="btc-addr-label">BTC Lightning Address</p>
                                <p class="btc-addr-text" x-text="btcAddress"></p>
                            </div>
                            <button class="btc-copy-btn" @click="copyBtcAddress()">
                                <i class="far fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Realtime Mempool Monitor / Status -->
                    <div class="btc-status-box">
                        <div x-show="bitcoinTxState === 'scanning'">
                            <div class="btc-status-row">
                                <i class="fas fa-rotate fa-spin" style="color:#f7931a"></i>
                                <span>Listening to Lightning mempool for incoming invoice...</span>
                            </div>
                        </div>

                        <div x-show="bitcoinTxState === 'detected'">
                            <div class="btc-status-row detected">
                                <i class="fas fa-circle-notch fa-spin"></i>
                                <span>Transaction detected on network!</span>
                            </div>
                            <p class="btc-hash">Hash: btc-tx-a6b1897c9d08e5fc3a2...</p>
                        </div>

                        <div x-show="bitcoinTxState === 'success'">
                            <div class="btc-status-row confirmed">
                                <i class="fas fa-check-circle"></i>
                                <span>Payment Confirmed by 1 Blockchain node!</span>
                            </div>
                        </div>
                    </div>

                    <button x-show="bitcoinTxState === 'scanning' || bitcoinTxState === 'idle'"
                        class="gw-pay-btn btc"
                        @click="simulateBitcoinPayment()">
                        <i class="fab fa-bitcoin"></i> Simulated Pay invoice
                    </button>
                </div>
            </div>
        </div>

        <!-- Bottom Nav -->
        <div class="bottom-nav">
            <button class="nav-item" @click="window.location.href = '{{ route('menu.show', $code) }}#restaurant'">
                <i class="fas fa-utensils"></i>
                <span class="text-[9px] font-bold uppercase mt-0.5" style="color:#fff">Menu</span>
            </button>
            <button class="nav-item" @click="callWaiter()" :disabled="isCalling">
                <i class="fas fa-bell" :class="isCalling ? 'animate-bounce' : ''"></i>
                <span class="text-[9px] font-bold uppercase mt-0.5" style="color:#fff">Waiter</span>
            </button>
            <button class="nav-item relative active" @click="window.location.href = '{{ route('menu.show', $code) }}#cart'">
                <i class="fas fa-shopping-bag"></i>
                <span class="text-[9px] font-bold uppercase mt-0.5" style="color:#e8890c">Basket</span>
                @if($cartCount > 0)
                <span
                    class="bg-[#e8890c] text-white text-[8px] font-black rounded-full flex items-center justify-center absolute -top-0.5 -right-0.5"
                    style="min-width:16px;height:16px;padding:0 3px;">{{ $cartCount }}</span>
                @endif
            </button>
            <button class="nav-item" @click="window.location.href = '{{ route('menu.show', $code) }}#order'">
                <i class="fas fa-receipt"></i>
                <span class="text-[9px] font-bold uppercase mt-0.5" style="color:#fff">Order</span>
                @if($hasActiveOrder)
                <span class="bg-red-500 text-white text-[8px] font-black rounded-full absolute -top-0.5 -right-0.5"
                    style="min-width:8px;height:8px;"></span>
                @endif
            </button>
        </div>

        <!-- Toast Notifications -->
        <div class="toast" id="toast" :class="toastShow ? 'show' : ''" x-text="toastMessage"></div>
    </div>

    <style>
        *, *::before, *::after {
            box-sizing: border-box;
        }
        body {
            background: #0e0e0e;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            font-family: "Poppins", sans-serif;
            margin: 0;
            padding: 0;
        }

        /* Phone shell */
        .shell {
            width: 100%;
            max-width: 425px;
            min-height: 100vh;
            background: #1c1c1c;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            overflow-y: auto;
            padding-bottom: 84px;
            box-shadow: 0 0 40px rgba(0, 0, 0, 0.85);
        }
        .shell::-webkit-scrollbar {
            display: none;
        }

        /* Bottom Nav */
        .bottom-nav {
            position: fixed;
            left: 50%;
            transform: translateX(-50%);
            bottom: 0;
            width: 100%;
            max-width: 425px;
            background: #1c1c1c;
            border-top: 1px solid #2a2a2a;
            display: flex;
            justify-content: space-around;
            padding: 10px 0 14px;
            z-index: 120;
        }

        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            background: none;
            border: none;
            cursor: pointer;
            position: relative;
        }

        .nav-item i {
            font-size: 20px;
            color: #fff;
        }

        .nav-item.active i {
            color: #e8890c;
        }

        /* Header */
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 22px 20px 14px;
            position: sticky;
            top: 0;
            background: #1c1c1c;
            z-index: 20;
        }
        .btn-back {
            display: flex;
            align-items: center;
            gap: 6px;
            background: none;
            border: none;
            cursor: pointer;
            color: #e8890c;
            font-size: 13px;
            font-weight: 500;
            font-family: "Poppins", sans-serif;
            min-width: 64px;
            padding: 0;
            text-align: left;
        }
        .btn-back i {
            font-size: 11px;
        }
        .header-title {
            font-family: "Dancing Script", cursive;
            font-size: 24px;
            font-weight: 700;
            color: #fff;
            flex: 1;
            text-align: center;
        }
        .btn-bell {
            background: none;
            border: none;
            cursor: pointer;
            color: #fff;
            font-size: 20px;
            min-width: 64px;
            text-align: right;
        }

        /* Credit card */
        .card-wrap {
            padding: 4px 18px 0;
        }
        .credit-card {
            background: linear-gradient(
                135deg,
                #f0900d 0%,
                #c96018 55%,
                #a84210 100%
            );
            border-radius: 20px;
            padding: 22px 22px 20px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 36px rgba(232, 137, 12, 0.4);
        }
        .credit-card::before {
            content: "";
            position: absolute;
            top: -50px;
            right: -50px;
            width: 170px;
            height: 170px;
            background: rgba(255, 255, 255, 0.07);
            border-radius: 50%;
        }
        .credit-card::after {
            content: "";
            position: absolute;
            bottom: -55px;
            right: 20px;
            width: 130px;
            height: 130px;
            background: rgba(255, 255, 255, 0.04);
            border-radius: 50%;
        }
        .cc-chip {
            position: absolute;
            top: 20px;
            right: 22px;
            width: 38px;
            height: 28px;
            background: rgba(255, 255, 255, 0.22);
            border-radius: 6px;
            border: 1px solid rgba(255, 255, 255, 0.28);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .cc-chip::before {
            content: "";
            width: 22px;
            height: 15px;
            border: 1.5px solid rgba(255, 255, 255, 0.45);
            border-radius: 3px;
        }
        .cc-type {
            display: flex;
            align-items: center;
            gap: 7px;
            color: rgba(255, 255, 255, 0.88);
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.4px;
            margin-bottom: 16px;
        }
        .cc-type i {
            font-size: 17px;
        }
        .cc-number {
            color: #fff;
            font-size: 19px;
            font-weight: 700;
            letter-spacing: 2.5px;
            margin-bottom: 10px;
            font-variant-numeric: tabular-nums;
            font-family: monospace;
        }
        .cc-name {
            color: rgba(255, 255, 255, 0.88);
            font-size: 13px;
            font-weight: 500;
            letter-spacing: 0.4px;
            text-transform: uppercase;
        }

        /* section label */
        .sec-label {
            color: #c8c8c8;
            font-size: 14px;
            font-weight: 500;
            padding: 20px 18px 10px;
            letter-spacing: 0.15px;
        }

        /* Address card */
        .addr-card {
            margin: 0 18px;
            background: #262626;
            border-radius: 14px;
            padding: 14px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            border: 1px solid #333;
        }
        .addr-name {
            color: #fff;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 4px;
        }
        .addr-text {
            color: #888;
            font-size: 12px;
            line-height: 1.55;
        }
        .btn-change {
            background: none;
            border: none;
            cursor: pointer;
            color: #e8890c;
            font-size: 12px;
            font-weight: 500;
            font-family: "Poppins", sans-serif;
            white-space: nowrap;
            flex-shrink: 0;
            padding: 0;
        }

        /* Payment methods list (ONLY SafePay + Bitcoin) */
        .pay-list {
            padding: 0 18px;
            display: flex;
            flex-direction: column;
            gap: 11px;
        }
        .pay-opt {
            background: #262626;
            border-radius: 14px;
            padding: 13px 15px;
            display: flex;
            align-items: center;
            gap: 13px;
            cursor: pointer;
            border: 2px solid transparent;
            transition:
                border-color 0.18s,
                background 0.18s;
            -webkit-tap-highlight-color: transparent;
        }
        .pay-opt:hover {
            background: #2d2d2d;
        }
        .pay-opt.active {
            border-color: #e8890c;
            background: #2d2d2d;
        }

        .pay-icon {
            width: 46px;
            height: 33px;
            border-radius: 8px;
            flex-shrink: 0;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .pay-info {
            flex: 1;
            min-width: 0;
        }
        .pay-name {
            color: #fff;
            font-size: 13px;
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .pay-sub {
            color: #777;
            font-size: 10.5px;
            font-weight: 400;
            margin-top: 1px;
        }

        /* custom radio */
        .pay-radio {
            width: 19px;
            height: 19px;
            border-radius: 50%;
            border: 2px solid #4a4a4a;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: border-color 0.18s;
        }
        .pay-opt.active .pay-radio {
            border-color: #e8890c;
        }
        .pay-radio-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: #e8890c;
            opacity: 0;
            transform: scale(0.4);
            transition:
                opacity 0.18s,
                transform 0.18s;
        }
        .pay-opt.active .pay-radio-dot {
            opacity: 1;
            transform: scale(1);
        }

        .spacer {
            flex: 1;
            min-height: 22px;
        }

        /* bottom total + confirm */
        .bottom {
            padding: 18px 18px 0;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            padding: 0 4px;
        }
        .total-lbl {
            color: #fff;
            font-size: 15px;
            font-weight: 700;
        }
        .total-val {
            color: #e8890c;
            font-size: 16px;
            font-weight: 700;
        }

        .btn-confirm {
            width: 100%;
            background: #bf441a;
            color: #fff;
            border: none;
            border-radius: 16px;
            padding: 16px;
            font-size: 15px;
            font-weight: 600;
            font-family: "Poppins", sans-serif;
            cursor: pointer;
            letter-spacing: 0.3px;
            transition:
                background 0.2s,
                transform 0.1s;
        }
        .btn-confirm:hover {
            background: #e8890c;
        }
        .btn-confirm:active {
            transform: scale(0.98);
        }

        /* toast message */
        .toast {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%) translateY(16px);
            background: #e8890c;
            color: #fff;
            padding: 10px 22px;
            border-radius: 50px;
            font-size: 12.5px;
            font-weight: 600;
            font-family: "Poppins", sans-serif;
            opacity: 0;
            pointer-events: none;
            transition:
                opacity 0.28s,
                transform 0.28s;
            z-index: 999;
            white-space: nowrap;
            box-shadow: 0 4px 12px rgba(0,0,0,0.5);
        }
        .toast.show {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }

        /* address modal */
        .modal-bg {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.75);
            z-index: 200;
            align-items: flex-end;
            justify-content: center;
        }
        .modal-bg.open {
            display: flex;
        }
        .modal-box {
            background: #262626;
            border-radius: 22px 22px 0 0;
            padding: 22px 20px 36px;
            width: 100%;
            max-width: 425px;
            animation: up 0.28s ease;
            border-top: 1px solid #383838;
        }
        .gateway-modal {
            border-radius: 22px;
            margin-bottom: auto;
            margin-top: auto;
            border: 1px solid #383838;
            box-shadow: 0 10px 30px rgba(0,0,0,0.8);
            width: calc(100% - 24px);
            max-width: 400px;
            max-height: 90vh;
            overflow-y: auto;
            animation: fadeIn 0.25s ease;
        }
        @keyframes up {
            from {
                transform: translateY(100%);
            }
            to {
                transform: translateY(0);
            }
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        .modal-ttl {
            color: #fff;
            font-size: 16px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 16px;
        }
        .modal-inp {
            width: 100%;
            background: #1c1c1c;
            border: 1.5px solid #383838;
            border-radius: 11px;
            padding: 12px 14px;
            color: #fff;
            font-size: 13px;
            font-family: "Poppins", sans-serif;
            outline: none;
            margin-bottom: 10px;
            transition: border-color 0.2s;
        }
        .modal-inp:focus {
            border-color: #e8890c;
        }
        .modal-save {
            width: 100%;
            background: #bf441a;
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 13px;
            font-size: 14px;
            font-weight: 600;
            font-family: "Poppins", sans-serif;
            cursor: pointer;
            margin-top: 4px;
            transition: background 0.2s;
        }
        .modal-save:hover {
            background: #e8890c;
        }
        .modal-save:disabled {
            background: #444;
            color: #888;
            cursor: not-allowed;
        }

        /* Gateway modal inner layout helpers (mobile-safe, no Tailwind dependency) */
        .gw-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            border-bottom: 1px solid #383838;
            padding-bottom: 12px;
        }
        .gw-header-left {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .gw-badge-safe {
            background: #0C5F4E;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            color: #fff;
            letter-spacing: 1.5px;
        }
        .gw-badge-btc {
            background: #f7931a;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            color: #000;
            letter-spacing: 1.5px;
        }
        .gw-secured {
            font-size: 12px;
            color: #34d399;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .gw-secured i { font-size: 10px; }
        .gw-live {
            font-size: 12px;
            color: #f59e0b;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .gw-live-dot {
            position: relative;
            display: flex;
            width: 10px;
            height: 10px;
        }
        .gw-live-dot .ping {
            position: absolute;
            display: inline-flex;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: #fbbf24;
            opacity: 0.75;
            animation: ping 1s cubic-bezier(0,0,0.2,1) infinite;
        }
        .gw-live-dot .dot {
            position: relative;
            display: inline-flex;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #f59e0b;
        }
        @keyframes ping {
            75%, 100% { transform: scale(2); opacity: 0; }
        }
        .gw-close {
            background: none;
            border: none;
            color: #71717a;
            font-size: 16px;
            cursor: pointer;
            padding: 4px;
            transition: color 0.2s;
        }
        .gw-close:hover { color: #fff; }
        .gw-body {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .gw-body-sm {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .gw-center {
            text-align: center;
            padding: 4px 0;
        }
        .gw-label {
            font-size: 10px;
            text-transform: uppercase;
            font-weight: 700;
            color: #a1a1aa;
            letter-spacing: 0.8px;
        }
        .gw-amount {
            font-size: 24px;
            font-weight: 900;
            color: #fff;
            margin-top: 2px;
        }
        .gw-amount-btc {
            font-size: 24px;
            font-weight: 900;
            color: #f7931a;
            margin-top: 2px;
        }
        .gw-small {
            font-size: 10px;
            color: #71717a;
            margin-top: 2px;
        }
        .gw-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        .gw-inp-wrap {
            position: relative;
        }
        .gw-inp-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #71717a;
            font-size: 14px;
        }
        .modal-inp.has-icon {
            padding-left: 40px;
        }
        .modal-inp.sm {
            margin-top: 4px;
            margin-bottom: 0;
            font-size: 14px;
        }
        .gw-pay-btn {
            width: 100%;
            background: #0C5F4E;
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-size: 14px;
            font-weight: 600;
            font-family: "Poppins", sans-serif;
            cursor: pointer;
            margin-top: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background 0.2s;
        }
        .gw-pay-btn:hover { background: #047857; }
        .gw-pay-btn.btc {
            background: #f7931a;
            color: #000;
            font-weight: 700;
        }
        .gw-pay-btn.btc:hover { background: #d97706; }
        /* Processing states */
        .gw-processing {
            padding: 32px 0;
            text-align: center;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }
        .gw-spinner-wrap {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .gw-spinner {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            border: 2px solid transparent;
            border-top-color: #10b981;
            border-bottom-color: #10b981;
            animation: spin 1s linear infinite;
            margin-bottom: 16px;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .gw-proc-text {
            font-size: 14px;
            font-weight: 700;
            color: #fff;
        }
        .gw-proc-sub {
            font-size: 10px;
            color: #71717a;
            margin-top: 4px;
        }
        .gw-success-icon {
            width: 64px;
            height: 64px;
            background: rgba(16, 185, 129, 0.2);
            color: #34d399;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            border: 1px solid rgba(16, 185, 129, 0.4);
            font-size: 24px;
        }
        .gw-success-title {
            font-size: 16px;
            font-weight: 900;
            color: #fff;
        }
        .gw-success-txid {
            font-size: 12px;
            color: #a1a1aa;
            margin-top: 4px;
        }
        .gw-success-sub {
            font-size: 10px;
            color: #10b981;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 16px;
        }
        /* BTC QR area */
        .btc-qr-wrap {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 8px 0;
        }
        .btc-qr-box {
            background: #fff;
            padding: 12px;
            border-radius: 16px;
            margin-bottom: 16px;
            border: 1px solid rgba(247, 147, 26, 0.3);
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }
        .btc-qr-img {
            width: 160px;
            height: 160px;
            object-fit: contain;
        }
        .btc-addr-box {
            width: 100%;
            background: #1c1c1c;
            border: 1px solid #383838;
            border-radius: 12px;
            padding: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .btc-addr-info {
            min-width: 0;
            flex: 1;
        }
        .btc-addr-label {
            font-size: 9px;
            text-transform: uppercase;
            font-weight: 900;
            color: #71717a;
            letter-spacing: 1px;
        }
        .btc-addr-text {
            font-size: 12px;
            color: #fff;
            font-family: monospace;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            margin-top: 2px;
        }
        .btc-copy-btn {
            padding: 8px;
            background: rgba(247, 147, 26, 0.15);
            color: #f7931a;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.2s;
            flex-shrink: 0;
        }
        .btc-copy-btn:hover { background: rgba(247, 147, 26, 0.25); }
        .btc-status-box {
            background: #1c1c1c;
            border: 1px solid #383838;
            border-radius: 12px;
            padding: 16px;
            text-align: center;
        }
        .btc-status-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            font-size: 12px;
            color: #a1a1aa;
        }
        .btc-status-row.detected {
            color: #f59e0b;
            font-weight: 700;
        }
        .btc-status-row.confirmed {
            color: #34d399;
            font-weight: 700;
        }
        .btc-hash {
            font-size: 10px;
            color: #71717a;
            font-family: monospace;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            margin-top: 8px;
        }

        /* Mobile responsive */
        @media (max-width: 430px) {
            .shell, .bottom-nav, .modal-box {
                max-width: 100%;
            }
            .gateway-modal {
                width: calc(100% - 16px);
                max-width: 100%;
            }
        }
    </style>
@endsection

@section('scripts')
    <!-- Input mask utility for credit card styling -->
    <script src="https://cdn.jsdelivr.net/npm/@alpinejs/mask@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        function paymentPage() {
            return {
                notes: '',
                paymentMethod: 'safepay',
                isModalOpen: false,
                gatewayOpen: false,
                gatewayState: 'idle', // idle, connecting, processing, authorizing, success, failed
                gatewayMessage: '',
                isCalling: false,
                
                addressName: 'Muhammad Anees ',
                addressStreet: 'Ahmed Pur East',
                addressCity: 'Any City, ST 12345',
                
                tempName: '',
                tempStreet: '',
                tempCity: '',
                
                // Credit card inputs
                ccName: 'Muhammad Anees ',
                ccNumber: '4000 1234 5678 9010',
                ccExpiry: '12/28',
                ccCvv: '123',
                
                // Bitcoin Inputs
                btcAmount: 0.0001, // will calculate dynamically based on totalAmount
                btcAddress: 'bc1q8c8gkyplshcm6g29x0e42g64s0w5w4z0q8x2z3',
                btcQrUrl: 'https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=bitcoin:bc1q8c8gkyplshcm6g29x0e42g64s0w5w4z0q8x2z3?amount=',
                bitcoinTxState: 'idle', // idle, scanning, detected, success
                
                transactionId: '',
                toastMessage: '',
                toastShow: false,
                
                totalAmount: {{ $totalAmount }},
                
                init() {
                    // Prepopulate modal fields
                    this.tempName = this.addressName;
                    this.tempStreet = this.addressStreet;
                    this.tempCity = this.addressCity;
                    
                    // Convert PKR to BTC (Rate: 1 BTC = 18,000,000 PKR)
                    this.btcAmount = this.totalAmount / 18000000;
                },
                
                goBack() {
                    window.location.href = '{{ route('menu.show', $code) }}';
                },
                
                callWaiter() {
                    if (this.isCalling) return;
                    this.isCalling = true;
                    axios.post('{{ route('table.call') }}')
                        .then(response => {
                            this.isCalling = false;
                            this.showToast('🔔 Waiter has been requested!');
                        })
                        .catch(error => {
                            this.showToast(error.response?.data?.error || 'Something went wrong.');
                            this.isCalling = false;
                        });
                },
                
                openAddressModal() {
                    this.tempName = this.addressName;
                    this.tempStreet = this.addressStreet;
                    this.tempCity = this.addressCity;
                    this.isModalOpen = true;
                },
                
                saveAddr() {
                    if (!this.tempName.trim() || !this.tempStreet.trim() || !this.tempCity.trim()) {
                        this.showToast("Please fill all details.");
                        return;
                    }
                    this.addressName = this.tempName;
                    this.addressStreet = this.tempStreet;
                    this.addressCity = this.tempCity;
                    this.ccName = this.tempName; // sync ccName
                    this.isModalOpen = false;
                    this.showToast("📍 Delivery details updated.");
                },
                
                formattedCardNumber() {
                    if (this.paymentMethod === 'bitcoin') {
                        return 'BITCOIN LIGHTNING INSTANT';
                    }
                    if (!this.ccNumber) return '•••• •••• •••• ••••';
                    return this.ccNumber;
                },
                
                showToast(msg) {
                    this.toastMessage = msg;
                    this.toastShow = true;
                    clearTimeout(this._tid);
                    this._tid = setTimeout(() => this.toastShow = false, 2400);
                },
                
                copyBtcAddress() {
                    navigator.clipboard.writeText(this.btcAddress);
                    this.showToast("📋 Bitcoin address copied!");
                },
                
                closeGateway() {
                    this.gatewayOpen = false;
                    this.gatewayState = 'idle';
                    this.bitcoinTxState = 'idle';
                },
                
                processPaymentGateways() {
                    this.gatewayOpen = true;
                    if (this.paymentMethod === 'safepay') {
                        this.gatewayState = 'idle';
                    } else if (this.paymentMethod === 'bitcoin') {
                        this.bitcoinTxState = 'scanning';
                    }
                },
                
                // SafePay Simulated Secure Gateway Flow
                startSafePayTransaction() {
                    if (!this.ccName.trim() || this.ccNumber.length < 15 || !this.ccExpiry || this.ccCvv.length < 3) {
                        this.showToast("⚠️ Please enter valid credit card details.");
                        return;
                    }
                    
                    this.gatewayState = 'connecting';
                    this.gatewayMessage = 'Connecting to SafePay secure payment server...';
                    
                    setTimeout(() => {
                        this.gatewayState = 'processing';
                        this.gatewayMessage = 'Processing card transaction...';
                        
                        setTimeout(() => {
                            this.gatewayState = 'authorizing';
                            this.gatewayMessage = 'Authenticating with 3D Secure Verification...';
                            
                            setTimeout(() => {
                                // Generate a secure transaction ID
                                this.transactionId = 'SF-' + Math.floor(10000000 + Math.random() * 90000000);
                                this.gatewayState = 'success';
                                this.showToast("✅ SafePay payment successful!");
                                
                                // Complete order creation on backend
                                this.submitOrderToBackend('safepay', this.transactionId);
                            }, 1500);
                        }, 1200);
                    }, 1000);
                },
                
                // Bitcoin Lightning Simulated Gateway Flow
                simulateBitcoinPayment() {
                    if (this.bitcoinTxState !== 'scanning') return;
                    
                    this.bitcoinTxState = 'detected';
                    this.showToast("⚡ Payment invoice detected in mempool!");
                    
                    setTimeout(() => {
                        this.bitcoinTxState = 'success';
                        this.showToast("✅ Transaction confirmed on blockchain!");
                        
                        setTimeout(() => {
                            // Generate transaction ID
                            const txId = 'btc-tx-' + Math.random().toString(36).substring(2, 12);
                            this.submitOrderToBackend('bitcoin', txId);
                        }, 1000);
                    }, 2000);
                },
                
                submitOrderToBackend(method, txId) {
                    const addressStr = `${this.addressName}, ${this.addressStreet}, ${this.addressCity}`;
                    
                    axios.post('{{ route('order.place') }}', {
                        notes: this.notes,
                        payment_method: method,
                        payment_status: 'paid',
                        transaction_id: txId,
                        address: addressStr
                    })
                    .then(response => {
                        this.showToast('🎉 Order placed and paid successfully!');
                        setTimeout(() => {
                            if (response.data.redirect) {
                                window.location.href = response.data.redirect;
                            } else {
                                window.location.href = '{{ route('menu.show', $code) }}';
                            }
                        }, 1500);
                    })
                    .catch(error => {
                        this.showToast(error.response?.data?.error || 'Something went wrong while placing order.');
                        this.gatewayState = 'failed';
                        this.bitcoinTxState = 'idle';
                    });
                }
            }
        }
    </script>
@endsection
