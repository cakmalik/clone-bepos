<div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold text-center ">Setup Wizard</h2>
        <h3 class="mb-6 text-center">{{ $step_title }}</h3>


        @if ($step == 1)
            <div class="mb-4">
                <label for="product_type" class="block text-sm font-medium text-gray-700">Tipe Produk</label>
                <select id="product_type" wire:model="selected_product_type"
                    class="mt-1 p-2 w-full border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Pilih Tipe Produk --</option>
                    @foreach ($product_types as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>

                @error('selected_product_type')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label for="company_name" class="block text-sm font-medium text-gray-700">Nama Perusahaan</label>
                <input type="text" id="company_name" name="company_name" wire:model='company_name'
                    placeholder="Masukkan Nama Perusahaan"
                    class="mt-1 p-2 w-full border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">

                @error('company_name')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                @enderror
            </div>
        @endif

        @if ($step == 2)
            <div class="mb-4">
                @foreach ($inventories_name as $index => $name)
                    <div class="flex items-center mb-2">
                        <input type="text" wire:model="inventories_name.{{ $index }}"
                            placeholder="Nama Gudang" class="border p-2 rounded w-full" />

                        <button type="button" wire:click="removeInventory({{ $index }})"
                            class="ml-2 bg-red-500 text-white px-2 py-1 rounded">
                            Hapus
                        </button>
                    </div>
                @endforeach
                @if ($canAddInventory)
                    <button type="button" wire:click="addInventory"
                        class="mt-2 bg-blue-500 text-white px-4 py-2 rounded">
                        + Tambah Gudang
                    </button>
                @endif

            </div>
        @endif

        @if ($step == 3)
            <div class="mb-4">
                <h3 class="text-lg font-bold mb-2">Step 3: Form Outlet</h3>

                @foreach ($outlets_name as $index => $name)
                    <div class="flex items-center mb-2">
                        <input type="text" wire:model="outlets_name.{{ $index }}" placeholder="Nama Outlet"
                            class="border p-2 rounded w-full" />

                        <button type="button" wire:click="removeOutlet({{ $index }})"
                            class="ml-2 bg-red-500 text-white px-2 py-1 rounded">
                            Hapus
                        </button>
                    </div>
                @endforeach

                @if ($canAddOutlet)
                    <button type="button" wire:click="addOutlet" class="mt-2 bg-blue-500 text-white px-4 py-2 rounded">
                        + Tambah Outlet
                    </button>
                @endif

            </div>
        @endif


        <div class="flex gap-2">
            @if ($step > 1)
                <button wire:click='backStep'
                    class="flex-1 bg-blue-500 text-white p-2 rounded-md hover:bg-blue-600">Back</button>
            @endif
            @if ($step == 3)
                <button wire:click='generate'
                    class="flex-1 bg-green-500 text-white p-2 rounded-md hover:bg-green-600">Generate</button>
            @else
                <button wire:click='validateNextStep'
                    class="flex-1 bg-blue-500 text-white p-2 rounded-md hover:bg-blue-600">Next</button>
            @endif
        </div>


    </div>
</div>
