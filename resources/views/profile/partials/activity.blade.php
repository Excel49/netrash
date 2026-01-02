<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-activity me-2"></i>Activity Log</h5>
        <p class="text-muted mb-0">Monitor recent activities on your account</p>
    </div>
    <div class="card-body">
        <!-- Activity Stats -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6 class="text-uppercase text-muted mb-2">Today</h6>
                        <h3 class="mb-0">{{ $today_activities ?? 0 }}</h3>
                        <small>Activities</small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-3">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6 class="text-uppercase text-muted mb-2">This Month</h6>
                        <h3 class="mb-0">{{ $month_activities ?? 0 }}</h3>
                        <small>Activities</small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-3">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6 class="text-uppercase text-muted mb-2">Total</h6>
                        <h3 class="mb-0">{{ $total_activities ?? 0 }}</h3>
                        <small>Activities</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Activity Timeline -->
        <h6 class="mb-3">Recent Activities</h6>
        
        @if(isset($activities) && count($activities) > 0)
        <div class="timeline">
            @foreach($activities as $activity)
            <div class="timeline-item">
                <div class="timeline-icon">
                    @switch($activity->type)
                        @case('login')
                            <i class="bi bi-box-arrow-in-right"></i>
                            @break
                        @case('transaction')
                            <i class="bi bi-receipt"></i>
                            @break
                        @case('profile_update')
                            <i class="bi bi-person-circle"></i>
                            @break
                        @default
                            <i class="bi bi-info-circle"></i>
                    @endswitch
                </div>
                <div class="timeline-content">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-1">{{ $activity->title }}</h6>
                        <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                    </div>
                    <p class="mb-0">{{ $activity->description }}</p>
                    @if($activity->ip_address)
                    <small class="text-muted">IP: {{ $activity->ip_address }}</small>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-4">
            <i class="bi bi-activity display-4 text-muted mb-3"></i>
            <p class="text-muted">No recent activities</p>
        </div>
        @endif
        
        <!-- Export Button -->
        <div class="text-center mt-4">
            <button class="btn btn-outline-netra">
                <i class="bi bi-download me-2"></i>Export Activity Log
            </button>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background-color: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-icon {
    position: absolute;
    left: -30px;
    top: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background-color: #2E8B57;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
}

.timeline-content {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid #2E8B57;
}
</style>