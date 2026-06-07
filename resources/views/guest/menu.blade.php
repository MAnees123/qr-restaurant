@extends('layouts.guest')
@section('title', $restaurant->name . ' - Menu')

@section('content')
    @php
        function getCategoryEmoji($name)
        {
            $name = strtolower($name);
            if (str_contains($name, 'starter') || str_contains($name, 'appetizer') || str_contains($name, 'chaat')) {
                return '🍿';
            }
            if (
                str_contains($name, 'main') ||
                str_contains($name, 'course') ||
                str_contains($name, 'karahi') ||
                str_contains($name, 'handi')
            ) {
                return '🍛';
            }
            if (str_contains($name, 'bbq') || str_contains($name, 'barbeque') || str_contains($name, 'meat')) {
                return '🥩';
            }
            if (str_contains($name, 'dessert') || str_contains($name, 'sweet')) {
                return '🍮';
            }
            if (str_contains($name, 'drink') || str_contains($name, 'beverage') || str_contains($name, 'tea')) {
                return '🥤';
            }
            if (str_contains($name, 'pizza') || str_contains($name, 'fast')) {
                return '🍕';
            }
            return '🍽️';
        }

        $featured = null;
        foreach ($categories as $category) {
            if ($category->menuItems->count() > 0) {
                $featured = $category->menuItems->first();
                break;
            }
        }
    @endphp

    <div class="phone-shell max-w-md mx-auto min-h-screen relative" :class="darkMode ? '' : 'light-theme'" x-data="customerSPA()"
        x-init="init()">

        <!-- Waiter Status Banner (Dismissible) -->
        <template x-if="!statusDismissed && (callStatus === 'sent' || callStatus === 'accepted')">
            <div
                class="bg-amber-500/10 border-b border-amber-500/20 px-6 py-3 flex items-center justify-between animate-pulse">
                <div class="flex items-center gap-3 text-[#e8890c]">
                    <i class="fas fa-bell"></i>
                    <div>
                        <p class="text-[11px] font-black uppercase tracking-widest"
                            x-text="callStatus === 'sent' ? 'Waiter Requested' : 'Waiter is coming'"></p>
                        <p class="text-[9px] text-[#ccc] font-bold"
                            x-text="callStatus === 'sent' ? 'Waiting for staff to acknowledge...' : 'A staff member is on their way!'">
                        </p>
                    </div>
                </div>
                <button @click="dismissCallStatus()" class="text-[#e8890c]/70 hover:text-[#e8890c] p-2">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </template>

        <!-- Header -->
        <div class="app-header px-6 py-4 flex justify-between items-start sticky top-0 bg-[#1c1c1c] z-50">
            <div>
                <div class="greeting-name">Hi, Table {{ $table->table_number }}</div>
                <div class="greeting-sub">Ready to order from {{ $restaurant->name }}?</div>
            </div>
            <button class="cart-btn relative text-white"
                @click="activeTab = 'cart'; window.scrollTo({top: 0, behavior: 'smooth'})">
                <i class="fas fa-shopping-cart text-xl"></i>
                <span
                    class="cart-badge bg-[#e8890c] text-white text-[9px] font-bold w-4 h-4 rounded-full flex items-center justify-center absolute -top-1.5 -right-1.5"
                    x-show="cartCount > 0" x-text="cartCount"></span>
            </button>
        </div>

        <!-- Search Section -->
        <div class="search-wrap px-6 pb-4" x-show="activeTab === 'restaurant'">
            <div class="search-row">
                <div class="search-box">
                    <input type="text" placeholder="Search menu..." x-model="searchQuery" />
                    <i class="fas fa-search"></i>
                </div>
                <button class="theme-toggle-btn" :class="!darkMode ? 'active' : ''" @click="toggleTheme()" :title="darkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode'">
                    <i class="fas" :class="darkMode ? 'fa-sun' : 'fa-moon'"></i>
                </button>
            </div>
        </div>

        <!-- MAIN MENU TAB CONTENT -->
        <div x-show="activeTab === 'restaurant'" class="flex-1 pb-20">
            <!-- Banners/Ads Slider -->
            @if(isset($banners) && $banners->count() > 0)
                <div class="px-4 mb-4" x-data="bannerSlider({{ $banners->count() }})">
                    <div class="relative w-full rounded-2xl overflow-hidden shadow-lg h-40 bg-[#2a2a2a]"
                         @mouseenter="pause()" @mouseleave="resume()">
                        
                        <!-- Slider Track -->
                        <div class="flex transition-transform duration-500 ease-in-out h-full w-full"
                             :style="`transform: translateX(-${currentIndex * 100}%)`"
                             @touchstart="touchStart($event)"
                             @touchmove="touchMove($event)"
                             @touchend="touchEnd()">
                            
                            @foreach($banners as $index => $banner)
                                <div class="w-full flex-shrink-0 h-full relative cursor-pointer" 
                                     @if($banner->redirect_url) @click="window.location.href='{{ $banner->redirect_url }}'" @endif>
                                    <img src="{{ asset('storage/' . $banner->image_path) }}" class="w-full h-full object-cover" alt="Banner">
                                    
                                    <!-- Overlay for Text -->
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent flex flex-col justify-end p-4 pointer-events-none">
                                        @if($banner->title)
                                            <h3 class="text-white font-black text-lg leading-tight">{{ $banner->title }}</h3>
                                        @endif
                                        @if($banner->subtitle)
                                            <p class="text-slate-300 text-xs font-bold mt-1">{{ $banner->subtitle }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Dots Indicator -->
                        @if($banners->count() > 1)
                            <div class="absolute bottom-3 left-0 right-0 flex justify-center gap-1.5 z-10">
                                <template x-for="i in {{ $banners->count() }}" :key="i">
                                    <div class="h-1.5 rounded-full transition-all duration-300 cursor-pointer"
                                         :class="(i - 1) === currentIndex ? 'w-4 bg-[#e8890c]' : 'w-1.5 bg-white/50'"
                                         @click="goTo(i - 1)"></div>
                                </template>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Hero Banner (Featured Item) -->
            @if ($featured)
                <div class="hero-banner"
                    @click="searchQuery = '{{ addslashes($featured->name) }}'; window.scrollTo({top: 280, behavior: 'smooth'})"
                    style="cursor: pointer;">
                    <div>
                        <div class="hero-badge">Featured</div>
                        <div class="hero-title">{{ $featured->name }}</div>
                        <div class="hero-meta">
                            <div class="hero-meta-item">
                                <i class="fas fa-clock"></i>
                                <span>{{ $featured->preparation_time }} Minutes</span>
                            </div>
                            <div class="hero-meta-item mt-1">
                                <i class="fas fa-utensils"></i>
                                <span>Rs {{ number_format($featured->price) }}</span>
                            </div>
                        </div>
                    </div>
                    @if ($featured->image)
                        <img src="{{ Storage::url($featured->image) }}" class="hero-img" alt="{{ $featured->name }}">
                    @else
                        <div class="hero-img-placeholder text-4xl">🍛</div>
                    @endif
                </div>
            @endif

            <!-- Category Section -->
            <div class="section-header">
                <div class="section-title">Category</div>
            </div>

            <div class="category-scroll">
                <button class="cat-btn" :class="activeCategory === 'all' ? 'active' : ''"
                    @click="activeCategory = 'all'">
                    <span class="cat-icon">🍽️</span> All
                </button>
                @foreach ($categories as $category)
                    <button class="cat-btn" :class="activeCategory === '{{ $category->id }}' ? 'active' : ''"
                        @click="activeCategory = '{{ $category->id }}'">
                        <span class="cat-icon">{{ getCategoryEmoji($category->name) }}</span> {{ $category->name }}
                    </button>
                @endforeach
            </div>

            <!-- Section Heading -->
            <div class="best-seller-title uppercase"
                x-text="activeCategory === 'all' ? 'Menu List' : (categories.find(cat => cat.id == activeCategory)?.name || 'Category Items')">
            </div>

            <!-- Empty state when search or category yields nothing -->
            <template x-if="filteredItems.length === 0">
                <div class="text-center py-12 text-[#888]">
                    <i class="fas fa-search-minus text-4xl mb-4 text-[#333]"></i>
                    <p class="text-sm">No items found matching criteria.</p>
                </div>
            </template>

            <!-- Cards Grid -->
            <div class="cards-grid">
                <template x-for="item in filteredItems" :key="item.id">
                    <div class="food-card">
                        <div class="food-card-img-wrap"
                            :style="item.image ? '' : 'background:linear-gradient(135deg,#2a1a0a,#3a2a10);'">
                            <template x-if="item.image">
                                <img :src="'/storage/' + item.image" class="w-full h-full object-cover"
                                    :alt="item.name">
                            </template>
                            <template x-if="!item.image">
                                <div class="food-card-img-placeholder" x-text="getEmoji(item.name, item.category_name)">
                                </div>
                            </template>
                            <button class="heart-btn" :class="likedItems.includes(item.id) ? 'liked' : ''"
                                @click="toggleLike(item.id)">
                                <i class="fas fa-heart"
                                    :style="likedItems.includes(item.id) ? 'color:#e8890c' : 'color:#888'"></i>
                            </button>
                        </div>
                        <div class="food-card-body">
                            <div>
                                <div class="food-card-name" x-text="item.name"></div>
                                <div class="food-card-rating">
                                    <i class="fas fa-star"></i>
                                    <span>4.9</span>
                                    <span class="food-card-price"
                                        x-text="'Rs ' + parseFloat(item.price).toLocaleString()"></span>
                                </div>
                            </div>
                            <div>
                                <div class="food-card-meta">
                                    <span><i class="fas fa-clock" style="font-size:9px;color:#888"></i> <span
                                            x-text="item.preparation_time || 15"></span> Mins</span>
                                </div>
                                <button @click="addToCart(item.id, item.name, item.price)"
                                    class="w-full mt-2 bg-[#c0441a] hover:bg-[#e8890c] text-white py-1.5 rounded-lg font-bold text-[10px] uppercase tracking-wider transition">
                                    ADD +
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- ORDER STATUS TAB CONTENT -->
        <div x-show="activeTab === 'order'" class="flex-1 p-6 pb-20" style="display: none;">
            <template x-if="guestOrders.length === 0">
                <div class="min-h-[50vh] flex flex-col items-center justify-center text-center p-8">
                    <div class="w-20 h-20 bg-[#252525] rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-receipt text-3xl text-[#555]"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">No Active Orders</h3>
                    <p class="text-xs text-[#888] leading-relaxed">Place an order from the menu to see its live tracking status here.</p>
                    <button @click="activeTab = 'restaurant'" class="mt-6 bg-[#c0441a] text-white px-6 py-3 rounded-xl font-bold text-xs uppercase tracking-wider transition">Browse Menu</button>
                </div>
            </template>

            <template x-if="guestOrders.length > 0">
                <div class="space-y-6">
                    <!-- Global Countdown for the most recent active order -->
                    <template x-if="activeOrder && activeOrder.status !== 'served' && activeOrder.status !== 'cancelled'">
                        <div class="credit-card">
                            <div class="card-chip"></div>
                            <div class="card-label">
                                <i class="fas fa-clock"></i> Estimated Prep Countdown (Order #<span x-text="activeOrder.order_number"></span>)
                            </div>
                            <div class="flex justify-center gap-4 py-2">
                                <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-2 w-16 text-center">
                                    <p class="text-2xl font-black text-white" x-text="countdown.minutes">00</p>
                                    <p class="text-[8px] uppercase font-black text-[#ccc] mt-0.5">Mins</p>
                                </div>
                                <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-2 w-16 text-center">
                                    <p class="text-2xl font-black text-white" x-text="countdown.seconds">00</p>
                                    <p class="text-[8px] uppercase font-black text-[#ccc] mt-0.5">Secs</p>
                                </div>
                            </div>
                            <div class="card-holder text-center mt-3 uppercase tracking-widest font-black text-white" x-text="'Status: ' + activeOrder.status"></div>
                        </div>
                    </template>

                    <template x-for="order in guestOrders" :key="order.id">
                        <div class="bg-[#252525] rounded-2xl p-5 border border-[#3a3a3a] space-y-4">
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-white">Order Number</span>
                                <span class="text-white font-bold uppercase tracking-wider" x-text="order.order_number"></span>
                            </div>
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-white">Order Status</span>
                                <span class="text-[#e8890c] font-bold uppercase tracking-wider" x-text="order.status"></span>
                            </div>
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-white">Payment Status</span>
                                <span class="bg-[#c0441a]/20 text-white px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider border border-[#c0441a]/30" x-text="order.payment_status"></span>
                            </div>
                            <hr class="border-[#3a3a3a]" />

                            <div class="space-y-3">
                                <template x-for="item in order.order_items" :key="item.id">
                                    <div class="flex justify-between items-center text-xs">
                                        <div class="flex items-center gap-2">
                                            <span class="text-white font-black" x-text="item.quantity + 'x'"></span>
                                            <span class="text-white font-medium" x-text="item.menu_item.name"></span>
                                        </div>
                                        <span class="text-white" x-text="'Rs ' + parseFloat(item.subtotal).toLocaleString()"></span>
                                    </div>
                                </template>
                            </div>

                            <hr class="border-[#3a3a3a] border-dashed" />
                            <div class="flex justify-between items-center font-bold text-sm">
                                <span class="text-white">Total Amount</span>
                                <span class="text-white" x-text="'Rs ' + parseFloat(order.total_amount).toLocaleString()"></span>
                            </div>
                            
                            <template x-if="order.payment_status !== 'paid' && order.status !== 'cancelled'">
                                <div class="text-center mt-3 bg-[#e8890c]/20 border border-[#e8890c]/50 text-[#e8890c] py-2 rounded-lg font-bold text-[10px] uppercase tracking-wider">
                                    Payment Pending (Pay at Counter)
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        <!-- Bottom Nav -->
        <div class="bottom-nav">
            <button class="nav-item" :class="activeTab === 'restaurant' ? 'active' : ''"
                @click="activeTab = 'restaurant'; window.location.hash = 'restaurant'; window.scrollTo({top: 0, behavior: 'smooth'})">
                <i class="fas fa-utensils"></i>
                <span class="text-[9px] font-bold uppercase mt-0.5"
                    :style="activeTab === 'restaurant' ? 'color:#e8890c' : 'color:#fff'">Menu</span>
            </button>
            <button class="nav-item" @click="callWaiter()"
                :disabled="isCalling || (!statusDismissed && (callStatus === 'sent' || callStatus === 'accepted'))">
                <i class="fas fa-bell" :class="isCalling ? 'animate-bounce' : ''"
                    :style="(callStatus === 'accepted' && !statusDismissed) ? 'color:#2e7d32' : ''"></i>
                <span class="text-[9px] font-bold uppercase mt-0.5" style="color:#fff">Waiter</span>
            </button>
            <button class="nav-item relative" :class="activeTab === 'cart' ? 'active' : ''"
                @click="activeTab = 'cart'; window.location.hash = 'cart'; window.scrollTo({top: 0, behavior: 'smooth'})">
                <i class="fas fa-shopping-bag"></i>
                <span class="text-[9px] font-bold uppercase mt-0.5"
                    :style="activeTab === 'cart' ? 'color:#e8890c' : 'color:#fff'">Basket</span>
                <span
                    class="bg-[#e8890c] text-white text-[8px] font-black rounded-full flex items-center justify-center absolute -top-0.5 -right-0.5"
                    style="min-width:16px;height:16px;padding:0 3px;" x-show="cartCount > 0" x-text="cartCount"></span>
            </button>
            <button class="nav-item" :class="activeTab === 'order' ? 'active' : ''"
                @click="activeTab = 'order'; window.location.hash = 'order'; window.scrollTo({top: 0, behavior: 'smooth'})">
                <i class="fas fa-receipt"></i>
                <span class="text-[9px] font-bold uppercase mt-0.5"
                    :style="activeTab === 'order' ? 'color:#e8890c' : 'color:#fff'">Order</span>
                <span class="bg-red-500 text-white text-[8px] font-black rounded-full absolute -top-0.5 -right-0.5"
                    style="min-width:8px;height:8px;"
                    x-show="activeOrder && activeOrder.status !== 'served' && activeOrder.status !== 'cancelled'"></span>
            </button>
        </div>

        <!-- CART TAB (Full-page, no overlay) -->
        <div x-show="activeTab === 'cart'" x-transition:enter="transition ease-out duration-250"
            x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0"
            class="flex-1 bg-[#1c1c1c] flex flex-col" style="max-width:425px; display:none;">

            <!-- Cart Header -->
            <div
                class="flex items-center text-white justify-between px-5 py-4 border-b border-[#2a2a2a] bg-[#1c1c1c] sticky top-0 z-10">
                <button class="back-btn" @click="activeTab = 'restaurant'">
                    <i class="fas fa-play text-white fa-flip-horizontal"></i> Back
                </button>
                <span class="page-title text-white">Cart</span>
                <span class="text-xs text-[#e8890c] font-bold min-w-[60px] text-right"
                    x-text="cartCount + ' item' + (cartCount !== 1 ? 's' : '')"></span>
            </div>

            <!-- Scrollable Items Area -->
            <div class="flex-1 overflow-y-auto px-5 py-5 space-y-3" style="padding-bottom:16px;">
                <div class="my-order-title">My Order</div>

                <!-- Empty cart state -->
                <template x-if="cartCount === 0">
                    <div class="flex flex-col items-center justify-center py-16 text-center">
                        <div class="w-20 h-20 bg-[#252525] rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-shopping-basket text-3xl text-[#555]"></i>
                        </div>
                        <p class="text-white font-semibold text-sm mb-1">Your basket is empty</p>
                        <p class="text-[#888] text-xs mb-6">Add items from the menu to get started</p>
                        <button @click="activeTab = 'restaurant'"
                            class="bg-[#c0441a] text-white px-6 py-3 rounded-xl font-bold text-xs uppercase tracking-wider">Browse
                            Menu</button>
                    </div>
                </template>

                <!-- Cart Items -->
                <template x-for="(item, key) in cart" :key="key">
                    <div class="cart-item-card">
                        <div class="cart-item-img bg-[#2a2a2a]" x-text="getEmoji(item.name)"></div>
                        <div class="cart-item-info">
                            <div class="cart-item-name" x-text="item.name"></div>
                            <div class="cart-item-bottom">
                                <span class="cart-item-price" x-text="'Rs ' + item.price.toLocaleString()"></span>
                                <div class="qty-controls">
                                    <button class="qty-btn minus"
                                        @click="updateQuantity(key, item.quantity - 1)">−</button>
                                    <span class="qty-num" x-text="item.quantity"></span>
                                    <button class="qty-btn plus"
                                        @click="updateQuantity(key, item.quantity + 1)">+</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Special Instructions -->
                <template x-if="cartCount > 0">
                    <div class="pt-2">
                        <label class="text-xs text-[#ccc] text-white font-semibold mb-1.5 block">Special
                            instructions</label>
                        <textarea x-model="notes" placeholder="Any special cooking instructions (e.g. less spicy)?"
                            class="w-full bg-[#252525] text-white border border-[#3a3a3a] rounded-xl p-3 text-xs outline-none focus:border-[#e8890c]" rows="2"></textarea>
                    </div>
                </template>
            </div>

            <!-- Summary + Place Order (sticky bottom) -->
            <template x-if="cartCount > 0">
                <div class="bg-[#252525] border-t border-[#3a3a3a] px-5 py-4">
                    <!-- Promo Code -->
                    <div class="promo-wrap mb-4 bg-[#1c1c1c] border border-[#2a2a2a]">
                        <input class="promo-input" type="text" placeholder="Promo Code..." x-model="couponCode" />
                        <button class="promo-apply-btn bg-[#c0441a]" @click="applyCoupon()">Apply</button>
                    </div>

                    <!-- Applied Coupon badge -->
                    <template x-if="appliedDiscount">
                        <div class="flex justify-between items-center px-3 py-2 rounded-xl border border-emerald-900 mb-3 text-xs"
                            style="background:rgba(6,78,59,0.2);">
                            <span class="text-emerald-400 font-bold" x-text="'Coupon: ' + appliedDiscount.code"></span>
                            <button class="text-red-400 font-black uppercase" @click="removeCoupon()">Remove</button>
                        </div>
                    </template>

                    <!-- Totals -->
                    <div class="summary-wrap">
                        <div class="summary-row">
                            <span class="summary-label" style="color:#888;font-weight:400;font-size:12px;">Subtotal</span>
                            <span class="summary-value" style="font-size:12px;"
                                x-text="'Rs ' + cartTotal.toLocaleString()"></span>
                        </div>
                        <template x-if="appliedDiscount">
                            <div class="summary-row">
                                <span class="summary-label"
                                    style="color:#888;font-weight:400;font-size:12px;">Discount</span>
                                <span style="color:#4ade80;font-size:12px;font-weight:600;"
                                    x-text="'- Rs ' + discountAmount.toLocaleString()"></span>
                            </div>
                        </template>
                        <hr class="summary-divider" />
                        <div class="summary-row total-payment">
                            <span class="summary-label">Total Payment</span>
                            <span class="summary-value" style="color:#e8890c;"
                                x-text="'Rs ' + (cartTotal - discountAmount).toLocaleString()"></span>
                        </div>
                    </div>

                    <!-- Place Order Button -->
                    <button class="checkout-btn mt-3" style="background:#c0441a;" @click="
                        axios.post('{{ route('cart.save-notes') }}', { notes: notes }).then(() => {
                            window.location.href = '{{ route('payment.show', $code) }}';
                        });
                    " x-text="'Proceed to Payment'">
                    </button>
                </div>
            </template>
        </div>

        <!-- Toast -->
        <div class="toast-msg" id="toast" :class="toastShow ? 'show' : ''" x-text="toastMessage"></div>
    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Poppins:wght@300;400;500;600;700&display=swap');

        body {
            background: #111;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
            padding: 0;
            margin: 0;
        }

        .phone-shell {
            width: 100%;
            max-width: 425px;
            min-height: 100vh;
            background: #1c1c1c;
            position: relative;
            overflow-x: hidden;
            overflow-y: auto;
            padding-bottom: 96px;
            box-shadow: 0 0 40px rgba(0, 0, 0, 0.8);
            display: flex;
            flex-direction: column;
        }

        .phone-shell::-webkit-scrollbar {
            display: none;
        }

        /* Greeting & Subtitle */
        .greeting-name {
            font-family: 'Dancing Script', cursive;
            font-size: 22px;
            color: #fff;
            font-weight: 700;
            line-height: 1.2;
        }

        .greeting-sub {
            color: #aaa;
            font-size: 11px;
            font-weight: 300;
            margin-top: 1px;
        }

        /* Search */
        .search-wrap {
            padding: 4px 18px 12px;
            background: #1c1c1c;
        }

        .search-row {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .search-box {
            background: #2a2a2a;
            border-radius: 12px;
            display: flex;
            align-items: center;
            padding: 10px 14px;
            gap: 8px;
            border: 1px solid #333;
            flex: 1;
        }

        /* Theme Toggle Button */
        .theme-toggle-btn {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            border: 1px solid #333;
            background: #2a2a2a;
            color: #888;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            flex-shrink: 0;
            font-size: 16px;
        }

        .theme-toggle-btn:hover {
            border-color: #e8890c;
            color: #e8890c;
        }

        .theme-toggle-btn.active {
            background: #e8890c;
            border-color: #e8890c;
            color: #fff;
            box-shadow: 0 0 12px rgba(232, 137, 12, 0.4);
        }

        .theme-toggle-btn i {
            transition: transform 0.4s ease;
        }

        .theme-toggle-btn:active i {
            transform: rotate(180deg);
        }

        .search-box input {
            background: none;
            border: none;
            outline: none;
            color: #ccc;
            font-size: 13px;
            font-family: 'Poppins', sans-serif;
            width: 100%;
        }

        .search-box input::placeholder {
            color: #666;
        }

        .search-box i {
            color: #888;
            font-size: 14px;
        }

        /* Hero Banner */
        .hero-banner {
            margin: 0 18px 16px;
            background: linear-gradient(135deg, #2a2a2a 0%, #222 100%);
            border-radius: 16px;
            padding: 18px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            overflow: hidden;
            min-height: 110px;
        }

        .hero-banner::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255, 140, 0, 0.08) 0%, transparent 60%);
            border-radius: 16px;
        }

        .hero-badge {
            background: #e8890c;
            color: #fff;
            font-size: 10px;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .hero-title {
            font-family: 'Dancing Script', cursive;
            font-size: 26px;
            color: #fff;
            font-weight: 700;
            line-height: 1.1;
            margin-bottom: 6px;
        }

        .hero-meta {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .hero-meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
            color: #ccc;
            font-size: 11px;
        }

        .hero-meta-item i {
            color: #e8890c;
            font-size: 11px;
        }

        .hero-img {
            width: 110px;
            height: 90px;
            object-fit: cover;
            border-radius: 12px;
            flex-shrink: 0;
        }

        .hero-img-placeholder {
            width: 110px;
            height: 90px;
            border-radius: 12px;
            background: linear-gradient(135deg, #3a2a1a, #2a1a0a);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            flex-shrink: 0;
        }

        /* Category */
        .section-header {
            padding: 0 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .section-title {
            font-family: 'Dancing Script', cursive;
            font-size: 22px;
            color: #fff;
            font-weight: 700;
        }

        .category-scroll {
            padding: 0 18px 16px;
            display: flex;
            gap: 10px;
            overflow-x: auto;
            scrollbar-width: none;
        }

        .category-scroll::-webkit-scrollbar {
            display: none;
        }

        .cat-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 30px;
            border: none;
            font-size: 13px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.2s;
        }

        .cat-btn.active {
            background: #e8890c;
            color: #fff;
        }

        .cat-btn:not(.active) {
            background: #2a2a2a;
            color: #ccc;
        }

        .cat-btn .cat-icon {
            font-size: 15px;
        }

        /* Best Seller */
        .best-seller-title {
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 1.5px;
            margin-bottom: 14px;
            padding: 0 18px;
        }

        /* Cards Grid */
        .cards-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            padding: 0 18px 24px;
        }

        .food-card {
            background: #252525;
            border-radius: 14px;
            overflow: hidden;
            position: relative;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .food-card-img-wrap {
            position: relative;
            height: 120px;
            background: #2a2a2a;
        }

        .food-card-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .food-card-img-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
        }

        .heart-btn {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 28px;
            height: 28px;
            background: rgba(0, 0, 0, 0.5);
            border-radius: 50%;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 13px;
        }

        .heart-btn.liked i {
            color: #e8890c;
        }

        .heart-btn:not(.liked) {
            color: #888;
        }

        .food-card-body {
            padding: 10px 10px 8px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .food-card-name {
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            line-height: 1.3;
            margin-bottom: 4px;
        }

        .food-card-rating {
            display: flex;
            align-items: center;
            gap: 4px;
            margin-bottom: 6px;
        }

        .food-card-rating i {
            color: #f5a623;
            font-size: 11px;
        }

        .food-card-rating span {
            color: #ccc;
            font-size: 11px;
            font-weight: 500;
        }

        .food-card-price {
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            margin-left: 2px;
        }

        .food-card-meta {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #888;
            font-size: 10px;
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

        .nav-dot {
            width: 5px;
            height: 5px;
            background: #e8890c;
            border-radius: 50%;
        }

        /* Cart Items */
        .my-order-title {
            font-family: 'Dancing Script', cursive;
            font-size: 22px;
            color: #fff;
            font-weight: 700;
            margin-bottom: 14px;
        }

        .cart-item-card {
            background: #252525;
            border-radius: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            margin-bottom: 12px;
        }

        .cart-item-img {
            width: 72px;
            height: 72px;
            border-radius: 12px;
            flex-shrink: 0;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 34px;
            background: #2a2a2a;
        }

        .cart-item-info {
            flex: 1;
        }

        .cart-item-name {
            color: #fff;
            font-size: 13px;
            font-weight: 600;
            line-height: 1.35;
            margin-bottom: 8px;
        }

        .cart-item-bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .cart-item-price {
            color: #ccc;
            font-size: 13px;
            font-weight: 500;
        }

        .qty-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .qty-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 18px;
            font-weight: 700;
            line-height: 1;
            padding: 0;
            transition: transform 0.1s;
        }

        .qty-btn.minus {
            color: #e8890c;
        }

        .qty-btn.plus {
            color: #e8890c;
        }

        .qty-num {
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            min-width: 18px;
            text-align: center;
        }

        /* Promo Code */
        .promo-wrap {
            background: #252525;
            border-radius: 14px;
            display: flex;
            align-items: center;
            overflow: hidden;
            padding: 4px 4px 4px 16px;
        }

        .promo-input {
            flex: 1;
            background: none;
            border: none;
            outline: none;
            color: #aaa;
            font-size: 13px;
            font-family: 'Poppins', sans-serif;
            padding: 10px 0;
        }

        .promo-input::placeholder {
            color: #666;
        }

        .promo-apply-btn {
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 10px 22px;
            font-size: 13px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: background 0.2s;
        }

        /* Order Summary */
        .summary-wrap {
            margin-top: 14px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .summary-label {
            color: #fff;
            font-size: 14px;
            font-weight: 600;
        }

        .summary-value {
            color: #fff;
            font-size: 14px;
            font-weight: 500;
        }

        .summary-row.total-payment .summary-label {
            font-weight: 700;
            font-size: 15px;
        }

        .summary-row.total-payment .summary-value {
            font-weight: 700;
            font-size: 15px;
        }

        .summary-divider {
            border: none;
            border-top: 1px solid #3a3a3a;
            margin: 4px 0 14px;
        }

        /* Checkout Button */
        .checkout-btn {
            width: 100%;
            color: #fff;
            border: none;
            border-radius: 16px;
            padding: 16px;
            font-size: 15px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s;
            letter-spacing: 0.3px;
        }

        /* Toast */
        .toast-msg {
            position: fixed;
            bottom: 90px;
            left: 50%;
            transform: translateX(-50%) translateY(20px);
            background: #e8890c;
            color: #fff;
            padding: 10px 22px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            opacity: 0;
            transition: opacity 0.3s, transform 0.3s;
            z-index: 200;
            white-space: nowrap;
            pointer-events: none;
        }

        .toast-msg.show {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }

        /* Credit Card for Live order details */
        .credit-card {
            background: linear-gradient(135deg, #e8890c 0%, #c0641a 60%, #a0440a 100%);
            border-radius: 20px;
            padding: 24px 24px 22px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(232, 137, 12, 0.35);
        }

        .credit-card::before {
            content: '';
            position: absolute;
            top: -40px;
            right: -40px;
            width: 160px;
            height: 160px;
            background: rgba(255, 255, 255, 0.07);
            border-radius: 50%;
        }

        .card-label {
            color: rgba(255, 255, 255, 0.85);
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card-holder {
            color: rgba(255, 255, 255, 0.9);
            font-size: 13px;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        .card-chip {
            position: absolute;
            top: 22px;
            right: 24px;
            width: 36px;
            height: 28px;
            background: rgba(255, 255, 255, 0.25);
            border-radius: 6px;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .section-label {
            color: #ccc;
            font-size: 14px;
            font-weight: 500;
            margin: 22px 0 10px;
            letter-spacing: 0.2px;
        }

        /* ===== LIGHT THEME ===== */
        .phone-shell {
            background: #1c1c1c;
            color: #ccc;
            transition: background 0.4s ease, color 0.4s ease;
        }

        .light-theme.phone-shell {
            background: #f5f5f5 !important;
            color: #333 !important;
        }

        .light-theme .app-header {
            background: #f5f5f5 !important;
        }

        .light-theme .greeting-name {
            color: #1a1a1a;
        }

        .light-theme .greeting-sub {
            color: #666;
        }

        .light-theme .search-wrap {
            background: #f5f5f5;
        }

        .light-theme .search-box {
            background: #fff;
            border-color: #ddd;
        }

        .light-theme .search-box input {
            color: #333;
        }

        .light-theme .search-box input::placeholder {
            color: #999;
        }

        .light-theme .search-box i {
            color: #999;
        }

        .light-theme .theme-toggle-btn {
            background: #fff;
            border-color: #ddd;
            color: #666;
        }

        .light-theme .theme-toggle-btn:hover {
            border-color: #e8890c;
            color: #e8890c;
        }

        .light-theme .hero-banner {
            background: linear-gradient(135deg, #fff 0%, #f0ebe4 100%);
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }

        .light-theme .hero-banner::before {
            background: linear-gradient(135deg, rgba(232, 137, 12, 0.06) 0%, transparent 60%);
        }

        .light-theme .hero-title {
            color: #1a1a1a;
        }

        .light-theme .hero-meta-item {
            color: #555;
        }

        .light-theme .hero-img-placeholder {
            background: linear-gradient(135deg, #f0e6d6, #e8dcc8);
        }

        .light-theme .section-title {
            color: #1a1a1a;
        }

        .light-theme .cat-btn:not(.active) {
            background: #fff;
            color: #555;
            border: 1px solid #e0e0e0;
        }

        .light-theme .best-seller-title {
            color: #1a1a1a;
        }

        .light-theme .food-card {
            background: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
        }

        .light-theme .food-card-img-wrap {
            background: #f0f0f0;
        }

        .light-theme .food-card-name {
            color: #1a1a1a;
        }

        .light-theme .food-card-rating span {
            color: #555;
        }

        .light-theme .food-card-price {
            color: #1a1a1a;
        }

        .light-theme .food-card-meta {
            color: #888;
        }

        .light-theme .bottom-nav {
            background: #fff;
            border-top: 1px solid #e8e8e8;
        }

        .light-theme .nav-item i {
            color: #555;
        }

        .light-theme .nav-item.active i {
            color: #e8890c;
        }

        /* Light theme - Cart */
        .light-theme .my-order-title {
            color: #1a1a1a;
        }

        .light-theme .cart-item-card {
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }

        .light-theme .cart-item-img {
            background: #f0f0f0;
        }

        .light-theme .cart-item-name {
            color: #1a1a1a;
        }

        .light-theme .cart-item-price {
            color: #555;
        }

        .light-theme .qty-num {
            color: #1a1a1a;
        }

        .light-theme .promo-wrap {
            background: #fff !important;
            border-color: #ddd !important;
        }

        .light-theme .promo-input {
            color: #333;
        }

        .light-theme .promo-input::placeholder {
            color: #999;
        }

        .light-theme .summary-label {
            color: #1a1a1a;
        }

        .light-theme .summary-value {
            color: #1a1a1a;
        }

        .light-theme .summary-divider {
            border-top-color: #e0e0e0;
        }

        /* Light theme - Order Status */
        .light-theme .section-label {
            color: #555;
        }

        /* Light theme - various background overrides */
        .light-theme [class*="bg-[#1c1c1c]"] {
            background: #f5f5f5 !important;
        }

        .light-theme [class*="bg-[#252525]"] {
            background: #fff !important;
        }

        .light-theme [class*="border-[#2a2a2a]"],
        .light-theme [class*="border-[#3a3a3a]"] {
            border-color: #e0e0e0 !important;
        }

        .light-theme [class*="text-[#ccc]"] {
            color: #555 !important;
        }

        .light-theme [class*="text-[#888]"] {
            color: #888 !important;
        }

        .light-theme .text-white,
        .light-theme [class*="text-white"] {
            color: #1a1a1a !important;
        }

        /* Keep accent-colored text white */
        .light-theme .cat-btn.active,
        .light-theme .checkout-btn,
        .light-theme .promo-apply-btn,
        .light-theme .hero-badge,
        .light-theme .cart-badge,
        .light-theme .credit-card .text-white,
        .light-theme .credit-card [class*="text-white"],
        .light-theme .toast-msg,
        .light-theme button[class*="bg-[#c0441a]"] {
            color: #fff !important;
        }

        .light-theme textarea {
            background: #f5f5f5 !important;
            color: #333 !important;
            border-color: #ddd !important;
        }

        .light-theme .back-btn {
            color: #333 !important;
        }

        .light-theme .back-btn i {
            color: #333 !important;
        }

        .light-theme .page-title {
            color: #1a1a1a !important;
        }
    </style>


@endsection

@section('scripts')
    <script>
        function customerSPA() {
            return {
                darkMode: true,
                activeTab: 'restaurant',
                activeCategory: 'all',
                cart: {},
                isCartOpen: false,
                notes: '',
                isPlacing: false,
                isCalling: false,
                callStatus: 'idle',
                statusDismissed: false,
                couponCode: '',
                appliedDiscount: null,
                searchQuery: '',
                categories: {!! json_encode(
                    $categories->map(function ($category) {
                        return ['id' => $category->id, 'name' => $category->name];
                    }),
                ) !!},
                allItems: [],
                likedItems: [],
                activeOrder: {!! $activeOrder ? json_encode($activeOrder) : 'null' !!},
                guestOrders: [],
                countdown: {
                    minutes: '00',
                    seconds: '00',
                    finished: false
                },
                toastShow: false,
                toastMessage: '',

                get cartCount() {
                    return Object.values(this.cart).reduce((sum, item) => sum + item.quantity, 0);
                },
                get cartTotal() {
                    return Object.values(this.cart).reduce((sum, item) => sum + (item.price * item.quantity), 0);
                },
                get discountAmount() {
                    if (!this.appliedDiscount) return 0;
                    return this.appliedDiscount.type === 'percentage' ? this.cartTotal * (this.appliedDiscount.value /
                        100) : Math.min(this.appliedDiscount.value, this.cartTotal);
                },

                get filteredItems() {
                    let items = this.allItems;
                    if (this.activeCategory !== 'all') {
                        items = items.filter(item => item.category_id == this.activeCategory);
                    }
                    if (this.searchQuery.trim().length > 0) {
                        const q = this.searchQuery.toLowerCase();
                        items = items.filter(item => item.name.toLowerCase().includes(q) || (item.description && item
                            .description.toLowerCase().includes(q)));
                    }
                    return items;
                },

                init() {
                    // Load theme preference
                    const savedTheme = localStorage.getItem('customer_theme');
                    this.darkMode = savedTheme !== 'light';

                    this.cart = {!! json_encode(session('cart', [])) !!};
                    if (Array.isArray(this.cart) && this.cart.length === 0) {
                        this.cart = {};
                    }
                    this.appliedDiscount = {!! json_encode(session('discount')) !!};

                    // Reconstruct all items with category names for emoji matching
                    const categories = {!! json_encode($categories) !!};
                    categories.forEach(cat => {
                        cat.menu_items.forEach(item => {
                            this.allItems.push({
                                ...item,
                                category_id: cat.id,
                                category_name: cat.name
                            });
                        });
                    });

                    this.fetchGuestOrders();

                    // Initialize active order timers
                    if (this.activeOrder) {
                        this.updateCountdown();
                        setInterval(() => this.updateCountdown(), 1000);
                    }
                    setInterval(() => this.fetchGuestOrders(), 5000);
                    
                    setInterval(() => this.pollCallStatus(), 3000);
                    this.pollCallStatus();

                    // Load likes from localStorage
                    try {
                        this.likedItems = JSON.parse(localStorage.getItem('liked_items') || '[]');
                    } catch (e) {}

                    // Check URL hash to switch active tab
                    const handleHash = () => {
                        if (window.location.hash === '#cart') {
                            this.activeTab = 'cart';
                        } else if (window.location.hash === '#order') {
                            this.activeTab = 'order';
                        } else if (window.location.hash === '#restaurant') {
                            this.activeTab = 'restaurant';
                        }
                    };
                    handleHash();
                    window.addEventListener('hashchange', handleHash);
                },

                playSound() {
                    const audio = new Audio('/audio/notification.mp3');
                    audio.play().catch(e => console.log('Audio playback prevented by browser'));
                },

                showToast(msg) {
                    this.toastMessage = msg;
                    this.toastShow = true;
                    this.playSound();
                    setTimeout(() => this.toastShow = false, 2500);
                },

                toggleTheme() {
                    this.darkMode = !this.darkMode;
                    localStorage.setItem('customer_theme', this.darkMode ? 'dark' : 'light');
                    this.showToast(this.darkMode ? '🌙 Dark mode activated' : '☀️ Light mode activated');
                },

                toggleLike(id) {
                    if (this.likedItems.includes(id)) {
                        this.likedItems = this.likedItems.filter(item => item !== id);
                    } else {
                        this.likedItems.push(id);
                        this.showToast('❤️ Added to favorites!');
                    }
                    localStorage.setItem('liked_items', JSON.stringify(this.likedItems));
                },

                pollCallStatus() {
                    axios.get('{{ route('table.call.status') }}')
                        .then(response => {
                            const oldCallStatus = this.callStatus;
                            if (this.callStatus !== response.data.status) {
                                this.callStatus = response.data.status;
                                this.statusDismissed = false;

                                // Notification for Waiter Call Acceptance
                                if (oldCallStatus !== 'accepted' && this.callStatus === 'accepted') {
                                    this.showToast('🏃 Waiter is coming to your table!');
                                }
                            }
                        });
                },

                dismissCallStatus() {
                    this.statusDismissed = true;
                },

                callWaiter() {
                    if (this.isCalling) return;
                    this.isCalling = true;
                    axios.post('{{ route('table.call') }}')
                        .then(response => {
                            this.callStatus = 'sent';
                            this.statusDismissed = false;
                            this.isCalling = false;
                            this.showToast('🔔 Waiter has been requested!');
                        })
                        .catch(error => {
                            this.showToast(error.response.data.error || 'Something went wrong.');
                            this.isCalling = false;
                        });
                },

                addToCart(id, name, price) {
                    axios.post('{{ route('cart.add') }}', {
                        menu_item_id: id,
                        quantity: 1
                    }).then(response => {
                        let key = id + '_d41d8cd98f00b204e9800998ecf8427e';
                        const newCart = {
                            ...this.cart
                        };
                        if (newCart[key]) {
                            newCart[key] = {
                                ...newCart[key],
                                quantity: newCart[key].quantity + 1
                            };
                        } else {
                            newCart[key] = {
                                id,
                                name,
                                price,
                                quantity: 1
                            };
                        }
                        this.cart = newCart;
                        this.showToast('🛒 Added ' + name + ' to basket!');
                    });
                },

                updateQuantity(key, newQuantity) {
                    if (newQuantity <= 0) {
                        axios.post('{{ route('cart.remove') }}', {
                            cart_id: key
                        }).then(() => {
                            const newCart = {
                                ...this.cart
                            };
                            delete newCart[key];
                            this.cart = newCart;
                            if (Object.keys(this.cart).length === 0) this.isCartOpen = false;
                            this.showToast('🗑️ Item removed.');
                        });
                    } else {
                        axios.post('{{ route('cart.update') }}', {
                            cart_id: key,
                            quantity: newQuantity
                        }).then(() => {
                            const newCart = {
                                ...this.cart
                            };
                            newCart[key] = {
                                ...newCart[key],
                                quantity: newQuantity
                            };
                            this.cart = newCart;
                        });
                    }
                },

                applyCoupon() {
                    if (!this.couponCode) return;
                    axios.post('{{ route('cart.discount.apply') }}', {
                        code: this.couponCode
                    }).then(response => {
                        this.appliedDiscount = response.data.discount;
                        this.couponCode = '';
                        this.showToast('🎉 Coupon applied successfully!');
                    }).catch(error => this.showToast(error.response.data.error || 'Invalid Coupon'));
                },

                removeCoupon() {
                    axios.post('{{ route('cart.discount.remove') }}').then(() => {
                        this.appliedDiscount = null;
                        this.showToast('🗑️ Coupon removed.');
                    });
                },

                placeOrder() {
                    if (Object.keys(this.cart).length === 0) return;
                    this.isPlacing = true;
                    axios.post('{{ route('order.place') }}', {
                        notes: this.notes
                    }).then(response => {
                        this.showToast('✅ Order placed successfully!');
                        setTimeout(() => location.reload(), 1000);
                    }).catch(error => {
                        this.showToast(error.response.data.error || 'Something went wrong.');
                        this.isPlacing = false;
                    });
                },

                fetchGuestOrders() {
                    axios.get('{{ route('guest.active-orders') }}')
                        .then(response => {
                            const newOrders = response.data;
                            
                            // Check for status changes to trigger toasts
                            newOrders.forEach(newOrder => {
                                const oldOrder = this.guestOrders.find(o => o.id === newOrder.id);
                                if (oldOrder) {
                                    if (oldOrder.status !== newOrder.status) {
                                        const statusMessages = {
                                            'preparing': '🍳 Order #' + newOrder.order_number + ' is now being prepared!',
                                            'ready': '🛎️ Order #' + newOrder.order_number + ' is ready to serve!',
                                            'served': '✅ Order #' + newOrder.order_number + ' has been served. Enjoy!',
                                            'cancelled': '❌ Order #' + newOrder.order_number + ' was cancelled.'
                                        };
                                        if (statusMessages[newOrder.status]) {
                                            this.showToast(statusMessages[newOrder.status]);
                                        }
                                    }
                                    if (oldOrder.payment_status !== newOrder.payment_status && newOrder.payment_status === 'paid') {
                                        this.showToast('💳 Payment received for Order #' + newOrder.order_number);
                                    }
                                }
                            });

                            this.guestOrders = newOrders;
                            if (this.guestOrders.length > 0) {
                                this.activeOrder = this.guestOrders[0];
                                this.updateCountdown();
                            } else {
                                this.activeOrder = null;
                            }
                        })
                        .catch(err => console.error(err));
                },

                updateCountdown() {
                    if (!this.activeOrder || !this.activeOrder.estimated_completion_time) return;
                    const target = new Date(this.activeOrder.estimated_completion_time).getTime();
                    const now = new Date().getTime();
                    const distance = target - now;
                    if (distance < 0) {
                        this.countdown.minutes = '00';
                        this.countdown.seconds = '00';
                        this.countdown.finished = true;
                        return;
                    }
                    const mins = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const secs = Math.floor((distance % (1000 * 60)) / 1000);
                    this.countdown.minutes = mins < 10 ? '0' + mins : mins;
                    this.countdown.seconds = secs < 10 ? '0' + secs : secs;
                    this.countdown.finished = false;
                },

                getEmoji(name, categoryName) {
                    name = (name || '').toLowerCase();
                    categoryName = (categoryName || '').toLowerCase();
                    if (name.includes('pizza')) return '🍕';
                    if (name.includes('burger') || name.includes('bun')) return '🍔';
                    if (name.includes('steak') || name.includes('meat') || name.includes('beef') || name.includes(
                            'mutton') || name.includes('kebab') || name.includes('tikka')) return '🥩';
                    if (name.includes('chicken') || name.includes('wing') || name.includes('nugget')) return '🍗';
                    if (name.includes('rice') || name.includes('biryani') || name.includes('platter') || name.includes(
                            'curry')) return '🍛';
                    if (name.includes('fries') || name.includes('snack') || name.includes('chips') || name.includes('roll'))
                        return '🍟';
                    if (name.includes('salad')) return '🥗';
                    if (name.includes('soup') || name.includes('noodle') || name.includes('ramen')) return '🍜';
                    if (name.includes('dessert') || name.includes('sweet') || name.includes('ice cream') || name.includes(
                            'cake') || name.includes('kulfi') || name.includes('kheer') || name.includes('bhallay'))
                        return '🍨';
                    if (name.includes('tea') || name.includes('coffee') || name.includes('latte')) return '☕';
                    if (name.includes('drink') || name.includes('juice') || name.includes('soda') || name.includes(
                            'cola') || name.includes('water')) return '🥤';

                    // Category fallbacks
                    if (categoryName.includes('starter') || categoryName.includes('appetizer')) return '🍿';
                    if (categoryName.includes('main')) return '🍛';
                    if (categoryName.includes('dessert') || categoryName.includes('sweet')) return '🍮';
                    if (categoryName.includes('drink') || categoryName.includes('beverage')) return '🧃';
                    return '🍽️';
                }
            }
        }

        function bannerSlider(count) {
            return {
                currentIndex: 0,
                count: count,
                interval: null,
                startX: 0,
                init() {
                    if (this.count > 1) {
                        this.startAutoPlay();
                    }
                },
                startAutoPlay() {
                    this.interval = setInterval(() => {
                        this.next();
                    }, 4000);
                },
                pause() {
                    clearInterval(this.interval);
                },
                resume() {
                    if (this.count > 1) {
                        this.startAutoPlay();
                    }
                },
                next() {
                    this.currentIndex = (this.currentIndex + 1) % this.count;
                },
                prev() {
                    this.currentIndex = (this.currentIndex - 1 + this.count) % this.count;
                },
                goTo(index) {
                    this.currentIndex = index;
                    this.pause();
                    this.resume();
                },
                touchStart(e) {
                    this.startX = e.touches[0].clientX;
                    this.pause();
                },
                touchMove(e) {
                    // prevent default if we want pure swipe, but scrolling might be needed
                },
                touchEnd(e) {
                    if (!e.changedTouches || e.changedTouches.length === 0) return;
                    let endX = e.changedTouches[0].clientX;
                    let diff = this.startX - endX;

                    if (Math.abs(diff) > 50) { // threshold
                        if (diff > 0) {
                            this.next();
                        } else {
                            this.prev();
                        }
                    }
                    this.resume();
                }
            }
        }
    </script>
@endsection
