<div class="space-y-8">
    <!-- Notification Settings -->
    <div class="form-card">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-xl font-bold text-gray-900">Pengaturan Notifikasi</h3>
                <p class="text-gray-600 mt-1">Atur cara Anda menerima pemberitahuan</p>
            </div>
            <div class="p-3 bg-yellow-50 rounded-xl">
                <i class="fas fa-bell text-yellow-600 text-xl"></i>
            </div>
        </div>
        
        <div class="space-y-6">
            @foreach(['Transaksi', 'Poin', 'Penarikan', 'Promo', 'Sistem'] as $type)
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                <div>
                    <h4 class="font-medium text-gray-900">{{ $type }}</h4>
                    <p class="text-sm text-gray-600 mt-1">Notifikasi tentang {{ strtolower($type) }}</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" class="sr-only peer" checked>
                    <div class="w-12 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>
            @endforeach
        </div>
        
        <div class="flex justify-end pt-6 border-t border-gray-200 mt-8">
            <button class="btn-primary bg-gradient-to-r from-yellow-500 to-orange-600 hover:from-yellow-600 hover:to-orange-700">
                <i class="fas fa-save mr-2"></i>Simpan Pengaturan
            </button>
        </div>
    </div>
    
    <!-- Recent Notifications -->
    <div class="form-card">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-xl font-bold text-gray-900">Notifikasi Terbaru</h3>
                <p class="text-gray-600 mt-1">Pemberitahuan terbaru dari sistem</p>
            </div>
            <div class="p-3 bg-green-50 rounded-xl">
                <i class="fas fa-envelope text-green-600 text-xl"></i>
            </div>
        </div>
        
        <div class="space-y-4">
            @foreach([
                ['icon' => 'fa-check-circle', 'color' => 'text-green-600', 'bg' => 'bg-green-100', 'title' => 'Transaksi Berhasil', 'time' => '5 menit lalu'],
                ['icon' => 'fa-coins', 'color' => 'text-yellow-600', 'bg' => 'bg-yellow-100', 'title' => 'Poin Bertambah', 'time' => '1 jam lalu'],
                ['icon' => 'fa-gift', 'color' => 'text-purple-600', 'bg' => 'bg-purple-100', 'title' => 'Promo Spesial', 'time' => '3 jam lalu'],
                ['icon' => 'fa-info-circle', 'color' => 'text-blue-600', 'bg' => 'bg-blue-100', 'title' => 'Pembaruan Sistem', 'time' => '1 hari lalu'],
            ] as $notification)
            <div class="flex items-start space-x-4 p-4 rounded-xl hover:bg-gray-50 transition-colors duration-300">
                <div class="w-12 h-12 {{ $notification['bg'] }} rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas {{ $notification['icon'] }} {{ $notification['color'] }} text-lg"></i>
                </div>
                <div class="flex-1">
                    <h4 class="font-medium text-gray-900">{{ $notification['title'] }}</h4>
                    <p class="text-sm text-gray-600 mt-1">Sistem NetraTrash mengirimkan pemberitahuan</p>
                    <p class="text-xs text-gray-500 mt-2">{{ $notification['time'] }}</p>
                </div>
                @if($loop->first)
                <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                @endif
            </div>
            @endforeach
        </div>
        
        <div class="text-center pt-6 border-t border-gray-200 mt-6">
            <button class="text-blue-600 hover:text-blue-700 font-medium">
                <i class="fas fa-bell mr-2"></i>Tandai Semua Sudah Dibaca
            </button>
        </div>
    </div>
</div>