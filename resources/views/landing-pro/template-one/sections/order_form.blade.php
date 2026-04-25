@if (data_get($sections, 'order_form.enabled', true))
    <section id="order" class="bg-white border-t-8 border-green-800 py-7 md:py-14">
        <div class="grid gap-8 px-2 mx-auto md:px-4 max-w-7xl lg:grid-cols-12">
            <div class="lg:col-span-7">
                <h2 class="mb-4 text-3xl font-black text-gray-900">
                    {{ data_get($sections, 'order_form.title', 'পণ্য ও পরিমাণ নির্বাচন করুন') }}</h2>
                <p class="mb-6 text-sm font-semibold text-gray-600">
                    {{ data_get($sections, 'order_form.subtitle', 'Choose Products & Quantity') }}</p>

                <div class="grid gap-4" :class="products.length > 4 ? 'md:grid-cols-2' : ''">
                    <template x-for="(product, index) in products" :key="product.id">
                        <div class="p-3 transition-all bg-white border rounded-md shadow-sm cursor-pointer group"
                            :class="product.selected ? 'border-blue-500 bg-blue-50' : 'border-gray-200'"
                            @click="toggleProductSelection(index)">
                            <div class="flex gap-3">
                                <input type="checkbox" :checked="product.selected"
                                    class="w-4 h-4 mt-1 cursor-pointer accent-blue-600" @click.stop
                                    @change="toggleProductSelection(index)">
                                <img :src="product.image" alt=""
                                    class="object-cover w-16 h-16 transition border rounded-lg group-hover:scale-105">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-gray-800" x-text="product.name"></p>
                                    <p class="mt-1 text-sm font-black text-green-700" x-text="product.price + '৳'"></p>
                                    <p x-show="product.free_delivery" class="mt-1 text-xs font-bold text-emerald-700">
                                        ফ্রি ডেলিভারি প্রযোজ্য
                                    </p>
                                    <div class="inline-flex items-center mt-2 text-sm bg-white border rounded"
                                        @click.stop>
                                        <button @click="decrement(index)" type="button"
                                            class="px-3 py-1 hover:bg-gray-100">−</button>
                                        <span class="w-10 font-bold text-center" x-text="product.qty"></span>
                                        <button @click="increment(index)" type="button"
                                            class="px-3 py-1 hover:bg-gray-100">+</button>
                                    </div>

                                    <template x-if="product.attributes.length > 0">
                                        <div class="grid gap-2 mt-3" @click.stop>
                                            <template x-for="attribute in product.attributes"
                                                :key="`${product.id}-${attribute.attribute_id}`">
                                                <div>
                                                    <label
                                                        class="block mb-1 text-[11px] font-bold uppercase text-gray-500"
                                                        x-text="attribute.attribute_name"></label>
                                                    <div class="flex flex-wrap gap-2">
                                                        <template x-for="option in attribute.options"
                                                            :key="`${attribute.attribute_id}-${option.id}`">
                                                            <label
                                                                class="inline-flex items-center gap-1 px-2 py-1 text-xs bg-white border rounded cursor-pointer"
                                                                :class="Number(attribute.selected_option_id) === Number(option
                                                                        .id) ?
                                                                    'border-green-600 text-green-700' :
                                                                    'border-gray-200 text-gray-700'">
                                                                <input type="radio"
                                                                    :name="`attr-${product.id}-${attribute.attribute_id}`"
                                                                    :value="Number(option.id)"
                                                                    x-model.number="attribute.selected_option_id"
                                                                    @change="selectVariantByAttributes(index)"
                                                                    class="accent-green-600">
                                                                <span x-text="option.name"></span>
                                                            </label>
                                                        </template>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                    <p x-cloak x-show="product.attribute_warning"
                                        class="mt-2 text-xs font-semibold text-red-600">
                                        Select attributes please
                                    </p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="lg:col-span-5">
                <div class="sticky overflow-hidden bg-white border border-gray-200 rounded-md shadow-2xl top-28">
                    <div class="p-3 text-white md:py-5 md:px-6 bg-gradient-to-r from-green-700 to-green-800">
                        <h3 class="text-2xl font-black text-center">ডেলিভারি তথ্য</h3>
                        <p class="mt-1 text-sm text-center text-green-100">দ্রুত ডেলিভারির জন্য সঠিক তথ্য দিন</p>
                    </div>
                    <form class="p-3 space-y-4 md:p-6 bg-gray-50" @submit.prevent="goCheckout">
                        <div class="grid grid-cols-1 gap-3 lg:grid-cols-2">
                            <div>
                                <label class="block mb-1 text-xs font-bold tracking-wide text-gray-700 uppercase">আপনার
                                    নাম</label>
                                <input type="text" x-model.trim="checkout.name" @blur="checkout.touched.name = true"
                                    placeholder="যেমন: মোহাম্মদ রাহাত"
                                    class="w-full rounded-lg border bg-white px-3 py-2.5 text-sm outline-none transition"
                                    :class="showNameError ? 'border-red-400 focus:border-red-500' :
                                        'border-gray-200 focus:border-green-600'">
                                <p x-cloak class="mt-1 text-xs font-semibold text-red-600" x-show="showNameError">নাম
                                    লিখুন।</p>
                            </div>

                            <div>
                                <label class="block mb-1 text-xs font-bold tracking-wide text-gray-700 uppercase">মোবাইল
                                    নাম্বার</label>
                                <input type="tel" x-model.trim="checkout.phone"
                                    @blur="checkout.touched.phone = true" placeholder="01XXXXXXXXX"
                                    class="w-full rounded-lg border bg-white px-3 py-2.5 text-sm outline-none transition"
                                    :class="showPhoneError ? 'border-red-400 focus:border-red-500' :
                                        'border-gray-200 focus:border-green-600'">
                                <p x-cloak class="mt-1 text-xs font-semibold text-red-600" x-show="showPhoneError">সঠিক
                                    মোবাইল নাম্বার দিন (01XXXXXXXXX)।</p>
                            </div>

                            <div class="p-3 bg-white border rounded-md lg:col-span-2">
                                <p class="mb-2 text-xs font-bold tracking-wide text-gray-700 uppercase">ডেলিভারি এরিয়া
                                </p>
                                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                                    <label
                                        class="flex items-center justify-between p-2 border rounded-lg cursor-pointer"
                                        :class="checkout.deliveryArea === 'inside' ? 'border-green-500 bg-green-50' :
                                            'border-gray-200'">
                                        <div class="flex items-center gap-2">
                                            <input type="radio" value="inside" x-model="checkout.deliveryArea"
                                                class="accent-green-600">
                                            <span class="text-sm font-semibold text-gray-800">Inside Dhaka</span>
                                        </div>
                                        <span class="text-sm font-black text-green-700"
                                            x-text="hasFreeDeliveryItem ? 'FREE' : `${insideDhakaDeliveryCharge}৳`"></span>
                                    </label>
                                    <label
                                        class="flex items-center justify-between p-2 border rounded-lg cursor-pointer"
                                        :class="checkout.deliveryArea === 'outside' ? 'border-green-500 bg-green-50' :
                                            'border-gray-200'">
                                        <div class="flex items-center gap-2">
                                            <input type="radio" value="outside" x-model="checkout.deliveryArea"
                                                class="accent-green-600">
                                            <span class="text-sm font-semibold text-gray-800">Outside Dhaka</span>
                                        </div>
                                        <span class="text-sm font-black text-green-700"
                                            x-text="hasFreeDeliveryItem ? 'FREE' : `${outsideDhakaDeliveryCharge}৳`"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="lg:col-span-2">
                                <label
                                    class="block mb-1 text-xs font-bold tracking-wide text-gray-700 uppercase">সম্পূর্ণ
                                    ঠিকানা</label>
                                <textarea x-model.trim="checkout.address" @blur="checkout.touched.address = true" rows="2"
                                    placeholder="এরিয়া, থানা, জেলা সহ পূর্ণ ঠিকানা লিখুন"
                                    class="w-full rounded-lg border bg-white px-3 py-2.5 text-sm outline-none transition"
                                    :class="showAddressError ? 'border-red-400 focus:border-red-500' :
                                        'border-gray-200 focus:border-green-600'"></textarea>
                                <p x-cloak class="mt-1 text-xs font-semibold text-red-600" x-show="showAddressError">
                                    সম্পূর্ণ ঠিকানা লিখুন।</p>
                            </div>
                        </div>

                        <div x-cloak x-show="products.length > 4 && selectedCount > 2"
                            class="p-4 bg-white border-2 border-green-300 border-dashed rounded-md">
                            <div class="flex items-center justify-between text-sm font-bold text-gray-700">
                                <span>Selected Items</span>
                                <span class="px-2 py-1 text-white bg-green-600 rounded-sm"
                                    x-text="selectedCount"></span>
                            </div>
                            <div class="pr-1 space-y-2 overflow-y-auto max-h-32">
                                <template x-for="item in selectedItems" :key="item.id">
                                    <div
                                        class="flex items-center justify-between p-2 text-xs border border-gray-100 rounded bg-gray-50">
                                        <div class="min-w-0">
                                            <span class="font-semibold" x-text="item.name + ' x ' + item.qty"></span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="font-black text-green-700"
                                                x-text="(item.price * item.qty) + '৳'"></span>
                                            <button type="button" @click="removeSelected(item.id)"
                                                class="px-2 py-1 text-[11px] font-bold text-red-700 bg-red-100 rounded hover:bg-red-200">
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <p class="mt-3 text-xs text-red-600" x-show="selectedCount === 0">অন্তত একটি প্রোডাক্ট
                                সিলেক্ট করুন।</p>
                        </div>

                        <div class="p-4 bg-white border rounded-md">
                            <div class="flex items-center justify-between mb-1 text-sm font-bold text-gray-700">
                                <span>Subtotal</span>
                                <span x-text="totalPrice + '৳'"></span>
                            </div>
                            <div class="flex items-center justify-between mb-2 text-sm font-bold text-gray-700">
                                <span>Delivery</span>
                                <span x-text="deliveryCharge + '৳'"></span>
                            </div>
                            <div
                                class="flex items-center justify-between pt-2 mb-3 text-sm font-black text-gray-900 border-t">
                                <span>Grand Total</span>
                                <span class="text-lg text-green-700" x-text="grandTotal + '৳'"></span>
                            </div>
                            <button type="submit"
                                class="w-full py-3 text-base font-black text-white transition bg-red-600 rounded-md hover:bg-red-700 disabled:bg-gray-400"
                                :disabled="loading || !isCheckoutValid">
                                <span x-show="!loading">অর্ডার কনফার্ম করুন</span>
                                <span x-show="loading">প্রসেস হচ্ছে...</span>
                            </button>
                            <a href="{{ $callUrl }}"
                                class="block mt-3 text-sm font-bold text-center text-gray-700 hover:text-green-700">সাহায্য
                                দরকার? কল করুন</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endif
