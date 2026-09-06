<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Job Application Tracker</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Chart.js DataLabels Plugin CDN for Percentage Overlay -->
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen font-sans pb-12">

    <!-- Top Navigation Bar -->
    <nav class="bg-gray-800/80 backdrop-blur-md border-b border-gray-700/50 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-8">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-[#92BA56]/20 rounded-lg border border-[#92BA56]/30">
                            <svg class="w-6 h-6 text-[#92BA56]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <span class="font-bold text-xl tracking-tight text-white">Job Tracker</span>
                    </div>

                    <!-- Navigation Links -->
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('job-applications.index') }}" 
                           class="px-3 py-2 rounded-lg text-sm font-semibold transition-colors bg-gray-900 text-[#92BA56] border border-[#92BA56]/30">
                            Dashboard
                        </a>
                        <a href="{{ route('job-applications.applications') }}" 
                           class="px-3 py-2 rounded-lg text-sm font-semibold transition-colors text-gray-300 hover:text-white hover:bg-gray-700/50">
                            Applications
                        </a>
                    </div>
                </div>

                <a href="{{ route('job-applications.create') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-gray-900 bg-[#92BA56] hover:bg-[#82a74a] rounded-lg transition-colors shadow-md shadow-[#92BA56]/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Log Application
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

        <!-- Notification Banner -->
        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 flex items-center justify-between">
                <span>{{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-200">&times;</button>
            </div>
        @endif

        <!-- KPI Metrics Summary Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3">
            <div class="bg-gray-800/60 border border-gray-700/60 rounded-xl p-3 text-center">
                <span class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Total Application</span>
                <span class="text-2xl font-black text-white mt-1 block">{{ $TotalApplications }}</span>
            </div>
            <div class="bg-gray-800/60 border border-gray-700/60 rounded-xl p-3 text-center">
                <span class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Interviews Done</span>
                <span class="text-2xl font-black text-amber-400 mt-1 block">{{ $InterviewsDone }}</span>
            </div>
            <div class="bg-gray-800/60 border border-gray-700/60 rounded-xl p-3 text-center">
                <span class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Applied</span>
                <span class="text-2xl font-black text-blue-400 mt-1 block">{{ $AppliedCount }}</span>
            </div>
            <div class="bg-gray-800/60 border border-gray-700/60 rounded-xl p-3 text-center">
                <span class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Under Review</span>
                <span class="text-2xl font-black text-indigo-400 mt-1 block">{{ $UnderReviewCount }}</span>
            </div>
            <div class="bg-gray-800/60 border border-gray-700/60 rounded-xl p-3 text-center">
                <span class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Interviewing</span>
                <span class="text-2xl font-black text-purple-400 mt-1 block">{{ $CurrentlyInterviewing }}</span>
            </div>
            <div class="bg-gray-800/60 border border-gray-700/60 rounded-xl p-3 text-center">
                <span class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Hired</span>
                <span class="text-2xl font-black text-[#92BA56] mt-1 block">{{ $HiredCount }}</span>
            </div>
            <div class="bg-gray-800/60 border border-gray-700/60 rounded-xl p-3 text-center">
                <span class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Rejected</span>
                <span class="text-2xl font-black text-rose-400 mt-1 block">{{ $RejectedCount }}</span>
            </div>
            <div class="bg-gray-800/60 border border-gray-700/60 rounded-xl p-3 text-center">
                <span class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Waiting Response</span>
                <span class="text-2xl font-black text-sky-400 mt-1 block">{{ $WaitingResponse }}</span>
            </div>
        </div>

        <!-- Charts Grid Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Horizontal Bar: Applications by Source -->
            <div class="bg-gray-800/40 border border-gray-700/60 rounded-xl p-5 backdrop-blur-sm">
                <h3 class="text-sm font-bold text-gray-200 mb-4 uppercase tracking-wider">Applications by Source</h3>
                <div class="h-64"><canvas id="SourceChart"></canvas></div>
            </div>

            <!-- Line Chart: Applications over Time -->
            <div class="bg-gray-800/40 border border-gray-700/60 rounded-xl p-5 backdrop-blur-sm">
                <h3 class="text-sm font-bold text-gray-200 mb-4 uppercase tracking-wider">Applications over Time</h3>
                <div class="h-64"><canvas id="DateChart"></canvas></div>
            </div>

            <!-- Doughnut Chart: Applications by Work Setup with Percentages -->
            <div class="bg-gray-800/40 border border-gray-700/60 rounded-xl p-5 backdrop-blur-sm">
                <h3 class="text-sm font-bold text-gray-200 mb-4 uppercase tracking-wider">Applications by Work Setup</h3>
                <div class="h-64 flex items-center justify-center">
                    <canvas id="WorkSetupChart"></canvas>
                </div>
            </div>

        </div>

        <!-- Location Bar Chart -->
        <div class="bg-gray-800/40 border border-gray-700/60 rounded-xl p-5 backdrop-blur-sm">
            <h3 class="text-sm font-bold text-gray-200 mb-4 uppercase tracking-wider">Applications by Location</h3>
            <div class="h-64"><canvas id="LocationChart"></canvas></div>
        </div>

        <!-- Top 10 Recent Applications Table -->
        <div class="bg-gray-800/40 border border-gray-700/60 rounded-xl shadow-xl overflow-hidden">
            <div class="p-4 border-b border-gray-700/60 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-white">Top 10 Recent Applications</h3>
                    <p class="text-xs text-gray-400">Latest logged records</p>
                </div>
                <a href="{{ route('job-applications.applications') }}" class="text-xs font-semibold text-[#92BA56] hover:underline">
                    View All Applications &rarr;
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-300">
                    <thead class="bg-gray-800/80 border-b border-gray-700/60 text-xs uppercase tracking-wider text-gray-400 font-semibold">
                        <tr>
                            <th class="px-6 py-4">Company & Position</th>
                            <th class="px-6 py-4">Type & City</th>
                            <th class="px-6 py-4">Salary</th>
                            <th class="px-6 py-4">Source</th>
                            <th class="px-6 py-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700/40">
                        @forelse($RecentApplications as $Application)
                            <tr class="hover:bg-gray-800/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-white text-base">{{ $Application->company_name }}</div>
                                    <div class="text-gray-400 text-xs">{{ $Application->position }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div>{{ $Application->job_type }}</div>
                                    <div class="text-gray-400 text-xs">{{ $Application->city }}</div>
                                </td>
                                <td class="px-6 py-4">${{ number_format($Application->salary, 2) }}</td>
                                <td class="px-6 py-4">{{ $Application->source }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium border bg-gray-700/50 text-gray-300 border-gray-600/30">
                                        {{ $Application->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-400">No applications recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <!-- Chart Configuration Script -->
    <script>
        const SourceData = @json($ApplicationsBySource);
        const DateData = @json($ApplicationsByDate);
        const WorkSetupData = @json($ApplicationsByJobType);
        const LocationData = @json($ApplicationsByLocation);

        // Register Chartjs DataLabels Plugin Globally
        Chart.register(ChartDataLabels);

        // 1. Applications by Source (Horizontal Bar)
        new Chart(document.getElementById('SourceChart'), {
            type: 'bar',
            data: {
                labels: Object.keys(SourceData),
                datasets: [{ data: Object.values(SourceData), backgroundColor: '#92BA56', borderRadius: 4 }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false }, datalabels: { display: false } },
                scales: {
                    x: { ticks: { color: '#9ca3af' }, grid: { color: '#374151' } },
                    y: { ticks: { color: '#9ca3af' }, grid: { display: false } }
                }
            }
        });

        // 2. Applications over Time (Line Chart)
        new Chart(document.getElementById('DateChart'), {
            type: 'line',
            data: {
                labels: Object.keys(DateData),
                datasets: [{
                    data: Object.values(DateData),
                    borderColor: '#38bdf8',
                    backgroundColor: 'rgba(56, 189, 248, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false }, datalabels: { display: false } },
                scales: {
                    x: { ticks: { color: '#9ca3af' }, grid: { color: '#374151' } },
                    y: { ticks: { color: '#9ca3af' }, grid: { color: '#374151' } }
                }
            }
        });

        // 3. Applications by Work Setup (Doughnut Chart with Percentage Labels)
        new Chart(document.getElementById('WorkSetupChart'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(WorkSetupData),
                datasets: [{
                    data: Object.values(WorkSetupData),
                    backgroundColor: ['#92BA56', '#38bdf8', '#7832a8', '#f59e0b', '#f43f5e'],
                    borderWidth: 2,
                    borderColor: '#1f2937'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#d1d5db', padding: 12, font: { size: 12, weight: '500' } }
                    },
                    datalabels: {
                        color: '#ffffff',
                        font: { weight: 'bold', size: 11 },
                        formatter: (Value, Context) => {
                            const Dataset = Context.chart.data.datasets[0];
                            const Total = Dataset.data.reduce((Acc, Val) => Acc + Number(Val), 0);
                            if (Total === 0) return '0%';
                            const Percentage = ((Value / Total) * 100).toFixed(1);
                            return `${Percentage}%`;
                        }
                    }
                },
                cutout: '60%'
            }
        });

        // 4. Applications by Location (Horizontal Bar)
        new Chart(document.getElementById('LocationChart'), {
            type: 'bar',
            data: {
                labels: Object.keys(LocationData),
                datasets: [{ data: Object.values(LocationData), backgroundColor: '#38bdf8', borderRadius: 4 }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false }, datalabels: { display: false } },
                scales: {
                    x: { ticks: { color: '#9ca3af' }, grid: { color: '#374151' } },
                    y: { ticks: { color: '#9ca3af' }, grid: { display: false } }
                }
            }
        });
    </script>
</body>
</html>