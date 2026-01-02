<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Log Aktivitas</h5>
        <p class="text-muted mb-0">Riwayat aktivitas akun Anda</p>
    </div>
    <div class="card-body">
        <!-- Activity Stats -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card bg-light border">
                    <div class="card-body text-center">
                        <h2 class="mb-1">{{ $stats['today_activities'] ?? 0 }}</h2>
                        <small class="text-muted">Hari Ini</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card bg-light border">
                    <div class="card-body text-center">
                        <h2 class="mb-1">{{ $stats['month_activities'] ?? 0 }}</h2>
                        <small class="text-muted">Bulan Ini</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card bg-light border">
                    <div class="card-body text-center">
                        <h2 class="mb-1">{{ $stats['total_activities'] ?? 0 }}</h2>
                        <small class="text-muted">Total Aktivitas</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Activity List -->
        <div class="list-group">
            @forelse($activities as $activity)
            <div class="list-group-item">
                <div class="d-flex align-items-start">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center">
                            <i class="bi {{ $activity['icon'] }} text-{{ $activity['color'] }}"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="mb-0">{{ $activity['title'] }}</h6>
                            @if(isset($activity['points']))
                            <span class="badge bg-{{ $activity['color'] }}">{{ $activity['points'] }}</span>
                            @endif
                        </div>
                        <p class="text-muted mb-1">{{ $activity['description'] }}</p>
                        <small class="text-muted">
                            <i class="bi bi-clock me-1"></i>{{ $activity['time'] }}
                        </small>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-4">
                <i class="bi bi-clock-history display-4 text-muted mb-3"></i>
                <h6 class="text-muted">Belum ada aktivitas</h6>
                <p class="text-muted">Aktivitas Anda akan muncul di sini</p>
            </div>
            @endforelse
        </div>
    </div>
</div>