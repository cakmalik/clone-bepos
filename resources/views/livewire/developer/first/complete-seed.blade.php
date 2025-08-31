<div class="flex items-center justify-center min-h-screen bg-gray-100 gap-3">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold text-center ">Setup Completed :)</h2>
        <p class="mb-6 text-gray-500 text-center">Cek Berkala data user</p>

        <div class="flex mb-6">
            <button wire:click="toggleInfoAccount" class="flex-1 bg-gray-500 text-white p-2 rounded-md hover:bg-gray-600">
                Information Account
            </button>
        </div>


        <div class="flex gap-2" wire:loading.remove>
            <button wire:click="refresh" class="flex-1 bg-red-500 text-white p-2 rounded-md hover:bg-red-600">
                Refresh Database
            </button>

            <button wire:click="importProduct" class="flex-1 bg-blue-500 text-white p-2 rounded-md hover:bg-blue-600">
                Import Product
            </button>
        </div>


        <div class="flex mt-6">
            <a href="{{ route('logout') }}"
                class="flex-1 bg-black text-white p-2 rounded-md hover:bg-black justify-center text-center">
                Logout
            </a>
        </div>

        {{-- <div class="w-full justify-center hidden" wire:loading.flex>
            <div class="h-6 w-6 animate-spin rounded-full border-4 border-t-4 border-gray-200 border-t-blue-500"></div>
        </div> --}}
    </div>


    @if ($showInfoAccount)
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
            <div class="flex w-full justify-end"><a href="javascript:window.location.reload();"
                    class="bg-gray-500 text-white py-1 px-2 rounded-md hover:bg-gray-600">Refresh Page</a></div>
            @if ($users)
                <div class="w-full overflow-x-auto">
                    <h3 class="font-semibold mb-3">Users</h3>
                    <table class="w-full border-collapse border border-gray-200 shadow-md rounded-md">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left text-gray-600 font-medium">Name</th>
                                <th class="px-4 py-2 text-left text-gray-600 font-medium">Username</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 border-b border-gray-200">{{ $user->users_name }}</td>
                                    <td class="px-4 py-2 border-b border-gray-200">{{ $user->username }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

        </div>
    @endif

    @if ($form_import)
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
            {{-- <input type="checkbox" id="isFresh" wire:model='isFresh' class="border p-2 rounded" /> --}}
            <label for="isFresh" class="ml-2">Fresh Data Produk</label>
            <input type="file" wire:model="selectedFile" class="border p-2 rounded w-full" />
            @error('selectedFile')
                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
            @enderror

            <button type="button" wire:click="import" class="mt-2 bg-blue-500 text-white px-4 py-2 rounded"
                wire:loading.remove>
                Import
            </button>
        </div>
    @endif


    <script>
        // document.addEventListener('livewire:load', function() {
        //     Livewire.on('import-running', () => {
        //         alert('Import sedang berjalan di background! Cek berkala log dan data produk');
        //         document.getElementById('loading-spinner').style.display = 'block';
        //     });
        // });

        // Livewire.on('import-running', = () => {
        //     alert('Import sedang berjalan di background! Cek berkala log dan data produk');
        // });
        window.addEventListener('import-run', () => {
            alert('Import sedang berjalan di background! Cek berkala log dan data produk');
        })

        setInterval(() => {
            window.location.reload();
        }, 5000);
    </script>
</div>
