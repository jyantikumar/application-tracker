<x-app-layout>
    <div class="flex flex-col min-h-screen">
        
        <!-- Mobile Header -->
        <header class="lg:hidden bg-gray-800/80 backdrop-blur-md border-b border-gray-700/50 p-4 sticky top-0 z-40 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="p-1.5 bg-[#92BA56]/20 rounded-lg border border-[#92BA56]/30">
                    <svg class="w-5 h-5 text-[#92BA56]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </div>
                <span class="font-bold text-lg tracking-tight text-white">Edit Application</span>
            </div>
            <a href="{{ route('job-applications.applications') }}" class="px-2.5 py-1.5 text-xs font-medium text-gray-300 bg-gray-800 rounded-lg border border-gray-700">Back</a>
        </header>

        <!-- Main Content Wrapper -->
        <main class="lg:pl-72 flex-1 max-w-4xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6" x-data="{ JobType: '{{ old('job_type', $JobApplication->job_type) }}' }">

            <!-- Page Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-white tracking-tight">Edit Application</h1>
                    <p class="text-gray-400 text-sm mt-0.5">Update details for <span class="text-[#92BA56] font-semibold">{{ $JobApplication->company_name }}</span> - {{ $JobApplication->position }}</p>
                </div>
                <a href="{{ route('job-applications.applications') }}" class="hidden sm:inline-flex items-center gap-1.5 text-xs font-medium text-gray-400 hover:text-white transition-colors">
                    &larr; Back to Applications
                </a>
            </div>

            @if($errors->any())
                <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-400">
                    <div class="font-semibold text-sm mb-2">Please fix validation errors:</div>
                    <ul class="list-disc list-inside text-xs space-y-1">
                        @foreach($errors->all() as $Error)
                            <li>{{ $Error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-gray-800/40 backdrop-blur-sm border border-gray-700/60 rounded-xl p-6 sm:p-8 shadow-xl">
                <form action="{{ route('job-applications.update', $JobApplication->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Company & Position -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">Company Name *</label>
                            <input type="text" name="company_name" value="{{ old('company_name', $JobApplication->company_name) }}" required
                                   class="w-full bg-gray-900/60 border border-gray-700/80 rounded-lg px-4 py-2.5 text-gray-100 text-sm focus:border-[#92BA56] focus:outline-none transition-colors"
                                   placeholder="e.g. Stripe">
                        </div>
                        <div>
                            <label class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">Position *</label>
                            <input type="text" name="position" value="{{ old('position', $JobApplication->position) }}" required
                                   class="w-full bg-gray-900/60 border border-gray-700/80 rounded-lg px-4 py-2.5 text-gray-100 text-sm focus:border-[#92BA56] focus:outline-none transition-colors"
                                   placeholder="e.g. Frontend Developer">
                        </div>
                    </div>

                    <!-- Work Setup & Location -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">Work Setup / Job Type *</label>
                            <select name="job_type" x-model="JobType" required
                                    class="w-full bg-gray-900/60 border border-gray-700/80 rounded-lg px-4 py-2.5 text-gray-100 text-sm focus:border-[#92BA56] focus:outline-none transition-colors">
                                <option value="Remote">Remote / Online</option>
                                <option value="Hybrid">Hybrid</option>
                                <option value="Onsite">Onsite</option>
                                <option value="Contract">Contract</option>
                                <option value="Freelance">Freelance</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">City / Location *</label>
                            <input type="text" name="city" value="{{ old('city', $JobApplication->city) }}" required
                                   class="w-full bg-gray-900/60 border border-gray-700/80 rounded-lg px-4 py-2.5 text-gray-100 text-sm focus:border-[#92BA56] focus:outline-none transition-colors"
                                   placeholder="e.g. Manila / Remote">
                        </div>
                    </div>

                    <!-- Dynamic Compensation Section -->
                    <div class="p-4 rounded-xl bg-gray-900/40 border border-gray-700/50 space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xs font-bold text-[#92BA56] uppercase tracking-wider">Compensation Log</h3>
                            <span class="text-[11px] text-gray-400" x-text="JobType === 'Remote' ? 'Exact Remote Rate Log' : 'Onsite / Hybrid Salary Range'"></span>
                        </div>

                        <!-- Remote Dynamic Compensation -->
                        <template x-if="JobType === 'Remote'">
                            <div class="space-y-4">
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">Currency *</label>
                                        <select name="currency" required class="w-full bg-gray-900/80 border border-gray-700/80 rounded-lg px-4 py-2.5 text-gray-100 text-sm focus:border-[#92BA56] focus:outline-none">
                                            <option value="USD" {{ old('currency', $JobApplication->currency) === 'USD' ? 'selected' : '' }}>USD ($)</option>
                                            <option value="PHP" {{ old('currency', $JobApplication->currency) === 'PHP' ? 'selected' : '' }}>PHP (₱)</option>
                                            <option value="EUR" {{ old('currency', $JobApplication->currency) === 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                                            <option value="GBP" {{ old('currency', $JobApplication->currency) === 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                                            <option value="AUD" {{ old('currency', $JobApplication->currency) === 'AUD' ? 'selected' : '' }}>AUD (A$)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">Amount *</label>
                                        <input type="number" step="0.01" name="salary" value="{{ old('salary', $JobApplication->salary) }}" required
                                               class="w-full bg-gray-900/80 border border-gray-700/80 rounded-lg px-4 py-2.5 text-gray-100 text-sm focus:border-[#92BA56] focus:outline-none"
                                               placeholder="e.g. 20 or 3000">
                                    </div>
                                    <div>
                                        <label class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">Pay Frequency *</label>
                                        <select name="pay_rate_type" required class="w-full bg-gray-900/80 border border-gray-700/80 rounded-lg px-4 py-2.5 text-gray-100 text-sm focus:border-[#92BA56] focus:outline-none">
                                            <option value="Hourly" {{ old('pay_rate_type', $JobApplication->pay_rate_type) === 'Hourly' ? 'selected' : '' }}>Hourly Rate</option>
                                            <option value="Monthly" {{ old('pay_rate_type', $JobApplication->pay_rate_type) === 'Monthly' ? 'selected' : '' }}>Monthly Rate</option>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">Hours Per Week *</label>
                                    <input type="number" step="0.5" name="hours_per_week" value="{{ old('hours_per_week', $JobApplication->hours_per_week ?: 40) }}" required
                                           class="w-full bg-gray-900/80 border border-gray-700/80 rounded-lg px-4 py-2.5 text-gray-100 text-sm focus:border-[#92BA56] focus:outline-none"
                                           placeholder="Standard: 40">
                                </div>
                                <input type="hidden" name="salary_min" value="">
                                <input type="hidden" name="salary_max" value="">
                            </div>
                        </template>

                        <!-- Onsite Range Salary Logging -->
                        <template x-if="JobType !== 'Remote'">
                            <div class="space-y-4">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">Minimum Monthly Salary (PHP ₱) *</label>
                                        <input type="number" step="0.01" name="salary_min" value="{{ old('salary_min', $JobApplication->salary_min) }}" required
                                               class="w-full bg-gray-900/80 border border-gray-700/80 rounded-lg px-4 py-2.5 text-gray-100 text-sm focus:border-[#92BA56] focus:outline-none"
                                               placeholder="e.g. 25000">
                                    </div>
                                    <div>
                                        <label class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">Maximum Monthly Salary (PHP ₱) *</label>
                                        <input type="number" step="0.01" name="salary_max" value="{{ old('salary_max', $JobApplication->salary_max) }}" required
                                               class="w-full bg-gray-900/80 border border-gray-700/80 rounded-lg px-4 py-2.5 text-gray-100 text-sm focus:border-[#92BA56] focus:outline-none"
                                               placeholder="e.g. 35000">
                                    </div>
                                </div>
                                <input type="hidden" name="salary" value="">
                                <input type="hidden" name="currency" value="PHP">
                                <input type="hidden" name="pay_rate_type" value="Monthly">
                                <input type="hidden" name="hours_per_week" value="40">
                            </div>
                        </template>
                    </div>

                    <!-- URL & Source -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">Application URL *</label>
                            <input type="url" name="ApplicationURL" value="{{ old('ApplicationURL', $JobApplication->ApplicationURL) }}" required
                                   class="w-full bg-gray-900/60 border border-gray-700/80 rounded-lg px-4 py-2.5 text-gray-100 text-sm focus:border-[#92BA56] focus:outline-none transition-colors"
                                   placeholder="https://company.com/careers/123">
                        </div>
                        <div>
                            <label class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">Source *</label>
                            <select name="source" required class="w-full bg-gray-900/60 border border-gray-700/80 rounded-lg px-4 py-2.5 text-gray-100 text-sm focus:border-[#92BA56] focus:outline-none transition-colors">
                              @foreach([
    'BossJob',
    'Career Fair',
    'Career Page',
    'Company Website',
    'Direct',
    'Facebook',
    'Indeed',
    'JobStreet',
    'JobTarget',
    'LinkedIn',
    'OLJ' => 'OnlineJobs.ph (OLJ)',
    'Prosple',
    'Referral'
] as $ValueOption => $LabelOption)
    @php
        $SourceValue = is_numeric($ValueOption) ? $LabelOption : $ValueOption;
    @endphp
    <option value="{{ $SourceValue }}" {{ old('source', $JobApplication->source) === $SourceValue ? 'selected' : '' }}>
        {{ $LabelOption }}
    </option>
@endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">Status *</label>
                        <select name="status" required class="w-full bg-gray-900/60 border border-gray-700/80 rounded-lg px-4 py-2.5 text-gray-100 text-sm focus:border-[#92BA56] focus:outline-none transition-colors">
                            @foreach(['Applied', 'Under Review', 'Currently Interviewing', 'Waiting response', 'Offered', 'Hired', 'Rejected', 'No Response'] as $StatusOption)
                                <option value="{{ $StatusOption }}" {{ old('status', $JobApplication->status) === $StatusOption ? 'selected' : '' }}>
                                    {{ $StatusOption }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">Job Description *</label>
                        <textarea name="description" rows="4" required class="w-full bg-gray-900/60 border border-gray-700/80 rounded-lg px-4 py-2.5 text-gray-100 text-sm focus:border-[#92BA56] focus:outline-none transition-colors">{{ old('description', $JobApplication->description) }}</textarea>
                    </div>

                    <!-- Pros & Cons -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">Pros (Optional)</label>
                            <input type="text" name="pros" value="{{ old('pros', $JobApplication->pros) }}" class="w-full bg-gray-900/60 border border-gray-700/80 rounded-lg px-4 py-2.5 text-gray-100 text-sm focus:border-[#92BA56] focus:outline-none transition-colors">
                        </div>
                        <div>
                            <label class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">Cons (Optional)</label>
                            <input type="text" name="cons" value="{{ old('cons', $JobApplication->cons) }}" class="w-full bg-gray-900/60 border border-gray-700/80 rounded-lg px-4 py-2.5 text-gray-100 text-sm focus:border-[#92BA56] focus:outline-none transition-colors">
                        </div>
                    </div>

                    <!-- Form Action Buttons -->
                    <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-700/60">
                        <a href="{{ route('job-applications.applications') }}" class="px-5 py-2.5 text-sm font-medium text-gray-400 hover:text-white transition-colors">Cancel</a>
                        <button type="submit" class="px-6 py-2.5 text-sm font-semibold text-gray-900 bg-[#92BA56] hover:bg-[#82a74a] rounded-lg transition-colors shadow-md shadow-[#92BA56]/20">
                            Update Application
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</x-app-layout>