<x-app-layout>
    <div class="flex flex-col min-h-screen" x-data="FormHandler()"
         x-effect="document.body.classList.toggle('overflow-hidden', IsAddSourceModalOpen || IsAddStatusModalOpen)"
         @keydown.escape.window="IsAddSourceModalOpen = false; IsAddStatusModalOpen = false;">

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
            <a href="{{ route('job-applications.applications') }}" class="btn btn-xs btn-outline border-gray-700 text-gray-300 hover:bg-gray-800 hover:text-white">Back</a>
        </header>

        <!-- Main Content Wrapper -->
        <main class="lg:pl-72 flex-1 max-w-6xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

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
                <form action="{{ route('job-applications.update', $JobApplication->id) }}" method="POST"
                      class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-5">
                    @csrf
                    @method('PUT')

                    <!-- Row 1: Company + Position + Job Type + City -->
                    <div class="lg:col-span-4">
                        <label class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">Company Name *</label>
                        <input type="text" name="company_name" value="{{ old('company_name', $JobApplication->company_name) }}" required
                               class="input w-full bg-gray-900/60 border border-gray-700/80 rounded-lg px-4 py-2.5 text-gray-100 text-sm focus:border-[#92BA56] focus:outline-none transition-colors"
                               placeholder="e.g. Stripe">
                    </div>
                    <div class="lg:col-span-4">
                        <label class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">Position *</label>
                        <input type="text" name="position" value="{{ old('position', $JobApplication->position) }}" required
                               class="input w-full bg-gray-900/60 border border-gray-700/80 rounded-lg px-4 py-2.5 text-gray-100 text-sm focus:border-[#92BA56] focus:outline-none transition-colors"
                               placeholder="e.g. Frontend Developer">
                    </div>
                    <div class="lg:col-span-2">
                        <label class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">Work Setup *</label>
                        <select name="job_type" x-model="JobType" required
                                class="select w-full bg-gray-900/60 border border-gray-700/80 rounded-lg px-4 py-2.5 text-gray-100 text-sm focus:border-[#92BA56] focus:outline-none transition-colors">
                            <option value="Remote">Remote</option>
                            <option value="Hybrid">Hybrid</option>
                            <option value="Onsite">Onsite</option>
                            <option value="Contract">Contract</option>
                            <option value="Freelance">Freelance</option>
                        </select>
                    </div>
                    <div class="lg:col-span-2">
                        <label class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">City *</label>
                        <input type="text" name="city" value="{{ old('city', $JobApplication->city) }}" required
                               class="input w-full bg-gray-900/60 border border-gray-700/80 rounded-lg px-4 py-2.5 text-gray-100 text-sm focus:border-[#92BA56] focus:outline-none transition-colors"
                               placeholder="Manila / Remote">
                    </div>

                    <!-- Row 2: Compensation Block (full width) -->
                    <div class="lg:col-span-12 p-4 rounded-xl bg-gray-900/40 border border-gray-700/50 space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xs font-bold text-[#92BA56] uppercase tracking-wider">Compensation Log</h3>
                            <span class="text-[11px] text-gray-400" x-text="JobType === 'Remote' ? 'Exact Remote Rate Log' : 'Onsite / Hybrid Salary Range'"></span>
                        </div>

                        <!-- Remote: 4 fields in one row -->
                        <template x-if="JobType === 'Remote'">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">Currency *</label>
                                    <select name="currency" required class="select w-full bg-gray-900/80 border border-gray-700/80 rounded-lg px-4 py-2.5 text-gray-100 text-sm focus:border-[#92BA56] focus:outline-none">
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
                                           class="input w-full bg-gray-900/80 border border-gray-700/80 rounded-lg px-4 py-2.5 text-gray-100 text-sm focus:border-[#92BA56] focus:outline-none"
                                           placeholder="e.g. 20 or 3000">
                                </div>
                                <div>
                                    <label class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">Pay Frequency *</label>
                                    <select name="pay_rate_type" required class="select w-full bg-gray-900/80 border border-gray-700/80 rounded-lg px-4 py-2.5 text-gray-100 text-sm focus:border-[#92BA56] focus:outline-none">
                                        <option value="Hourly" {{ old('pay_rate_type', $JobApplication->pay_rate_type) === 'Hourly' ? 'selected' : '' }}>Hourly Rate</option>
                                        <option value="Monthly" {{ old('pay_rate_type', $JobApplication->pay_rate_type) === 'Monthly' ? 'selected' : '' }}>Monthly Rate</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">Hours / Week *</label>
                                    <input type="number" step="0.5" name="hours_per_week" value="{{ old('hours_per_week', $JobApplication->hours_per_week ?: 40) }}" required
                                           class="input w-full bg-gray-900/80 border border-gray-700/80 rounded-lg px-4 py-2.5 text-gray-100 text-sm focus:border-[#92BA56] focus:outline-none"
                                           placeholder="Standard: 40">
                                </div>
                                <input type="hidden" name="salary_min" value="">
                                <input type="hidden" name="salary_max" value="">
                            </div>
                        </template>

                        <!-- Onsite: 2 fields in one row -->
                        <template x-if="JobType !== 'Remote'">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">Minimum Monthly Salary (PHP ₱) *</label>
                                    <input type="number" step="0.01" name="salary_min" value="{{ old('salary_min', $JobApplication->salary_min) }}" required
                                           class="input w-full bg-gray-900/80 border border-gray-700/80 rounded-lg px-4 py-2.5 text-gray-100 text-sm focus:border-[#92BA56] focus:outline-none"
                                           placeholder="e.g. 25000">
                                </div>
                                <div>
                                    <label class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">Maximum Monthly Salary (PHP ₱) *</label>
                                    <input type="number" step="0.01" name="salary_max" value="{{ old('salary_max', $JobApplication->salary_max) }}" required
                                           class="input w-full bg-gray-900/80 border border-gray-700/80 rounded-lg px-4 py-2.5 text-gray-100 text-sm focus:border-[#92BA56] focus:outline-none"
                                           placeholder="e.g. 35000">
                                </div>
                                <input type="hidden" name="salary" value="">
                                <input type="hidden" name="currency" value="PHP">
                                <input type="hidden" name="pay_rate_type" value="Monthly">
                                <input type="hidden" name="hours_per_week" value="40">
                            </div>
                        </template>
                    </div>

                    <!-- Row 3: URL + Source + Status -->
                    <div class="lg:col-span-6">
                        <label class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">Application URL *</label>
                        <input type="url" name="ApplicationURL" value="{{ old('ApplicationURL', $JobApplication->ApplicationURL) }}" required
                               class="input w-full bg-gray-900/60 border border-gray-700/80 rounded-lg px-4 py-2.5 text-gray-100 text-sm focus:border-[#92BA56] focus:outline-none transition-colors"
                               placeholder="https://company.com/careers/123">
                    </div>
                    <div class="lg:col-span-3">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-xs uppercase tracking-wider text-gray-400 font-semibold">Source *</label>
                            <button type="button" @click="IsAddSourceModalOpen = true" class="text-xs text-[#92BA56] hover:underline font-semibold">+ Add</button>
                        </div>
                        <select name="source" x-model="SelectedSource" required class="select w-full bg-gray-900/60 border border-gray-700/80 rounded-lg px-4 py-2.5 text-gray-100 text-sm focus:border-[#92BA56] focus:outline-none transition-colors">
                            <template x-for="Source in SourcesList" :key="Source">
                                <option :value="Source" x-text="Source" :selected="Source === SelectedSource"></option>
                            </template>
                        </select>
                    </div>
                    <div class="lg:col-span-3">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-xs uppercase tracking-wider text-gray-400 font-semibold">Status *</label>
                            <button type="button" @click="IsAddStatusModalOpen = true" class="text-xs text-[#92BA56] hover:underline font-semibold">+ Add</button>
                        </div>
                        <select name="status" x-model="SelectedStatus" required class="select w-full bg-gray-900/60 border border-gray-700/80 rounded-lg px-4 py-2.5 text-gray-100 text-sm focus:border-[#92BA56] focus:outline-none transition-colors">
                            <template x-for="Status in StatusesList" :key="Status">
                                <option :value="Status" x-text="Status" :selected="Status === SelectedStatus"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Row 4: Description -->
                    <div class="lg:col-span-12">
                        <label class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">Job Description *</label>
                        <textarea name="description" rows="3" required class="textarea w-full bg-gray-900/60 border border-gray-700/80 rounded-lg px-4 py-2.5 text-gray-100 text-sm focus:border-[#92BA56] focus:outline-none transition-colors">{{ old('description', $JobApplication->description) }}</textarea>
                    </div>

                    <!-- Row 5: Pros + Cons -->
                    <div class="lg:col-span-6">
                        <label class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">Pros (Optional)</label>
                        <input type="text" name="pros" value="{{ old('pros', $JobApplication->pros) }}" class="input w-full bg-gray-900/60 border border-gray-700/80 rounded-lg px-4 py-2.5 text-gray-100 text-sm focus:border-[#92BA56] focus:outline-none transition-colors">
                    </div>
                    <div class="lg:col-span-6">
                        <label class="block text-xs uppercase tracking-wider text-gray-400 font-semibold mb-2">Cons (Optional)</label>
                        <input type="text" name="cons" value="{{ old('cons', $JobApplication->cons) }}" class="input w-full bg-gray-900/60 border border-gray-700/80 rounded-lg px-4 py-2.5 text-gray-100 text-sm focus:border-[#92BA56] focus:outline-none transition-colors">
                    </div>

                    <!-- Row 6: Actions -->
                    <div class="lg:col-span-12 flex items-center justify-end gap-3 pt-4 border-t border-gray-700/60">
                        <a href="{{ route('job-applications.applications') }}"
                           class="btn btn-sm btn-ghost text-gray-400 hover:text-white">Cancel</a>
                        <button type="submit"
                                class="btn btn-sm bg-[#92BA56] border-0 text-gray-900 hover:bg-[#82a74a] shadow-md shadow-[#92BA56]/20">
                            Update Application
                        </button>
                    </div>
                </form>
            </div>
        </main>

        <!-- ============================================================
             Modal: Add Custom Source
             ============================================================ -->
        <div x-show="IsAddSourceModalOpen" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4"
             role="dialog" aria-modal="true" aria-labelledby="add-source-title">
            <div x-show="IsAddSourceModalOpen" x-transition.opacity @click="IsAddSourceModalOpen = false"
                 class="absolute inset-0 bg-gray-950/80 backdrop-blur-sm"></div>

            <div x-show="IsAddSourceModalOpen" x-transition
                 class="relative z-10 max-w-md w-full bg-gray-900 border border-gray-800 rounded-2xl shadow-2xl p-6 space-y-4">

                <div class="flex items-start justify-between">
                    <div>
                        <h3 id="add-source-title" class="text-lg font-bold text-white">Add Custom Job Source</h3>
                        <p class="text-xs text-gray-400 mt-0.5">This source will be saved for future applications.</p>
                    </div>
                    <button type="button" @click="IsAddSourceModalOpen = false"
                            class="btn btn-sm btn-circle btn-text text-gray-400 hover:text-white" aria-label="Close">&times;</button>
                </div>

                <input type="text" x-model="NewSourceInput" @keydown.enter.prevent="addSource()"
                       class="input w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-sm text-white focus:outline-none focus:border-[#92BA56]"
                       placeholder="e.g. Wellfound, Discord...">

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="IsAddSourceModalOpen = false"
                            class="btn btn-sm btn-outline border-gray-700 text-gray-300 hover:bg-gray-800 hover:text-white">
                        Cancel
                    </button>
                    <button type="button" @click="addSource()"
                            class="btn btn-sm bg-[#92BA56] border-0 text-gray-950 hover:bg-[#82a74a]">
                        Add Source
                    </button>
                </div>
            </div>
        </div>

        <!-- ============================================================
             Modal: Add Custom Status
             ============================================================ -->
        <div x-show="IsAddStatusModalOpen" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4"
             role="dialog" aria-modal="true" aria-labelledby="add-status-title">
            <div x-show="IsAddStatusModalOpen" x-transition.opacity @click="IsAddStatusModalOpen = false"
                 class="absolute inset-0 bg-gray-950/80 backdrop-blur-sm"></div>

            <div x-show="IsAddStatusModalOpen" x-transition
                 class="relative z-10 max-w-md w-full bg-gray-900 border border-gray-800 rounded-2xl shadow-2xl p-6 space-y-4">

                <div class="flex items-start justify-between">
                    <div>
                        <h3 id="add-status-title" class="text-lg font-bold text-white">Add Custom Application Status</h3>
                        <p class="text-xs text-gray-400 mt-0.5">This status will be saved for future applications.</p>
                    </div>
                    <button type="button" @click="IsAddStatusModalOpen = false"
                            class="btn btn-sm btn-circle btn-text text-gray-400 hover:text-white" aria-label="Close">&times;</button>
                </div>

                <input type="text" x-model="NewStatusInput" @keydown.enter.prevent="addStatus()"
                       class="input w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-sm text-white focus:outline-none focus:border-[#92BA56]"
                       placeholder="e.g. Assessment Sent, Negotiation...">

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="IsAddStatusModalOpen = false"
                            class="btn btn-sm btn-outline border-gray-700 text-gray-300 hover:bg-gray-800 hover:text-white">
                        Cancel
                    </button>
                    <button type="button" @click="addStatus()"
                            class="btn btn-sm bg-[#92BA56] border-0 text-gray-950 hover:bg-[#82a74a]">
                        Add Status
                    </button>
                </div>
            </div>
        </div>

    </div>

    <script>
        function FormHandler() {
            return {
                JobType: '{{ old('job_type', $JobApplication->job_type) }}',
                SelectedSource: '{{ old('source', $JobApplication->source) }}',
                SelectedStatus: '{{ old('status', $JobApplication->status) }}',
                SourcesList: @json($CustomSources ?? []),
                StatusesList: @json($CustomStatuses ?? []),
                IsAddSourceModalOpen: false,
                IsAddStatusModalOpen: false,
                NewSourceInput: '',
                NewStatusInput: '',

                async addSource() {
    const Name = this.NewSourceInput.trim();
    if (!Name) return;

    try {
        const Res = await fetch('{{ route('application-sources.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ name: Name }),
        });

        if (!Res.ok) throw new Error('Request failed');

        if (!this.SourcesList.includes(Name)) this.SourcesList.push(Name);
        this.SelectedSource = Name;
        this.NewSourceInput = '';
        this.IsAddSourceModalOpen = false;

        window.toast.success('Source added', `"${Name}" is now selectable.`);
    } catch (err) {
        window.toast.error('Could not add source', 'Please check your connection and try again.');
    }
},

async addStatus() {
    const Name = this.NewStatusInput.trim();
    if (!Name) return;

    try {
        const Res = await fetch('{{ route('application-statuses.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ name: Name }),
        });

        if (!Res.ok) throw new Error('Request failed');

        if (!this.StatusesList.includes(Name)) this.StatusesList.push(Name);
        this.SelectedStatus = Name;
        this.NewStatusInput = '';
        this.IsAddStatusModalOpen = false;

        window.toast.success('Status added', `"${Name}" is now selectable.`);
    } catch (err) {
        window.toast.error('Could not add status', 'Please check your connection and try again.');
    }
},
            }
        }
    </script>
</x-app-layout>