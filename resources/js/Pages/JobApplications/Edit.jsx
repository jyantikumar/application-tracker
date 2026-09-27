import React, { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Card, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription, DialogFooter } from '@/components/ui/dialog';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { ArrowLeft, Plus, AlertCircle } from 'lucide-react';

export default function Edit({ JobApplication, CustomSources = [], CustomStatuses = [] }) {
    // Dynamic List States for Sources and Statuses
    const [sourcesList, setSourcesList] = useState(() => {
        const list = CustomSources.length ? [...CustomSources] : ['LinkedIn', 'Indeed', 'Referral'];
        if (JobApplication?.source && !list.includes(JobApplication.source)) {
            list.push(JobApplication.source);
        }
        return list;
    });

    const [statusesList, setStatusesList] = useState(() => {
        const list = CustomStatuses.length ? [...CustomStatuses] : ['Applied', 'Under Review', 'Interviewing'];
        if (JobApplication?.status && !list.includes(JobApplication.status)) {
            list.push(JobApplication.status);
        }
        return list;
    });

    // Modal Visibility States
    const [isAddSourceOpen, setIsAddSourceOpen] = useState(false);
    const [isAddStatusOpen, setIsAddStatusOpen] = useState(false);
    const [newSourceInput, setNewSourceInput] = useState('');
    const [newStatusInput, setNewStatusInput] = useState('');
    const [isAddingOption, setIsAddingOption] = useState(false);

    // Primary Application Form State pre-populated with JobApplication model
    const form = useForm({
        company_name: JobApplication?.company_name || '',
        position: JobApplication?.position || '',
        job_type: JobApplication?.job_type || 'Remote',
        city: JobApplication?.city || '',
        currency: JobApplication?.currency || 'USD',
        salary: JobApplication?.salary || '',
        salary_min: JobApplication?.salary_min || '',
        salary_max: JobApplication?.salary_max || '',
        pay_rate_type: JobApplication?.pay_rate_type || 'Hourly',
        hours_per_week: JobApplication?.hours_per_week || '40',
        ApplicationURL: JobApplication?.ApplicationURL || '',
        source: JobApplication?.source || sourcesList[0] || 'LinkedIn',
        status: JobApplication?.status || statusesList[0] || 'Applied',
        description: JobApplication?.description || '',
        pros: JobApplication?.pros || '',
        cons: JobApplication?.cons || '',
    });

    // Handle Main Form Update Submission
    const handleSubmit = (e) => {
        e.preventDefault();
        form.put(route('job-applications.update', JobApplication.id));
    };

    // Add Custom Source Handler
    const handleAddSource = async () => {
        const name = newSourceInput.trim();
        if (!name) return;

        setIsAddingOption(true);
        try {
            const response = await fetch(route('application-sources.store'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ name }),
            });

            if (!response.ok) throw new Error('Failed to create source');

            if (!sourcesList.includes(name)) {
                setSourcesList([...sourcesList, name]);
            }
            form.setData('source', name);
            setNewSourceInput('');
            setIsAddSourceOpen(false);
        } catch (err) {
            alert('Could not add source. Please try again.');
        } finally {
            setIsAddingOption(false);
        }
    };

    // Add Custom Status Handler
    const handleAddStatus = async () => {
        const name = newStatusInput.trim();
        if (!name) return;

        setIsAddingOption(true);
        try {
            const response = await fetch(route('application-statuses.store'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ name }),
            });

            if (!response.ok) throw new Error('Failed to create status');

            if (!statusesList.includes(name)) {
                setStatusesList([...statusesList, name]);
            }
            form.setData('status', name);
            setNewStatusInput('');
            setIsAddStatusOpen(false);
        } catch (err) {
            alert('Could not add status. Please try again.');
        } finally {
            setIsAddingOption(false);
        }
    };

    return (
        <AppLayout active="applications">
            <Head title={`Edit - ${JobApplication?.company_name || 'Application'}`} />

            <div className="max-w-6xl mx-auto space-y-6">
                {/* Page Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h1 className="text-2xl font-bold text-white tracking-tight">Edit Application</h1>
                        <p className="text-gray-400 text-sm mt-0.5">
                            Update details for{' '}
                            <span className="text-[#92BA56] font-semibold">{JobApplication?.company_name}</span> - {JobApplication?.position}
                        </p>
                    </div>
                    <Button asChild variant="ghost" className="text-xs text-gray-400 hover:text-white">
                        <Link href={route('job-applications.applications')}>
                            <ArrowLeft className="w-3.5 h-3.5 mr-1" /> Back to Applications
                        </Link>
                    </Button>
                </div>

                {/* Validation Errors Banner */}
                {Object.keys(form.errors).length > 0 && (
                    <Alert variant="destructive" className="bg-rose-500/10 border-rose-500/30 text-rose-400">
                        <AlertCircle className="h-4 w-4" />
                        <AlertTitle className="text-sm font-semibold">Please fix validation errors:</AlertTitle>
                        <AlertDescription className="mt-2 text-xs space-y-1">
                            {Object.entries(form.errors).map(([field, message]) => (
                                <p key={field}>• {message}</p>
                            ))}
                        </AlertDescription>
                    </Alert>
                )}

                {/* Main Edit Form Card */}
                <Card className="bg-gray-800/40 border-gray-700/60 shadow-xl backdrop-blur-sm">
                    <CardContent className="p-6 sm:p-8">
                        <form onSubmit={handleSubmit} className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-5">
                            
                            {/* Row 1: Company + Position + Work Setup + City */}
                            <div className="lg:col-span-4 space-y-2">
                                <Label className="text-xs uppercase tracking-wider text-gray-400 font-semibold">Company Name *</Label>
                                <Input
                                    value={form.data.company_name}
                                    onChange={e => form.setData('company_name', e.target.value)}
                                    placeholder="e.g. Stripe"
                                    className="bg-gray-900/60 border-gray-700/80 text-white focus:border-[#92BA56]"
                                    required
                                />
                            </div>

                            <div className="lg:col-span-4 space-y-2">
                                <Label className="text-xs uppercase tracking-wider text-gray-400 font-semibold">Position *</Label>
                                <Input
                                    value={form.data.position}
                                    onChange={e => form.setData('position', e.target.value)}
                                    placeholder="e.g. Frontend Developer"
                                    className="bg-gray-900/60 border-gray-700/80 text-white focus:border-[#92BA56]"
                                    required
                                />
                            </div>

                            <div className="lg:col-span-2 space-y-2">
                                <Label className="text-xs uppercase tracking-wider text-gray-400 font-semibold">Work Setup *</Label>
                                <Select value={form.data.job_type} onValueChange={v => form.setData('job_type', v)}>
                                    <SelectTrigger className="bg-gray-900/60 border-gray-700/80 text-white">
                                        <SelectValue />
                                    </SelectTrigger>
                                    <SelectContent className="bg-gray-800 border-gray-700 text-white">
                                        <SelectItem value="Remote">Remote</SelectItem>
                                        <SelectItem value="Hybrid">Hybrid</SelectItem>
                                        <SelectItem value="Onsite">Onsite</SelectItem>
                                        <SelectItem value="Contract">Contract</SelectItem>
                                        <SelectItem value="Freelance">Freelance</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>

                            <div className="lg:col-span-2 space-y-2">
                                <Label className="text-xs uppercase tracking-wider text-gray-400 font-semibold">City *</Label>
                                <Input
                                    value={form.data.city}
                                    onChange={e => form.setData('city', e.target.value)}
                                    placeholder="Manila / Remote"
                                    className="bg-gray-900/60 border-gray-700/80 text-white focus:border-[#92BA56]"
                                    required
                                />
                            </div>

                            {/* Row 2: Compensation Block */}
                            <div className="lg:col-span-12 p-4 rounded-xl bg-gray-900/40 border border-gray-700/50 space-y-4">
                                <div className="flex items-center justify-between">
                                    <h3 className="text-xs font-bold text-[#92BA56] uppercase tracking-wider">Compensation Log</h3>
                                    <span className="text-[11px] text-gray-400">
                                        {form.data.job_type === 'Remote' ? 'Exact Remote Rate Log' : 'Onsite / Hybrid Salary Range'}
                                    </span>
                                </div>

                                {form.data.job_type === 'Remote' ? (
                                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                        <div className="space-y-2">
                                            <Label className="text-xs uppercase tracking-wider text-gray-400 font-semibold">Currency *</Label>
                                            <Select value={form.data.currency} onValueChange={v => form.setData('currency', v)}>
                                                <SelectTrigger className="bg-gray-900/80 border-gray-700/80 text-white">
                                                    <SelectValue />
                                                </SelectTrigger>
                                                <SelectContent className="bg-gray-800 border-gray-700 text-white">
                                                    <SelectItem value="USD">USD ($)</SelectItem>
                                                    <SelectItem value="PHP">PHP (₱)</SelectItem>
                                                    <SelectItem value="EUR">EUR (€)</SelectItem>
                                                    <SelectItem value="GBP">GBP (£)</SelectItem>
                                                    <SelectItem value="AUD">AUD (A$)</SelectItem>
                                                </SelectContent>
                                            </Select>
                                        </div>

                                        <div className="space-y-2">
                                            <Label className="text-xs uppercase tracking-wider text-gray-400 font-semibold">Amount *</Label>
                                            <Input
                                                type="number"
                                                step="0.01"
                                                value={form.data.salary}
                                                onChange={e => form.setData('salary', e.target.value)}
                                                placeholder="e.g. 20 or 3000"
                                                className="bg-gray-900/80 border-gray-700/80 text-white focus:border-[#92BA56]"
                                                required
                                            />
                                        </div>

                                        <div className="space-y-2">
                                            <Label className="text-xs uppercase tracking-wider text-gray-400 font-semibold">Pay Frequency *</Label>
                                            <Select value={form.data.pay_rate_type} onValueChange={v => form.setData('pay_rate_type', v)}>
                                                <SelectTrigger className="bg-gray-900/80 border-gray-700/80 text-white">
                                                    <SelectValue />
                                                </SelectTrigger>
                                                <SelectContent className="bg-gray-800 border-gray-700 text-white">
                                                    <SelectItem value="Hourly">Hourly Rate</SelectItem>
                                                    <SelectItem value="Monthly">Monthly Rate</SelectItem>
                                                </SelectContent>
                                            </Select>
                                        </div>

                                        <div className="space-y-2">
                                            <Label className="text-xs uppercase tracking-wider text-gray-400 font-semibold">Hours / Week *</Label>
                                            <Input
                                                type="number"
                                                step="0.5"
                                                value={form.data.hours_per_week}
                                                onChange={e => form.setData('hours_per_week', e.target.value)}
                                                placeholder="Standard: 40"
                                                className="bg-gray-900/80 border-gray-700/80 text-white focus:border-[#92BA56]"
                                                required
                                            />
                                        </div>
                                    </div>
                                ) : (
                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div className="space-y-2">
                                            <Label className="text-xs uppercase tracking-wider text-gray-400 font-semibold">Minimum Monthly Salary (PHP ₱) *</Label>
                                            <Input
                                                type="number"
                                                step="0.01"
                                                value={form.data.salary_min}
                                                onChange={e => form.setData('salary_min', e.target.value)}
                                                placeholder="e.g. 25000"
                                                className="bg-gray-900/80 border-gray-700/80 text-white focus:border-[#92BA56]"
                                                required
                                            />
                                        </div>

                                        <div className="space-y-2">
                                            <Label className="text-xs uppercase tracking-wider text-gray-400 font-semibold">Maximum Monthly Salary (PHP ₱) *</Label>
                                            <Input
                                                type="number"
                                                step="0.01"
                                                value={form.data.salary_max}
                                                onChange={e => form.setData('salary_max', e.target.value)}
                                                placeholder="e.g. 35000"
                                                className="bg-gray-900/80 border-gray-700/80 text-white focus:border-[#92BA56]"
                                                required
                                            />
                                        </div>
                                    </div>
                                )}
                            </div>

                            {/* Row 3: Application URL + Source + Status */}
                            <div className="lg:col-span-6 space-y-2">
                                <Label className="text-xs uppercase tracking-wider text-gray-400 font-semibold">Application URL *</Label>
                                <Input
                                    type="url"
                                    value={form.data.ApplicationURL}
                                    onChange={e => form.setData('ApplicationURL', e.target.value)}
                                    placeholder="https://company.com/careers/123"
                                    className="bg-gray-900/60 border-gray-700/80 text-white focus:border-[#92BA56]"
                                    required
                                />
                            </div>

                            <div className="lg:col-span-3 space-y-2">
                                <div className="flex items-center justify-between">
                                    <Label className="text-xs uppercase tracking-wider text-gray-400 font-semibold">Source *</Label>
                                    <button
                                        type="button"
                                        onClick={() => setIsAddSourceOpen(true)}
                                        className="text-xs text-[#92BA56] hover:underline font-semibold flex items-center gap-0.5"
                                    >
                                        <Plus className="w-3 h-3" /> Add
                                    </button>
                                </div>
                                <Select value={form.data.source} onValueChange={v => form.setData('source', v)}>
                                    <SelectTrigger className="bg-gray-900/60 border-gray-700/80 text-white">
                                        <SelectValue />
                                    </SelectTrigger>
                                    <SelectContent className="bg-gray-800 border-gray-700 text-white">
                                        {sourcesList.map(src => (
                                            <SelectItem key={src} value={src}>{src}</SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>

                            <div className="lg:col-span-3 space-y-2">
                                <div className="flex items-center justify-between">
                                    <Label className="text-xs uppercase tracking-wider text-gray-400 font-semibold">Status *</Label>
                                    <button
                                        type="button"
                                        onClick={() => setIsAddStatusOpen(true)}
                                        className="text-xs text-[#92BA56] hover:underline font-semibold flex items-center gap-0.5"
                                    >
                                        <Plus className="w-3 h-3" /> Add
                                    </button>
                                </div>
                                <Select value={form.data.status} onValueChange={v => form.setData('status', v)}>
                                    <SelectTrigger className="bg-gray-900/60 border-gray-700/80 text-white">
                                        <SelectValue />
                                    </SelectTrigger>
                                    <SelectContent className="bg-gray-800 border-gray-700 text-white">
                                        {statusesList.map(st => (
                                            <SelectItem key={st} value={st}>{st}</SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>

                            {/* Row 4: Job Description */}
                            <div className="lg:col-span-12 space-y-2">
                                <Label className="text-xs uppercase tracking-wider text-gray-400 font-semibold">Job Description *</Label>
                                <Textarea
                                    rows={3}
                                    value={form.data.description}
                                    onChange={e => form.setData('description', e.target.value)}
                                    placeholder="Paste job description or requirements..."
                                    className="bg-gray-900/60 border-gray-700/80 text-white focus:border-[#92BA56]"
                                    required
                                />
                            </div>

                            {/* Row 5: Pros & Cons */}
                            <div className="lg:col-span-6 space-y-2">
                                <Label className="text-xs uppercase tracking-wider text-gray-400 font-semibold">Pros (Optional)</Label>
                                <Input
                                    value={form.data.pros}
                                    onChange={e => form.setData('pros', e.target.value)}
                                    placeholder="Good benefits, flexible schedule..."
                                    className="bg-gray-900/60 border-gray-700/80 text-white focus:border-[#92BA56]"
                                />
                            </div>

                            <div className="lg:col-span-6 space-y-2">
                                <Label className="text-xs uppercase tracking-wider text-gray-400 font-semibold">Cons (Optional)</Label>
                                <Input
                                    value={form.data.cons}
                                    onChange={e => form.setData('cons', e.target.value)}
                                    placeholder="Late working hours, high workload..."
                                    className="bg-gray-900/60 border-gray-700/80 text-white focus:border-[#92BA56]"
                                />
                            </div>

                            {/* Row 6: Actions */}
                            <div className="lg:col-span-12 flex items-center justify-end gap-3 pt-4 border-t border-gray-700/60">
                                <Button asChild variant="ghost" className="text-gray-400 hover:text-white">
                                    <Link href={route('job-applications.applications')}>Cancel</Link>
                                </Button>
                                <Button
                                    type="submit"
                                    disabled={form.processing}
                                    className="bg-[#92BA56] border-0 text-gray-900 font-bold hover:bg-[#82a74a] shadow-md shadow-[#92BA56]/20"
                                >
                                    Update Application
                                </Button>
                            </div>

                        </form>
                    </CardContent>
                </Card>
            </div>

            {/* Modal: Add Custom Source */}
            <Dialog open={isAddSourceOpen} onOpenChange={setIsAddSourceOpen}>
                <DialogContent className="bg-gray-900 border-gray-800 text-white max-w-md">
                    <DialogHeader>
                        <DialogTitle className="text-lg font-bold">Add Custom Job Source</DialogTitle>
                        <DialogDescription className="text-xs text-gray-400">
                            This source will be saved for future applications.
                        </DialogDescription>
                    </DialogHeader>

                    <div className="py-2">
                        <Input
                            value={newSourceInput}
                            onChange={e => setNewSourceInput(e.target.value)}
                            onKeyDown={e => e.key === 'Enter' && (e.preventDefault(), handleAddSource())}
                            placeholder="e.g. Wellfound, Discord..."
                            className="bg-gray-800 border-gray-700 text-white focus:border-[#92BA56]"
                        />
                    </div>

                    <DialogFooter className="gap-2">
                        <Button variant="outline" onClick={() => setIsAddSourceOpen(false)} className="border-gray-700 text-gray-300">
                            Cancel
                        </Button>
                        <Button onClick={handleAddSource} disabled={isAddingOption} className="bg-[#92BA56] text-gray-950 font-bold hover:bg-[#82a74a]">
                            Add Source
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>

            {/* Modal: Add Custom Status */}
            <Dialog open={isAddStatusOpen} onOpenChange={setIsAddStatusOpen}>
                <DialogContent className="bg-gray-900 border-gray-800 text-white max-w-md">
                    <DialogHeader>
                        <DialogTitle className="text-lg font-bold">Add Custom Application Status</DialogTitle>
                        <DialogDescription className="text-xs text-gray-400">
                            This status will be saved for future applications.
                        </DialogDescription>
                    </DialogHeader>

                    <div className="py-2">
                        <Input
                            value={newStatusInput}
                            onChange={e => setNewStatusInput(e.target.value)}
                            onKeyDown={e => e.key === 'Enter' && (e.preventDefault(), handleAddStatus())}
                            placeholder="e.g. Assessment Sent, Negotiation..."
                            className="bg-gray-800 border-gray-700 text-white focus:border-[#92BA56]"
                        />
                    </div>

                    <DialogFooter className="gap-2">
                        <Button variant="outline" onClick={() => setIsAddStatusOpen(false)} className="border-gray-700 text-gray-300">
                            Cancel
                        </Button>
                        <Button onClick={handleAddStatus} disabled={isAddingOption} className="bg-[#92BA56] text-gray-950 font-bold hover:bg-[#82a74a]">
                            Add Status
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>

        </AppLayout>
    );
}