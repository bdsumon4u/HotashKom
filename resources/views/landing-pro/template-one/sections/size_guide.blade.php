@if (data_get($sections, 'size_guide.enabled', true) && !empty($sizeRows))
    <section class="py-5 border-green-100 md:py-10 border-y-2 bg-green-50">
        <div class="max-w-3xl px-2 mx-auto md:px-4">
            <div class="p-8 bg-white border-2 border-green-700 rounded-md shadow-xl">
                <h3 class="mb-6 text-2xl font-black text-center text-gray-900 uppercase">
                    {{ data_get($sections, 'size_guide.title', 'সাইজ গাইড (Size Chart)') }}</h3>
                <div class="overflow-hidden border-2 border-gray-100 rounded-md">
                    <table class="w-full text-center bg-white">
                        <thead class="text-white bg-green-800">
                            <tr>
                                <th class="p-4">SIZE</th>
                                <th class="p-4">WAIST (কোমর)</th>
                                <th class="p-4">LENGTH (লেন্থ)</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-800 divide-y">
                            @foreach ($sizeRows as $row)
                                <tr>
                                    <td class="p-4 bg-gray-50">{{ $row['size'] }}</td>
                                    <td class="p-4">{{ $row['waist'] }}</td>
                                    <td class="p-4">{{ $row['length'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endif
