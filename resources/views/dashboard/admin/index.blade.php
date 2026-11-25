<x-admin>
    <div class="container-fluid py-4">
        <h1 class="dashboard-title text-center text-md-start mb-4">Admin Dashboard</h1>

        <div class="row g-3">
            <!-- Total Users -->
            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                <a href="{{ route('admin.users') }}" class="stats-link" aria-label="View all users">
                    <div class="stats-card primary text-center text-md-start">
                        <div class="icon mb-2">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                        <div class="stats-value">{{ $totalUsers }}</div>
                        <div class="stats-label">Total Users</div>
                        <i class="fas fa-arrow-right stats-go" aria-hidden="true"></i>
                    </div>
                </a>
            </div>

            <!-- Total Deposits -->
            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                <a href="{{ route('admin.deposits.all') }}" class="stats-link" aria-label="View all deposits">
                    <div class="stats-card success text-center text-md-start">
                        <div class="icon mb-2">
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                        <div class="stats-value">${{ number_format($totalDeposits, 2) }}</div>
                        <div class="stats-label">Total Deposits</div>
                        <i class="fas fa-arrow-right stats-go" aria-hidden="true"></i>
                    </div>
                </a>
            </div>

            <!-- Total Withdrawals -->
            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                <a href="{{ route('admin.withdrawals') }}" class="stats-link" aria-label="View all withdrawals">
                    <div class="stats-card danger text-center text-md-start">
                        <div class="icon mb-2">
                            <i class="fas fa-arrow-up fa-2x"></i>
                        </div>
                        <div class="stats-value">${{ number_format($totalWithdrawals, 2) }}</div>
                        <div class="stats-label">Total Withdrawals</div>
                        <i class="fas fa-arrow-right stats-go" aria-hidden="true"></i>
                    </div>
                </a>
            </div>

            <!-- Pending Deposits -->
            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                <a href="{{ route('admin.deposits.all', ['status' => 'pending']) }}" class="stats-link" aria-label="View pending deposits">
                    <div class="stats-card info text-center text-md-start">
                        <div class="icon mb-2">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                        <div class="stats-value">{{ $pendingDeposits }}</div>
                        <div class="stats-label">Pending Deposits</div>
                        <i class="fas fa-arrow-right stats-go" aria-hidden="true"></i>
                    </div>
                </a>
            </div>

            <!-- Pending Withdrawals -->
            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                <a href="{{ route('admin.withdrawals', ['status' => 'pending']) }}" class="stats-link" aria-label="View pending withdrawals">
                    <div class="stats-card info text-center text-md-start">
                        <div class="icon mb-2">
                            <i class="fas fa-hourglass-half fa-2x"></i>
                        </div>
                        <div class="stats-value">{{ $pendingWithdrawals }}</div>
                        <div class="stats-label">Pending Withdrawals</div>
                        <i class="fas fa-arrow-right stats-go" aria-hidden="true"></i>
                    </div>
                </a>
            </div>

            <!-- Active Plans -->
            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                <a href="{{ route('admin.show.plans') }}" class="stats-link" aria-label="View investment plans">
                    <div class="stats-card success text-center text-md-start">
                        <div class="icon mb-2">
                            <i class="fas fa-coins fa-2x"></i>
                        </div>
                        <div class="stats-value">{{ $activePlansCount }}</div>
                        <div class="stats-label">Active Plans</div>
                        <i class="fas fa-arrow-right stats-go" aria-hidden="true"></i>
                    </div>
                </a>
            </div>
        </div>
    </div>

    {{-- Optional Inline CSS --}}
    <style>
        .stats-card {
            padding: 1.5rem;
            border-radius: 12px;
            background-color: #ffffff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            transition: all 0.3s ease-in-out;
        }

        .stats-card .icon {
            color: #6c757d;
        }

        .stats-card.primary {
            border-left: 4px solid #007bff;
        }

        .stats-card.success {
            border-left: 4px solid #28a745;
        }

        .stats-card.danger {
            border-left: 4px solid #dc3545;
        }

        .stats-card.info {
            border-left: 4px solid #17a2b8;
        }

        .stats-card .stats-value {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .stats-card .stats-label {
            color: #6c757d;
            font-size: 0.9rem;
        }

        /* Whole card is a link to its page */
        .stats-link {
            display: block;
            height: 100%;
            color: inherit;
            text-decoration: none;
            border-radius: 12px;
        }

        .stats-link:hover,
        .stats-link:focus {
            color: inherit;
            text-decoration: none;
        }

        .stats-link .stats-card {
            position: relative;
            cursor: pointer;
        }

        .stats-link:hover .stats-card,
        .stats-link:focus-visible .stats-card {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }

        .stats-link:focus-visible {
            outline: 2px solid #34E834;
            outline-offset: 3px;
        }

        .stats-go {
            position: absolute;
            top: 1rem;
            right: 1rem;
            color: #adb5bd;
            font-size: 0.9rem;
            transition: transform 0.2s ease, color 0.2s ease;
        }

        .stats-link:hover .stats-go {
            color: #1FAF1F;
            transform: translateX(3px);
        }

        @media (max-width: 576px) {
            .stats-card .stats-value {
                font-size: 1.25rem;
            }

            .stats-card .icon {
                font-size: 1.5rem;
            }
        }
    </style>
</x-admin>
