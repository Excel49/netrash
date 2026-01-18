<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NetraTrash - Kelola Sampah, Dapatkan Poin</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        .hero-gradient {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }
        .feature-card:hover {
            transform: translateY(-10px);
            transition: transform 0.3s ease;
        }
        .tip-slide {
            animation: slideIn 0.5s ease-out;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body class="font-sans bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg fixed w-full z-10">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-recycle text-3xl text-green-500"></i>
                    <span class="text-2xl font-bold text-gray-800">Netra<span class="text-green-500">Trash</span></span>
                </div>
                
                <div class="hidden md:flex space-x-8">
                    <a href="#home" class="text-gray-700 hover:text-green-500 font-medium">Beranda</a>
                    <a href="#features" class="text-gray-700 hover:text-green-500 font-medium">Fitur</a>
                    <a href="#stats" class="text-gray-700 hover:text-green-500 font-medium">Statistik</a>
                    <a href="#about" class="text-gray-700 hover:text-green-500 font-medium">Tentang</a>
                </div>
                
                <div class="flex items-center space-x-4">
                    <a href="{{ route('login') }}" 
                       class="text-green-500 hover:text-green-600 font-medium">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" 
                       class="bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-6 rounded-lg transition duration-300">
                        Daftar Gratis
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-gradient text-white pt-24 pb-20">
        <div class="container mx-auto px-4">
            <div class="flex flex-col lg:flex-row items-center">
                <div class="lg:w-1/2 mb-12 lg:mb-0">
                    <h1 class="text-5xl font-bold mb-6 leading-tight">
                        Kelola Sampah,<br>Dapatkan <span class="text-yellow-300">Poin</span>
                    </h1>
                    <p class="text-xl mb-8 text-gray-100">
                        Aplikasi inovatif untuk pengelolaan sampah berbasis poin. 
                        Setor sampah, kumpulkan poin, dan tukarkan dengan hadiah menarik!
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('register') }}" 
                           class="bg-white text-green-600 hover:bg-gray-100 font-bold py-3 px-8 rounded-lg text-lg transition duration-300">
                            Mulai Sekarang <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                        <a href="#features" 
                           class="border-2 border-white text-white hover:bg-white hover:text-green-500 font-bold py-3 px-8 rounded-lg text-lg transition duration-300">
                            Lihat Fitur
                        </a>
                    </div>
                </div>
                
                <div class="lg:w-1/2">
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8">
                        <h3 class="text-2xl font-bold mb-6 text-center">Statistik Real-time</h3>
                        <div class="grid grid-cols-2 gap-6 mb-8">
                            <div class="bg-white/20 p-4 rounded-lg text-center">
                                <div class="text-3xl font-bold">{{ number_format($stats['total_users']) }}</div>
                                <div class="text-sm">Pengguna</div>
                            </div>
                            <div class="bg-white/20 p-4 rounded-lg text-center">
                                <div class="text-3xl font-bold">{{ number_format($stats['total_transactions']) }}</div>
                                <div class="text-sm">Transaksi</div>
                            </div>
                            <div class="bg-white/20 p-4 rounded-lg text-center">
                                <div class="text-3xl font-bold">{{ number_format($stats['total_berat']) }} kg</div>
                                <div class="text-sm">Sampah Terkelola</div>
                            </div>
                            <div class="bg-white/20 p-4 rounded-lg text-center">
                                <div class="text-3xl font-bold">{{ number_format($stats['total_points']) }}</div>
                                <div class="text-sm">Poin Terkumpul</div>
                            </div>
                        </div>
                        
                        <!-- Pie Chart -->
                        <div class="relative h-64">
                            <canvas id="sampahChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold text-center text-gray-800 mb-4">Fitur Utama NetraTrash</h2>
            <p class="text-gray-600 text-center mb-12 max-w-2xl mx-auto">
                Temukan berbagai fitur canggih yang membuat pengelolaan sampah menjadi mudah dan menguntungkan
            </p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($features as $feature)
                <div class="bg-gray-50 rounded-2xl p-6 shadow-lg feature-card hover:shadow-xl border border-gray-100">
                    <div class="w-16 h-16 rounded-xl bg-gradient-to-br {{ $feature['color'] }} text-white flex items-center justify-center mb-6 mx-auto">
                        <i class="{{ $feature['icon'] }} text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3 text-center">{{ $feature['title'] }}</h3>
                    <p class="text-gray-600 text-center mb-6">{{ $feature['description'] }}</p>
                    <div class="text-center">
                        <a href="{{ route($feature['route']) }}" 
                           class="text-green-500 hover:text-green-600 font-medium inline-flex items-center">
                            Coba Sekarang <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section id="stats" class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-4xl font-bold text-gray-800 mb-6">Distribusi Jenis Sampah</h2>
                    <p class="text-gray-600 mb-8">
                        Berikut adalah persentase jenis sampah yang telah dikumpulkan melalui sistem NetraTrash. 
                        Data ini membantu dalam pengelolaan dan daur ulang sampah yang lebih efektif.
                    </p>
                    
                    <!-- Tips Section -->
                    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                        <div class="flex items-center mb-4">
                            <i class="fas fa-lightbulb text-yellow-500 text-2xl mr-3"></i>
                            <h3 class="text-xl font-bold text-gray-800">Tips Hari Ini</h3>
                        </div>
                        <p class="text-gray-700 tip-slide" id="randomTip">{{ $randomTip }}</p>
                        <button onclick="refreshTip()" 
                                class="mt-4 text-green-500 hover:text-green-600 font-medium inline-flex items-center">
                            <i class="fas fa-sync-alt mr-2"></i> Tips Lainnya
                        </button>
                    </div>
                    
                    <!-- Quick Stats -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white rounded-xl p-4 shadow">
                            <div class="text-2xl font-bold text-green-600">{{ $stats['active_warga'] }}</div>
                            <div class="text-gray-600">Warga Aktif</div>
                        </div>
                        <div class="bg-white rounded-xl p-4 shadow">
                            <div class="text-2xl font-bold text-blue-600">{{ $stats['active_petugas'] }}</div>
                            <div class="text-gray-600">Petugas</div>
                        </div>
                    </div>
                </div>
                
                <div>
                    <div class="bg-white rounded-2xl shadow-xl p-8">
                        <h3 class="text-2xl font-bold text-gray-800 mb-6 text-center">Komposisi Sampah</h3>
                        <div class="relative h-96">
                            <canvas id="pieChart"></canvas>
                        </div>
                        <div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-4" id="chartLegend"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold text-center text-gray-800 mb-12">Apa Kata Pengguna?</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($testimonials as $testimonial)
                <div class="bg-gray-50 rounded-2xl p-6 border border-gray-200">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-green-500">
                            <i class="fas fa-user text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h4 class="font-bold text-gray-800">{{ $testimonial['name'] }}</h4>
                            <p class="text-gray-600 text-sm">{{ $testimonial['role'] }}</p>
                        </div>
                        <div class="ml-auto bg-green-100 text-green-600 font-bold py-1 px-3 rounded-full">
                            {{ $testimonial['points'] }} poin
                        </div>
                    </div>
                    <p class="text-gray-700 italic">"{{ $testimonial['content'] }}"</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 hero-gradient text-white">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-4xl font-bold mb-6">Siap Mengelola Sampah dengan Lebih Baik?</h2>
            <p class="text-xl mb-8 max-w-2xl mx-auto">
                Bergabunglah dengan ribuan pengguna NetraTrash dan mulai kumpulkan poin dari sampah Anda!
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('register') }}" 
                   class="bg-white text-green-600 hover:bg-gray-100 font-bold py-3 px-8 rounded-lg text-lg transition duration-300">
                    Daftar Sekarang Gratis
                </a>
                <a href="{{ route('login') }}" 
                   class="bg-transparent border-2 border-white hover:bg-white hover:text-green-500 font-bold py-3 px-8 rounded-lg text-lg transition duration-300">
                    Masuk ke Akun
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-6 md:mb-0">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-recycle text-3xl text-green-400 mr-3"></i>
                        <span class="text-2xl font-bold">Netra<span class="text-green-400">Trash</span></span>
                    </div>
                    <p class="text-gray-400">Mengubah sampah menjadi nilai, menyelamatkan bumi.</p>
                </div>
                
                <div class="text-center md:text-right">
                    <p class="text-gray-400 mb-2">&copy; {{ date('Y') }} NetraTrash. All rights reserved.</p>
                    <p class="text-gray-500 text-sm">v1.0.0</p>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Pie Chart Configuration
        const pieData = @json($pieData);
        const pieLabels = @json($pieLabels);
        const pieColors = @json($pieColors);
        
                console.log('PHP Data:', {
            pieData: pieData,
            pieLabels: pieLabels,
            total: pieData.reduce((a, b) => a + b, 0)
        });
        
        // Hitung persentase
        const total = pieData.reduce((a, b) => a + b, 0);
        const percentages = total > 0 ? 
            pieData.map(value => ((value / total) * 100).toFixed(1)) : 
            pieData.map(() => 0);
        console.log('Percentages:', percentages);

        // Update pie labels dengan persentase
        const pieLabelsWithPercent = percentages.map((percent, index) => 
            total > 0 ? `${pieLabels[index]} (${percent}%)` : pieLabels[index]
        );
        
        // Initialize Pie Chart
        const pieCtx = document.getElementById('pieChart').getContext('2d');
        const pieChart = new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: pieLabelsWithPercent,
                datasets: [{
                    data: pieData,
                    backgroundColor: pieColors,
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const percentage = context.parsed || 0;
                                return `${label}: ${value} kg (${((percentage/total)*100).toFixed(1)}%)`;
                            }
                        }
                    }
                }
            }
        });
        
        // Create custom legend
         function createLegend() {
            const legendContainer = document.getElementById('chartLegend');
            legendContainer.innerHTML = '';
            
            // Pastikan total dihitung ulang dengan data aktual
            const actualTotal = pieData.reduce((a, b) => a + b, 0);
            const actualPercentages = pieData.map(value => 
                actualTotal > 0 ? ((value / actualTotal) * 100).toFixed(1) : 0
            );
            
            pieLabels.forEach((label, index) => {
                const percentage = actualPercentages[index];
                const color = pieColors[index];
                const value = pieData[index];
                
                const legendItem = document.createElement('div');
                legendItem.className = 'text-center';
                legendItem.innerHTML = `
                    <div class="flex items-center justify-center mb-2">
                        <div class="w-4 h-4 rounded-full mr-2" style="background-color: ${color}"></div>
                        <span class="font-medium text-gray-800">${label}</span>
                    </div>
                    <div class="text-2xl font-bold" style="color: ${color}">${percentage}%</div>
                    <div class="text-gray-600 text-sm">${value} kg</div>
                `;
                
                legendContainer.appendChild(legendItem);
            });
            
            // DEBUG: Log ke console
            console.log('Chart Data:', {
                labels: pieLabels,
                data: pieData,
                total: actualTotal,
                percentages: actualPercentages
            });
        }
        
        // Initialize Bar Chart (smaller one)
        const sampahCtx = document.getElementById('sampahChart').getContext('2d');
        const sampahChart = new Chart(sampahCtx, {
            type: 'doughnut',
            data: {
                labels: pieLabels,
                datasets: [{
                    data: pieData,
                    backgroundColor: pieColors,
                    borderWidth: 1,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            font: {
                                size: 12
                            }
                        }
                    }
                }
            }
        });
        
        // Function to refresh random tip
                // Function to refresh random tip
        async function refreshTip() {
            try {
                const response = await fetch('/api/tips/random');
                const data = await response.json();
                
                if (data.success) {
                    const tipElement = document.getElementById('randomTip');
                    tipElement.classList.remove('tip-slide');
                    void tipElement.offsetWidth; // Trigger reflow
                    tipElement.textContent = data.tip;
                    tipElement.classList.add('tip-slide');
                }
            } catch (error) {
                // Fallback to predefined tips
                const fallbackTips = [
                    "♻️ Pisahkan sampah organik dan anorganik untuk memudahkan daur ulang",
                    "🌱 Sampah organik bisa dijadikan kompos untuk tanaman",
                    "⚡ 1 kg sampah plastik bisa menghasilkan listrik untuk 1 jam",
                    "💰 Setiap 1 kg sampah kertas bisa ditukar dengan poin belanja",
                    "🌍 Indonesia menghasilkan 64 juta ton sampah per tahun"
                ];
                const randomTip = fallbackTips[Math.floor(Math.random() * fallbackTips.length)];
                const tipElement = document.getElementById('randomTip');
                tipElement.classList.remove('tip-slide');
                void tipElement.offsetWidth;
                tipElement.textContent = randomTip;
                tipElement.classList.add('tip-slide');
            }
        }
        
        // Create legend when page loads
        document.addEventListener('DOMContentLoaded', function() {
            createLegend();
            
            // Auto-refresh tip every 30 seconds
            setInterval(refreshTip, 30000);
        });
        
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>
</body>
</html>