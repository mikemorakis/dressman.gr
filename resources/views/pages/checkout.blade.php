<x-layouts.app>
    <x-slot:seo>
        <x-seo-meta
            title="Checkout — PeShop"
            description="Complete your order."
            :noindex="true"
        />
    </x-slot:seo>

    <x-slot:breadcrumb>
        <x-breadcrumb :items="[
            ['label' => 'Home', 'url' => url('/')],
            ['label' => 'Cart', 'url' => route('cart')],
            ['label' => 'Checkout'],
        ]" />
    </x-slot:breadcrumb>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl font-bold text-gray-900">Checkout</h1>

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="mt-4 rounded-md bg-green-50 p-4" role="status">
                <div class="flex">
                    <svg class="h-5 w-5 text-green-400 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                    </svg>
                    <p class="ml-3 text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mt-4 rounded-md bg-red-50 p-4" role="alert">
                <div class="flex">
                    <svg class="h-5 w-5 text-red-400 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.28 7.22a.75.75 0 0 0-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 1 0 1.06 1.06L10 11.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L11.06 10l1.72-1.72a.75.75 0 0 0-1.06-1.06L10 8.94 8.28 7.22Z" clip-rule="evenodd" />
                    </svg>
                    <p class="ml-3 text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <div class="mt-8 lg:grid lg:grid-cols-12 lg:gap-x-8">
            {{-- Checkout form --}}
            <section class="lg:col-span-8" aria-label="Checkout form">
                <form
                    method="POST"
                    action="{{ route('checkout.store') }}"
                    novalidate
                    x-data="{ billingSame: {{ old('billing_same_as_shipping', $checkout['billing_same_as_shipping'] ?? true) ? 'true' : 'false' }} }"
                >
                    @csrf

                    {{-- ── Contact Information ── --}}
                    <fieldset>
                        <legend class="text-lg font-semibold text-gray-900">Contact Information</legend>

                        <div class="mt-4">
                            <label for="email" class="block text-sm font-medium text-gray-700">
                                Email address <span class="text-red-500" aria-hidden="true">*</span>
                            </label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                autocomplete="email"
                                inputmode="email"
                                required
                                value="{{ old('email', $checkout['email'] ?? '') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('email') border-red-300 text-red-900 @enderror"
                                @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                            >
                            @error('email')
                                <p class="mt-1 text-sm text-red-600" id="email-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </fieldset>

                    {{-- ── Shipping Address ── --}}
                    <fieldset class="mt-8">
                        <legend class="text-lg font-semibold text-gray-900">Shipping Address</legend>

                        <div class="mt-4 grid grid-cols-1 gap-y-4 sm:grid-cols-2 sm:gap-x-4">
                            {{-- First name --}}
                            <div>
                                <label for="shipping_first_name" class="block text-sm font-medium text-gray-700">
                                    First name <span class="text-red-500" aria-hidden="true">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="shipping_first_name"
                                    name="shipping_first_name"
                                    autocomplete="shipping given-name"
                                    required
                                    value="{{ old('shipping_first_name', $checkout['shipping_first_name'] ?? '') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('shipping_first_name') border-red-300 text-red-900 @enderror"
                                    @error('shipping_first_name') aria-invalid="true" aria-describedby="shipping-first-name-error" @enderror
                                >
                                @error('shipping_first_name')
                                    <p class="mt-1 text-sm text-red-600" id="shipping-first-name-error">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Last name --}}
                            <div>
                                <label for="shipping_last_name" class="block text-sm font-medium text-gray-700">
                                    Last name <span class="text-red-500" aria-hidden="true">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="shipping_last_name"
                                    name="shipping_last_name"
                                    autocomplete="shipping family-name"
                                    required
                                    value="{{ old('shipping_last_name', $checkout['shipping_last_name'] ?? '') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('shipping_last_name') border-red-300 text-red-900 @enderror"
                                    @error('shipping_last_name') aria-invalid="true" aria-describedby="shipping-last-name-error" @enderror
                                >
                                @error('shipping_last_name')
                                    <p class="mt-1 text-sm text-red-600" id="shipping-last-name-error">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Street address --}}
                            <div class="sm:col-span-2">
                                <label for="shipping_address" class="block text-sm font-medium text-gray-700">
                                    Street address <span class="text-red-500" aria-hidden="true">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="shipping_address"
                                    name="shipping_address"
                                    autocomplete="shipping address-line1"
                                    required
                                    value="{{ old('shipping_address', $checkout['shipping_address'] ?? '') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('shipping_address') border-red-300 text-red-900 @enderror"
                                    @error('shipping_address') aria-invalid="true" aria-describedby="shipping-address-error" @enderror
                                >
                                @error('shipping_address')
                                    <p class="mt-1 text-sm text-red-600" id="shipping-address-error">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Address line 2 --}}
                            <div class="sm:col-span-2">
                                <label for="shipping_address2" class="block text-sm font-medium text-gray-700">
                                    Apartment, suite, etc. <span class="text-gray-400">(optional)</span>
                                </label>
                                <input
                                    type="text"
                                    id="shipping_address2"
                                    name="shipping_address2"
                                    autocomplete="shipping address-line2"
                                    value="{{ old('shipping_address2', $checkout['shipping_address2'] ?? '') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                >
                            </div>

                            {{-- City --}}
                            <div>
                                <label for="shipping_city" class="block text-sm font-medium text-gray-700">
                                    City <span class="text-red-500" aria-hidden="true">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="shipping_city"
                                    name="shipping_city"
                                    autocomplete="shipping address-level2"
                                    required
                                    value="{{ old('shipping_city', $checkout['shipping_city'] ?? '') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('shipping_city') border-red-300 text-red-900 @enderror"
                                    @error('shipping_city') aria-invalid="true" aria-describedby="shipping-city-error" @enderror
                                >
                                @error('shipping_city')
                                    <p class="mt-1 text-sm text-red-600" id="shipping-city-error">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- State / Region --}}
                            <div>
                                <label for="shipping_state" class="block text-sm font-medium text-gray-700">
                                    State / Region <span class="text-gray-400">(optional)</span>
                                </label>
                                <input
                                    type="text"
                                    id="shipping_state"
                                    name="shipping_state"
                                    autocomplete="shipping address-level1"
                                    value="{{ old('shipping_state', $checkout['shipping_state'] ?? '') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                >
                            </div>

                            {{-- Postal code --}}
                            <div>
                                <label for="shipping_postal_code" class="block text-sm font-medium text-gray-700">
                                    Postal code <span class="text-red-500" aria-hidden="true">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="shipping_postal_code"
                                    name="shipping_postal_code"
                                    autocomplete="shipping postal-code"
                                    required
                                    value="{{ old('shipping_postal_code', $checkout['shipping_postal_code'] ?? '') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('shipping_postal_code') border-red-300 text-red-900 @enderror"
                                    @error('shipping_postal_code') aria-invalid="true" aria-describedby="shipping-postal-code-error" @enderror
                                >
                                @error('shipping_postal_code')
                                    <p class="mt-1 text-sm text-red-600" id="shipping-postal-code-error">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Country --}}
                            <div>
                                <label for="shipping_country" class="block text-sm font-medium text-gray-700">
                                    Country <span class="text-red-500" aria-hidden="true">*</span>
                                </label>
                                <select
                                    id="shipping_country"
                                    name="shipping_country"
                                    autocomplete="shipping country"
                                    required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('shipping_country') border-red-300 text-red-900 @enderror"
                                    @error('shipping_country') aria-invalid="true" aria-describedby="shipping-country-error" @enderror
                                >
                                    <option value="GR" @selected(old('shipping_country', $checkout['shipping_country'] ?? 'GR') === 'GR')>Greece</option>
                                    <option value="CY" @selected(old('shipping_country', $checkout['shipping_country'] ?? 'GR') === 'CY')>Cyprus</option>
                                </select>
                                @error('shipping_country')
                                    <p class="mt-1 text-sm text-red-600" id="shipping-country-error">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Phone --}}
                            <div class="sm:col-span-2">
                                <label for="shipping_phone" class="block text-sm font-medium text-gray-700">
                                    Phone <span class="text-gray-400">(optional)</span>
                                </label>
                                <input
                                    type="tel"
                                    id="shipping_phone"
                                    name="shipping_phone"
                                    autocomplete="shipping tel"
                                    inputmode="tel"
                                    value="{{ old('shipping_phone', $checkout['shipping_phone'] ?? '') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                >
                            </div>
                        </div>
                    </fieldset>

                    {{-- ── Billing Address ── --}}
                    <fieldset class="mt-8">
                        <legend class="text-lg font-semibold text-gray-900">Billing Address</legend>

                        <div class="mt-4">
                            <label class="flex items-center gap-x-3 cursor-pointer">
                                <input
                                    type="checkbox"
                                    name="billing_same_as_shipping"
                                    value="1"
                                    x-model="billingSame"
                                    class="h-5 w-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                                >
                                <span class="text-sm text-gray-700">Same as shipping address</span>
                            </label>
                        </div>

                        <div x-show="!billingSame" x-cloak class="mt-4 grid grid-cols-1 gap-y-4 sm:grid-cols-2 sm:gap-x-4">
                            {{-- Billing first name --}}
                            <div>
                                <label for="billing_first_name" class="block text-sm font-medium text-gray-700">
                                    First name <span class="text-red-500" aria-hidden="true">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="billing_first_name"
                                    name="billing_first_name"
                                    autocomplete="billing given-name"
                                    value="{{ old('billing_first_name', $checkout['billing_first_name'] ?? '') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('billing_first_name') border-red-300 text-red-900 @enderror"
                                    @error('billing_first_name') aria-invalid="true" aria-describedby="billing-first-name-error" @enderror
                                >
                                @error('billing_first_name')
                                    <p class="mt-1 text-sm text-red-600" id="billing-first-name-error">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Billing last name --}}
                            <div>
                                <label for="billing_last_name" class="block text-sm font-medium text-gray-700">
                                    Last name <span class="text-red-500" aria-hidden="true">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="billing_last_name"
                                    name="billing_last_name"
                                    autocomplete="billing family-name"
                                    value="{{ old('billing_last_name', $checkout['billing_last_name'] ?? '') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('billing_last_name') border-red-300 text-red-900 @enderror"
                                    @error('billing_last_name') aria-invalid="true" aria-describedby="billing-last-name-error" @enderror
                                >
                                @error('billing_last_name')
                                    <p class="mt-1 text-sm text-red-600" id="billing-last-name-error">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Billing street address --}}
                            <div class="sm:col-span-2">
                                <label for="billing_address" class="block text-sm font-medium text-gray-700">
                                    Street address <span class="text-red-500" aria-hidden="true">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="billing_address"
                                    name="billing_address"
                                    autocomplete="billing address-line1"
                                    value="{{ old('billing_address', $checkout['billing_address'] ?? '') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('billing_address') border-red-300 text-red-900 @enderror"
                                    @error('billing_address') aria-invalid="true" aria-describedby="billing-address-error" @enderror
                                >
                                @error('billing_address')
                                    <p class="mt-1 text-sm text-red-600" id="billing-address-error">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Billing address line 2 --}}
                            <div class="sm:col-span-2">
                                <label for="billing_address2" class="block text-sm font-medium text-gray-700">
                                    Apartment, suite, etc. <span class="text-gray-400">(optional)</span>
                                </label>
                                <input
                                    type="text"
                                    id="billing_address2"
                                    name="billing_address2"
                                    autocomplete="billing address-line2"
                                    value="{{ old('billing_address2', $checkout['billing_address2'] ?? '') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                >
                            </div>

                            {{-- Billing city --}}
                            <div>
                                <label for="billing_city" class="block text-sm font-medium text-gray-700">
                                    City <span class="text-red-500" aria-hidden="true">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="billing_city"
                                    name="billing_city"
                                    autocomplete="billing address-level2"
                                    value="{{ old('billing_city', $checkout['billing_city'] ?? '') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('billing_city') border-red-300 text-red-900 @enderror"
                                    @error('billing_city') aria-invalid="true" aria-describedby="billing-city-error" @enderror
                                >
                                @error('billing_city')
                                    <p class="mt-1 text-sm text-red-600" id="billing-city-error">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Billing state --}}
                            <div>
                                <label for="billing_state" class="block text-sm font-medium text-gray-700">
                                    State / Region <span class="text-gray-400">(optional)</span>
                                </label>
                                <input
                                    type="text"
                                    id="billing_state"
                                    name="billing_state"
                                    autocomplete="billing address-level1"
                                    value="{{ old('billing_state', $checkout['billing_state'] ?? '') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                >
                            </div>

                            {{-- Billing postal code --}}
                            <div>
                                <label for="billing_postal_code" class="block text-sm font-medium text-gray-700">
                                    Postal code <span class="text-red-500" aria-hidden="true">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="billing_postal_code"
                                    name="billing_postal_code"
                                    autocomplete="billing postal-code"
                                    value="{{ old('billing_postal_code', $checkout['billing_postal_code'] ?? '') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('billing_postal_code') border-red-300 text-red-900 @enderror"
                                    @error('billing_postal_code') aria-invalid="true" aria-describedby="billing-postal-code-error" @enderror
                                >
                                @error('billing_postal_code')
                                    <p class="mt-1 text-sm text-red-600" id="billing-postal-code-error">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Billing country --}}
                            <div>
                                <label for="billing_country" class="block text-sm font-medium text-gray-700">
                                    Country <span class="text-red-500" aria-hidden="true">*</span>
                                </label>
                                <select
                                    id="billing_country"
                                    name="billing_country"
                                    autocomplete="billing country"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('billing_country') border-red-300 text-red-900 @enderror"
                                    @error('billing_country') aria-invalid="true" aria-describedby="billing-country-error" @enderror
                                >
                                    <option value="GR" @selected(old('billing_country', $checkout['billing_country'] ?? 'GR') === 'GR')>Greece</option>
                                    <option value="CY" @selected(old('billing_country', $checkout['billing_country'] ?? 'GR') === 'CY')>Cyprus</option>
                                </select>
                                @error('billing_country')
                                    <p class="mt-1 text-sm text-red-600" id="billing-country-error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </fieldset>

                    {{-- ── Payment Method ── --}}
                    <fieldset class="mt-8">
                        <legend class="text-lg font-semibold text-gray-900">Payment Method</legend>
                        @error('payment_method')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        @php $selectedMethod = old('payment_method', $checkout['payment_method'] ?? 'stripe'); @endphp

                        <div class="mt-4 space-y-3">
                            {{-- Stripe / Card --}}
                            <label class="flex items-start gap-3 p-4 border rounded-lg cursor-pointer transition-colors hover:bg-gray-50 @if($selectedMethod === 'stripe') border-primary-500 bg-primary-50 @else border-gray-200 @endif">
                                <input
                                    type="radio"
                                    name="payment_method"
                                    value="stripe"
                                    @checked($selectedMethod === 'stripe')
                                    class="mt-0.5 h-4 w-4 border-gray-300 text-primary-600 focus:ring-primary-500"
                                >
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Credit / Debit Card</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Secure payment via Stripe. Visa, Mastercard, American Express.</p>
                                </div>
                            </label>

                            {{-- Bank Transfer --}}
                            <label class="flex items-start gap-3 p-4 border rounded-lg cursor-pointer transition-colors hover:bg-gray-50 @if($selectedMethod === 'bank_transfer') border-primary-500 bg-primary-50 @else border-gray-200 @endif">
                                <input
                                    type="radio"
                                    name="payment_method"
                                    value="bank_transfer"
                                    @checked($selectedMethod === 'bank_transfer')
                                    class="mt-0.5 h-4 w-4 border-gray-300 text-primary-600 focus:ring-primary-500"
                                >
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Bank Transfer</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Pay directly via bank transfer. You will receive the bank details after placing your order.</p>
                                </div>
                            </label>

                            {{-- Store Pickup --}}
                            <label class="flex items-start gap-3 p-4 border rounded-lg cursor-pointer transition-colors hover:bg-gray-50 @if($selectedMethod === 'store_pickup') border-primary-500 bg-primary-50 @else border-gray-200 @endif">
                                <input
                                    type="radio"
                                    name="payment_method"
                                    value="store_pickup"
                                    @checked($selectedMethod === 'store_pickup')
                                    class="mt-0.5 h-4 w-4 border-gray-300 text-primary-600 focus:ring-primary-500"
                                >
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Pay at Store (Pickup)</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Pick up and pay at one of our stores in Athens.</p>
                                </div>
                            </label>
                        </div>
                    </fieldset>

                    {{-- ── Order Notes ── --}}
                    <fieldset class="mt-8">
                        <legend class="text-lg font-semibold text-gray-900">Additional Information</legend>

                        <div class="mt-4">
                            <label for="notes" class="block text-sm font-medium text-gray-700">
                                Order notes <span class="text-gray-400">(optional)</span>
                            </label>
                            <textarea
                                id="notes"
                                name="notes"
                                rows="3"
                                maxlength="500"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('notes') border-red-300 text-red-900 @enderror"
                                @error('notes') aria-invalid="true" aria-describedby="notes-error" @enderror
                            >{{ old('notes', $checkout['notes'] ?? '') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600" id="notes-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </fieldset>

                    {{-- Submit --}}
                    <div class="mt-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <a href="{{ route('cart') }}" class="text-sm font-medium text-primary-600 hover:text-primary-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 rounded-sm">
                            &larr; Return to cart
                        </a>
                        <button type="submit" class="btn-primary w-full sm:w-auto">
                            Save Information
                        </button>
                    </div>
                </form>

                {{-- Proceed to Payment --}}
                @if(!empty($checkout))
                    <div class="mt-6">
                        <form method="POST" action="{{ route('checkout.pay') }}">
                            @csrf
                            <button type="submit" class="btn-primary w-full sm:w-auto">
                                @if(($checkout['payment_method'] ?? 'stripe') === 'stripe')
                                    Pay with Card
                                @elseif(($checkout['payment_method'] ?? '') === 'bank_transfer')
                                    Place Order (Bank Transfer)
                                @else
                                    Place Order (Store Pickup)
                                @endif
                            </button>
                        </form>
                    </div>
                @endif
            </section>

            {{-- Order summary sidebar --}}
            <section class="mt-8 lg:mt-0 lg:col-span-4" aria-label="Order summary">
                <div class="bg-gray-50 rounded-lg p-6 sticky top-24">
                    <h2 class="text-lg font-semibold text-gray-900">Order Summary</h2>

                    {{-- Item list --}}
                    <ul role="list" class="mt-4 divide-y divide-gray-200">
                        @foreach($items as $item)
                            <li class="py-3 flex gap-3">
                                <div class="h-16 w-16 flex-shrink-0 rounded-md overflow-hidden bg-gray-100">
                                    @php $image = $item->product->images->first(); @endphp
                                    @if($image)
                                        <img src="{{ asset('storage/' . $image->path_thumb) }}" alt="{{ $image->alt_text }}" class="h-full w-full object-cover" loading="lazy">
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $item->product->name }}</p>
                                    @if($item->variant)
                                        <p class="text-xs text-gray-500">{{ $item->variant->sku }}</p>
                                    @endif
                                    <p class="text-xs text-gray-500">Qty: {{ $item->quantity }}</p>
                                </div>
                                <p class="text-sm font-medium text-gray-900 flex-shrink-0">{{ format_price($item->line_total) }}</p>
                            </li>
                        @endforeach
                    </ul>

                    {{-- Totals --}}
                    <dl class="mt-4 space-y-3 border-t border-gray-200 pt-4">
                        <div class="flex justify-between text-sm">
                            <dt class="text-gray-600">Subtotal ({{ $totals['count'] }} {{ Str::plural('item', $totals['count']) }})</dt>
                            <dd class="font-medium text-gray-900">{{ format_price($totals['subtotal']) }}</dd>
                        </div>

                        @if($totals['prices_include_vat'])
                            <div class="flex justify-between text-sm">
                                <dt class="text-gray-500">Includes VAT ({{ number_format($totals['vat_rate'], 0) }}%)</dt>
                                <dd class="text-gray-500">{{ format_price($totals['vat_amount']) }}</dd>
                            </div>
                        @else
                            <div class="flex justify-between text-sm">
                                <dt class="text-gray-600">VAT ({{ number_format($totals['vat_rate'], 0) }}%)</dt>
                                <dd class="font-medium text-gray-900">{{ format_price($totals['vat_amount']) }}</dd>
                            </div>
                        @endif

                        <div class="flex justify-between text-sm">
                            <dt class="text-gray-600">{{ $totals['shipping_label'] ?? 'Shipping' }}</dt>
                            <dd class="font-medium text-gray-900">
                                @if($totals['shipping'] > 0)
                                    {{ format_price($totals['shipping']) }}
                                @else
                                    <span class="text-green-600">Free</span>
                                @endif
                            </dd>
                        </div>

                        <div class="flex justify-between text-base font-semibold text-gray-900 pt-3 border-t border-gray-200">
                            <dt>Total</dt>
                            <dd>{{ format_price($totals['total']) }}</dd>
                        </div>
                    </dl>
                </div>
            </section>
        </div>
    </div>
</x-layouts.app>
