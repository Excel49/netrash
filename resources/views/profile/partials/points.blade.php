@php
    $user = auth()->user();
    $stats = isset($stats) ? $stats : [
        'total_points' => 0,
        'total_transactions' => 0,
        'withdrawn_points' => 0,
        'pending_points' => 0
    ];
@endphp

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-coin me-2"></i>My Points</h5>
        <p class="text-muted mb-0">Manage and track points earned from waste transactions</p>
    </div>
    <div class="card-body">
        <!-- Points Summary -->
        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Total Points</h6>
                                <h3 class="mb-0">{{ number_format($stats['total_points'], 0, ',', '.') }}</h3>
                                <small>Equivalent to Rp {{ number_format($stats['total_points'] * 100, 0, ',', '.') }}</small>
                            </div>
                            <i class="bi bi-coin display-6 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Total Transactions</h6>
                                <h3 class="mb-0">{{ $stats['total_transactions'] }}</h3>
                                <small>Waste transactions</small>
                            </div>
                            <i class="bi bi-receipt display-6 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-3">
                <div class="card bg-warning text-dark">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Points Withdrawn</h6>
                                <h3 class="mb-0">{{ number_format($stats['withdrawn_points'], 0, ',', '.') }}</h3>
                                <small>Successfully withdrawn</small>
                            </div>
                            <i class="bi bi-cash-coin display-6 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Pending Points</h6>
                                <h3 class="mb-0">{{ number_format($stats['pending_points'], 0, ',', '.') }}</h3>
                                <small>Awaiting approval</small>
                            </div>
                            <i class="bi bi-clock-history display-6 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <h6>Quick Actions</h6>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('warga.penarikan.create') }}" class="btn btn-netra">
                        <i class="bi bi-cash-coin me-2"></i>Withdraw Points
                    </a>
                    <a href="{{ route('warga.transaksi.index') }}" class="btn btn-outline-netra">
                        <i class="bi bi-receipt me-2"></i>Transaction History
                    </a>
                    <a href="{{ route('warga.penarikan.index') }}" class="btn btn-outline-netra">
                        <i class="bi bi-clock-history me-2"></i>Withdrawal History
                    </a>
                    <a href="{{ route('warga.leaderboard') }}" class="btn btn-outline-netra">
                        <i class="bi bi-trophy me-2"></i>Leaderboard
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Recent Transactions -->
        <div>
            <h6 class="mb-3">Recent Transactions</h6>
            
            @if(isset($recentTransactions) && $recentTransactions->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Points</th>
                            <th>Weight</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentTransactions as $transaction)
                        <tr>
                            <td>{{ $transaction->created_at->format('d/m/Y') }}</td>
                            <td>Waste Deposit</td>
                            <td class="text-success">+{{ number_format($transaction->total_poin, 0, ',', '.') }}</td>
                            <td>{{ number_format($transaction->total_berat, 1) }} kg</td>
                            <td>
                                <span class="badge bg-success">Completed</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-4">
                <i class="bi bi-receipt display-4 text-muted mb-3"></i>
                <p class="text-muted">No transactions yet</p>
                <a href="{{ route('warga.qrcode.index') }}" class="btn btn-netra">
                    <i class="bi bi-qr-code-scan me-2"></i>Start Your First Transaction
                </a>
            </div>
            @endif
        </div>
    </div>
</div>