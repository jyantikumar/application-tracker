import React, { useState, useMemo } from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Card } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Sheet, SheetContent, SheetHeader, SheetTitle, SheetDescription } from '@/components/ui/sheet';
import { Tabs, TabsList, TabsTrigger, TabsContent } from '@/components/ui/tabs';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import {
    DropdownMenu,
    DropdownMenuCheckboxItem,
    DropdownMenuContent,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { 
    Briefcase, 
    MapPin, 
    Clock, 
    Globe, 
    ExternalLink, 
    Trash2, 
    Edit, 
    Check, 
    ChevronRight, 
    Search,
    Columns,
    Calendar
} from 'lucide-react';

export default function Applications({ JobApplications = [], CustomStatuses = [] }) {
    // Search, Filters & Drawer State
    const [searchTerm, setSearchTerm] = useState('');
    const [jobTypeFilter, setJobTypeFilter] = useState('ALL');
    const [statusFilter, setStatusFilter] = useState('ALL');
    const [selectedApp, setSelectedApp] = useState(null);
    const [isDrawerOpen, setIsDrawerOpen] = useState(false);
    const [activeTab, setActiveTab] = useState('general');

    // Column Display Toggles
    const [columnVisibility, setColumnVisibility] = useState({
        companyPosition: true,
        workSetup: true,
        compensation: true,
        dateApplied: true,
        interviewStatus: true,
        status: true,
    });

    // Inertia Form for Adding Interviews
    const interviewForm = useForm({
        interview_type: 'HR Screening',
        scheduled_at: '',
        status: 'Scheduled',
        notes: '',
    });

    // Filter Options
    const workSetups = useMemo(() => {
        return [...new Set(JobApplications.map(app => app.job_type).filter(Boolean))].sort();
    }, [JobApplications]);

    const statusOptions = useMemo(() => {
        const allStatuses = JobApplications.map(app => app.status).concat(CustomStatuses || []);
        return [...new Set(allStatuses.filter(Boolean))].sort();
    }, [JobApplications, CustomStatuses]);

    // Data Filtering
    const filteredApplications = useMemo(() => {
        return JobApplications.filter(app => {
            const matchesSearch = 
                !searchTerm ||
                app.company_name?.toLowerCase().includes(searchTerm.toLowerCase()) ||
                app.position?.toLowerCase().includes(searchTerm.toLowerCase()) ||
                app.city?.toLowerCase().includes(searchTerm.toLowerCase());

            const matchesJobType = jobTypeFilter === 'ALL' || app.job_type === jobTypeFilter;

            let matchesStatus = true;
            if (statusFilter !== 'ALL') {
                if (statusFilter === 'Interviewed') {
                    const interviews = app.interviews || app.Interviews || [];
                    const hasCompleted = interviews.some(i => i.status === 'Completed');
                    matchesStatus = app.has_been_interviewed || hasCompleted || app.status?.toLowerCase().includes('interviewed');
                } else {
                    matchesStatus = app.status === statusFilter;
                }
            }

            return matchesSearch && matchesJobType && matchesStatus;
        });
    }, [JobApplications, searchTerm, jobTypeFilter, statusFilter]);

    // Drawer Controls
    const openDetails = (app) => {
        setSelectedApp(app);
        setActiveTab('general');
        setIsDrawerOpen(true);
        interviewForm.reset();
    };

    const handleInterviewSubmit = (e) => {
        e.preventDefault();
        if (!selectedApp) return;

        interviewForm.post(route('interviews.store', selectedApp.id), {
            preserveScroll: true,
            onSuccess: () => {
                interviewForm.reset();
                setIsDrawerOpen(false);
            },
        });
    };

    const handleDeleteApplication = () => {
        if (!selectedApp) return;
        if (confirm('Delete this application record?')) {
            router.delete(route('job-applications.destroy', selectedApp.id), {
                onSuccess: () => setIsDrawerOpen(false),
            });
        }
    };

    return (
        <AppLayout active="applications">
            <Head title="All Job Applications" />

            <div className="space-y-6">
                {/* Header & Controls Bar */}
                <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h1 className="text-2xl font-bold text-white tracking-tight">All Job Applications</h1>
                        <p className="text-gray-400 text-sm mt-0.5">Click any row to inspect details and perform actions.</p>
                    </div>

                    <div className="flex flex-wrap items-center gap-3">
                        {/* Search Input */}
                        <div className="relative flex-1 sm:w-64">
                            <Search className="absolute left-3 top-2.5 h-4 w-4 text-gray-400" />
                            <Input
                                placeholder="Search records..."
                                value={searchTerm}
                                onChange={(e) => setSearchTerm(e.target.value)}
                                className="pl-9 bg-gray-800/80 border-gray-700 text-xs text-gray-200 focus:border-[#92BA56]"
                            />
                        </div>

                        {/* Work Setup Select */}
                        <Select value={jobTypeFilter} onValueChange={setJobTypeFilter}>
                            <SelectTrigger className="w-[150px] bg-gray-800/80 border-gray-700/80 text-xs text-gray-200">
                                <SelectValue placeholder="All Work Setups" />
                            </SelectTrigger>
                            <SelectContent className="bg-gray-800 border-gray-700 text-gray-200">
                                <SelectItem value="ALL">All Work Setups</SelectItem>
                                {workSetups.map(setup => (
                                    <SelectItem key={setup} value={setup}>{setup}</SelectItem>
                                ))}
                            </SelectContent>
                        </Select>

                        {/* Status Select */}
                        <Select value={statusFilter} onValueChange={setStatusFilter}>
                            <SelectTrigger className="w-[150px] bg-gray-800/80 border-gray-700/80 text-xs text-gray-200">
                                <SelectValue placeholder="All Statuses" />
                            </SelectTrigger>
                            <SelectContent className="bg-gray-800 border-gray-700 text-gray-200">
                                <SelectItem value="ALL">All Statuses</SelectItem>
                                {statusOptions.map(status => (
                                    <SelectItem key={status} value={status}>{status}</SelectItem>
                                ))}
                            </SelectContent>
                        </Select>

                        {/* Selectable Columns Display Dropdown */}
                        <DropdownMenu>
                            <DropdownMenuTrigger asChild>
                                <Button variant="outline" className="bg-gray-800/80 border-gray-700 text-gray-300 text-xs gap-1.5">
                                    <Columns className="w-3.5 h-3.5" /> Columns
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end" className="w-48 bg-gray-800 border-gray-700 text-gray-200">
                                <DropdownMenuLabel className="text-xs text-gray-400 uppercase">Toggle Columns</DropdownMenuLabel>
                                <DropdownMenuSeparator className="bg-gray-700" />
                                <DropdownMenuCheckboxItem
                                    checked={columnVisibility.companyPosition}
                                    onCheckedChange={(v) => setColumnVisibility(prev => ({ ...prev, companyPosition: v }))}
                                >
                                    Company & Position
                                </DropdownMenuCheckboxItem>
                                <DropdownMenuCheckboxItem
                                    checked={columnVisibility.workSetup}
                                    onCheckedChange={(v) => setColumnVisibility(prev => ({ ...prev, workSetup: v }))}
                                >
                                    Work Setup
                                </DropdownMenuCheckboxItem>
                                <DropdownMenuCheckboxItem
                                    checked={columnVisibility.compensation}
                                    onCheckedChange={(v) => setColumnVisibility(prev => ({ ...prev, compensation: v }))}
                                >
                                    Compensation
                                </DropdownMenuCheckboxItem>
                                <DropdownMenuCheckboxItem
                                    checked={columnVisibility.dateApplied}
                                    onCheckedChange={(v) => setColumnVisibility(prev => ({ ...prev, dateApplied: v }))}
                                >
                                    Date Applied
                                </DropdownMenuCheckboxItem>
                                <DropdownMenuCheckboxItem
                                    checked={columnVisibility.interviewStatus}
                                    onCheckedChange={(v) => setColumnVisibility(prev => ({ ...prev, interviewStatus: v }))}
                                >
                                    Interview Status
                                </DropdownMenuCheckboxItem>
                                <DropdownMenuCheckboxItem
                                    checked={columnVisibility.status}
                                    onCheckedChange={(v) => setColumnVisibility(prev => ({ ...prev, status: v }))}
                                >
                                    Status
                                </DropdownMenuCheckboxItem>
                            </DropdownMenuContent>
                        </DropdownMenu>

                        <div className="bg-gray-800/80 border border-gray-700/80 text-gray-300 text-xs py-2 px-3 rounded-lg">
                            Total: <span className="ml-1 font-bold text-white">{filteredApplications.length}</span>
                        </div>
                    </div>
                </div>

                {/* Applications DataTable */}
                <Card className="bg-gray-900/60 border-gray-800 shadow-xl overflow-hidden">
                    <div className="overflow-x-auto">
                        <Table className="w-full text-left">
                            <TableHeader className="bg-gray-800/80 text-xs uppercase tracking-wider text-gray-400 border-b border-gray-700/60">
                                <TableRow>
                                    {columnVisibility.companyPosition && <TableHead className="py-4 px-6 text-gray-400">Company & Position</TableHead>}
                                    {columnVisibility.workSetup && <TableHead className="py-4 px-6 text-gray-400">Work Setup</TableHead>}
                                    {columnVisibility.compensation && <TableHead className="py-4 px-6 text-gray-400">Compensation</TableHead>}
                                    {columnVisibility.dateApplied && <TableHead className="py-4 px-6 text-gray-400">Date Applied</TableHead>}
                                    {columnVisibility.interviewStatus && <TableHead className="py-4 px-6 text-gray-400">Interview Status</TableHead>}
                                    {columnVisibility.status && <TableHead className="py-4 px-6 text-gray-400">Status</TableHead>}
                                </TableRow>
                            </TableHeader>
                            <TableBody className="divide-y divide-gray-800/60">
                                {filteredApplications.length > 0 ? (
                                    filteredApplications.map((app) => {
                                        const interviews = app.interviews || app.Interviews || [];
                                        const completedInterviews = interviews.filter(i => i.status === 'Completed').length;
                                        const scheduledInterviews = interviews.filter(i => i.status === 'Scheduled').length;

                                        return (
                                            <TableRow
                                                key={app.id}
                                                onClick={() => openDetails(app)}
                                                className="group hover:bg-gray-800/40 cursor-pointer transition-colors"
                                            >
                                                {columnVisibility.companyPosition && (
                                                    <TableCell className="py-4 px-6">
                                                        <div className="font-bold text-base text-white">{app.company_name}</div>
                                                        <div className="text-[#92BA56] font-semibold text-xs mt-0.5 inline-flex items-center gap-1">
                                                            <span>{app.position}</span>
                                                            <ChevronRight className="w-3 h-3 opacity-0 group-hover:opacity-100 transition-opacity" />
                                                        </div>
                                                    </TableCell>
                                                )}

                                                {columnVisibility.workSetup && (
                                                    <TableCell className="py-4 px-6 whitespace-nowrap">
                                                        <div className="text-gray-200">{app.job_type}</div>
                                                        <div className="text-gray-400 text-xs">{app.city || 'N/A'}</div>
                                                    </TableCell>
                                                )}

                                                {columnVisibility.compensation && (
                                                    <TableCell className="py-4 px-6 whitespace-nowrap font-medium">
                                                        <span className="text-[#92BA56] font-bold">{app.formatted_salary || 'N/A'}</span>
                                                        {app.pay_rate_type && (
                                                            <span className="text-gray-400 text-xs ml-1">/ {app.pay_rate_type.toLowerCase()}</span>
                                                        )}
                                                    </TableCell>
                                                )}

                                                {columnVisibility.dateApplied && (
                                                    <TableCell className="py-4 px-6 whitespace-nowrap text-xs text-gray-300">
                                                        {app.created_at ? new Date(app.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 'N/A'}
                                                    </TableCell>
                                                )}

                                                {columnVisibility.interviewStatus && (
                                                    <TableCell className="py-4 px-6 whitespace-nowrap">
                                                        {app.has_been_interviewed || completedInterviews > 0 ? (
                                                            <Badge className="bg-emerald-500/10 text-emerald-400 border-emerald-500/20 text-xs font-semibold gap-1">
                                                                <Check className="w-3 h-3" /> Interviewed
                                                            </Badge>
                                                        ) : scheduledInterviews > 0 ? (
                                                            <Badge className="bg-amber-500/10 text-amber-400 border-amber-500/20 text-xs font-semibold">
                                                                Scheduled ({scheduledInterviews})
                                                            </Badge>
                                                        ) : (
                                                            <span className="text-xs text-gray-500">No interviews logged</span>
                                                        )}
                                                    </TableCell>
                                                )}

                                                {columnVisibility.status && (
                                                    <TableCell className="py-4 px-6 whitespace-nowrap">
                                                        <Badge variant="outline" className="px-2.5 py-1 rounded-full text-xs font-medium bg-gray-700/50 text-gray-300 border-gray-600/30">
                                                            {app.status}
                                                        </Badge>
                                                    </TableCell>
                                                )}
                                            </TableRow>
                                        );
                                    })
                                ) : (
                                    <TableRow>
                                        <TableCell colSpan={6} className="py-8 text-center text-gray-400">
                                            No job applications match your filters.
                                        </TableCell>
                                    </TableRow>
                                )}
                            </TableBody>
                        </Table>
                    </div>
                </Card>
            </div>

            {/* shadcn Sheet Component (Right Slide-over Drawer) */}
            <Sheet open={isDrawerOpen} onOpenChange={setIsDrawerOpen}>
                <SheetContent side="right" className="w-full sm:max-w-[680px] lg:max-w-[760px] bg-gray-900 border-l border-gray-800 text-gray-100 p-0 flex flex-col h-full overflow-hidden">
                    {selectedApp && (
                        <div className="flex flex-col h-full">
                            {/* Drawer Header */}
                            <SheetHeader className="shrink-0 border-b border-gray-800 p-6 space-y-1 text-left">
                                <SheetDescription className="text-xs text-gray-400">
                                    Applications / <strong className="text-white">{selectedApp.company_name}</strong>
                                </SheetDescription>
                                <SheetTitle className="text-xl font-bold text-white">
                                    {selectedApp.company_name}
                                </SheetTitle>
                                <p className="text-sm text-[#92BA56] font-semibold">{selectedApp.position}</p>

                                <div className="flex flex-wrap gap-2 pt-2 text-xs text-gray-300">
                                    <span className="flex items-center gap-1 px-2.5 py-1 bg-gray-800 border border-gray-700 rounded-md">
                                        <Briefcase className="w-3.5 h-3.5 text-gray-400" /> {selectedApp.job_type || 'N/A'}
                                    </span>
                                    <span className="flex items-center gap-1 px-2.5 py-1 bg-gray-800 border border-gray-700 rounded-md">
                                        <MapPin className="w-3.5 h-3.5 text-gray-400" /> {selectedApp.city || 'N/A'}
                                    </span>
                                    <span className="flex items-center gap-1 px-2.5 py-1 bg-gray-800 border border-gray-700 rounded-md">
                                        <Clock className="w-3.5 h-3.5 text-gray-400" /> {selectedApp.hours_per_week || 40} hrs/wk
                                    </span>
                                    <span className="flex items-center gap-1 px-2.5 py-1 bg-gray-800 border border-gray-700 rounded-md">
                                        <Globe className="w-3.5 h-3.5 text-gray-400" /> {selectedApp.source || '—'}
                                    </span>
                                </div>
                            </SheetHeader>

                            {/* shadcn Tabs Component */}
                            <Tabs value={activeTab} onValueChange={setActiveTab} className="flex-1 flex flex-col min-h-0">
                                <div className="px-6 border-b border-gray-800 bg-gray-900/90">
                                    <TabsList className="bg-transparent space-x-2 border-b-0">
                                        <TabsTrigger value="general" className="data-[state=active]:bg-gray-800 data-[state=active]:text-white text-xs font-bold uppercase">
                                            General
                                        </TabsTrigger>
                                        <TabsTrigger value="description" className="data-[state=active]:bg-gray-800 data-[state=active]:text-white text-xs font-bold uppercase">
                                            Job Desc.
                                        </TabsTrigger>
                                        <TabsTrigger value="interviews" className="data-[state=active]:bg-gray-800 data-[state=active]:text-white text-xs font-bold uppercase flex items-center gap-1.5">
                                            Interviews
                                            <Badge variant="secondary" className="px-1.5 py-0.2 text-[10px] bg-gray-800 text-gray-300">
                                                {(selectedApp.interviews || selectedApp.Interviews || []).length}
                                            </Badge>
                                        </TabsTrigger>
                                    </TabsList>
                                </div>

                                <div className="flex-1 overflow-y-auto p-6 space-y-6">
                                    <TabsContent value="general" className="mt-0 space-y-6">
                                        <div className="rounded-xl bg-gradient-to-r from-[#92BA56]/10 to-transparent border border-gray-800 p-4">
                                            <h4 className="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-3">Estimated Compensation</h4>
                                            <div className="grid grid-cols-4 gap-3 text-center">
                                                <div>
                                                    <span className="block text-[10px] font-bold text-gray-500 uppercase">Hourly</span>
                                                    <span className="text-sm font-bold text-gray-100">{selectedApp.currency || ''} {selectedApp.calculated_earnings?.Hourly || '0.00'}</span>
                                                </div>
                                                <div>
                                                    <span className="block text-[10px] font-bold text-gray-500 uppercase">Daily</span>
                                                    <span className="text-sm font-bold text-gray-100">{selectedApp.currency || ''} {selectedApp.calculated_earnings?.Daily || '0.00'}</span>
                                                </div>
                                                <div>
                                                    <span className="block text-[10px] font-bold text-gray-500 uppercase">Weekly</span>
                                                    <span className="text-sm font-bold text-gray-100">{selectedApp.currency || ''} {selectedApp.calculated_earnings?.Weekly || '0.00'}</span>
                                                </div>
                                                <div>
                                                    <span className="block text-[10px] font-bold text-[#92BA56] uppercase">Monthly</span>
                                                    <span className="text-sm font-black text-[#92BA56]">{selectedApp.currency || ''} {selectedApp.calculated_earnings?.Monthly || '0.00'}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <h4 className="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-3">Assessment</h4>
                                            <div className="space-y-3">
                                                <div>
                                                    <span className="text-xs font-bold text-emerald-400 uppercase">Pros</span>
                                                    <p className="text-sm text-gray-300 mt-1">{selectedApp.pros || 'None specified'}</p>
                                                </div>
                                                <div>
                                                    <span className="text-xs font-bold text-rose-400 uppercase">Cons</span>
                                                    <p className="text-sm text-gray-300 mt-1">{selectedApp.cons || 'None specified'}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </TabsContent>

                                    <TabsContent value="description" className="mt-0 space-y-4">
                                        {selectedApp.ApplicationURL && (
                                            <a
                                                href={selectedApp.ApplicationURL}
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                className="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-gray-300 bg-gray-800 border border-gray-700 rounded-lg hover:bg-gray-700 text-white transition-colors"
                                            >
                                                <ExternalLink className="w-3.5 h-3.5" /> Open Original Listing
                                            </a>
                                        )}
                                        <h4 className="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Full Description</h4>
                                        <p className="text-sm text-gray-300 leading-relaxed whitespace-pre-line">{selectedApp.description || 'No description provided.'}</p>
                                    </TabsContent>

                                    <TabsContent value="interviews" className="mt-0 space-y-6">
                                        <div>
                                            <h4 className="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-4">Logged Rounds</h4>
                                            <div className="space-y-3">
                                                {(selectedApp.interviews || selectedApp.Interviews || []).map((interview) => (
                                                    <div key={interview.id} className="p-3 bg-gray-800/60 border border-gray-700/60 rounded-lg">
                                                        <div className="flex justify-between items-center mb-1">
                                                            <span className="text-sm font-bold text-white">{interview.interview_type}</span>
                                                            <Badge variant="outline" className="text-[10px] uppercase">
                                                                {interview.status}
                                                            </Badge>
                                                        </div>
                                                        <p className="text-xs text-gray-400 mb-1">
                                                            {interview.scheduled_at ? new Date(interview.scheduled_at).toLocaleString() : 'Date not set'}
                                                        </p>
                                                        <p className="text-xs text-gray-300">{interview.notes || 'No notes added.'}</p>
                                                    </div>
                                                ))}
                                            </div>
                                        </div>

                                        <form onSubmit={handleInterviewSubmit} className="pt-4 border-t border-gray-800 space-y-4">
                                            <h4 className="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Log New Round</h4>
                                            
                                            <div className="grid grid-cols-2 gap-3">
                                                <div>
                                                    <label className="text-xs text-gray-400 mb-1 block">Interview Type</label>
                                                    <Select
                                                        value={interviewForm.data.interview_type}
                                                        onValueChange={v => interviewForm.setData('interview_type', v)}
                                                    >
                                                        <SelectTrigger className="bg-gray-800 border-gray-700 text-xs text-white">
                                                            <SelectValue />
                                                        </SelectTrigger>
                                                        <SelectContent className="bg-gray-800 border-gray-700 text-white">
                                                            <SelectItem value="HR Screening">HR Screening</SelectItem>
                                                            <SelectItem value="Technical Assessment">Technical Assessment</SelectItem>
                                                            <SelectItem value="Technical Interview">Technical Interview</SelectItem>
                                                            <SelectItem value="Final / Executive">Final / Executive</SelectItem>
                                                        </SelectContent>
                                                    </Select>
                                                </div>

                                                <div>
                                                    <label className="text-xs text-gray-400 mb-1 block">Scheduled Date & Time</label>
                                                    <Input
                                                        type="datetime-local"
                                                        value={interviewForm.data.scheduled_at}
                                                        onChange={e => interviewForm.setData('scheduled_at', e.target.value)}
                                                        className="bg-gray-800 border-gray-700 text-xs text-white"
                                                        required
                                                    />
                                                </div>
                                            </div>

                                            <div className="grid grid-cols-2 gap-3">
                                                <div>
                                                    <label className="text-xs text-gray-400 mb-1 block">Status</label>
                                                    <Select
                                                        value={interviewForm.data.status}
                                                        onValueChange={v => interviewForm.setData('status', v)}
                                                    >
                                                        <SelectTrigger className="bg-gray-800 border-gray-700 text-xs text-white">
                                                            <SelectValue />
                                                        </SelectTrigger>
                                                        <SelectContent className="bg-gray-800 border-gray-700 text-white">
                                                            <SelectItem value="Scheduled">Scheduled</SelectItem>
                                                            <SelectItem value="Completed">Completed</SelectItem>
                                                            <SelectItem value="Cancelled">Cancelled</SelectItem>
                                                        </SelectContent>
                                                    </Select>
                                                </div>

                                                <div>
                                                    <label className="text-xs text-gray-400 mb-1 block">Notes / Feedback</label>
                                                    <Input
                                                        placeholder="Interviewer, tech stack..."
                                                        value={interviewForm.data.notes}
                                                        onChange={e => interviewForm.setData('notes', e.target.value)}
                                                        className="bg-gray-800 border-gray-700 text-xs text-white"
                                                    />
                                                </div>
                                            </div>

                                            <Button
                                                type="submit"
                                                disabled={interviewForm.processing}
                                                className="w-full bg-[#92BA56] hover:bg-[#82a74a] text-gray-950 font-bold text-xs"
                                            >
                                                Save Interview
                                            </Button>
                                        </form>
                                    </TabsContent>
                                </div>
                            </Tabs>

                            {/* Drawer Footer Actions */}
                            <div className="shrink-0 border-t border-gray-800 bg-gray-900/95 p-4 flex items-center justify-between gap-3">
                                <Button variant="outline" size="icon" onClick={handleDeleteApplication} className="border-gray-800 text-rose-400 hover:bg-rose-500/10">
                                    <Trash2 className="w-4 h-4" />
                                </Button>

                                <Button asChild className="flex-1 bg-[#92BA56] hover:bg-[#82a74a] text-gray-950 font-bold text-xs">
                                    <Link href={route('job-applications.edit', selectedApp.id)}>
                                        <Edit className="w-3.5 h-3.5 mr-1.5" /> Edit Application
                                    </Link>
                                </Button>
                            </div>
                        </div>
                    )}
                </SheetContent>
            </Sheet>
        </AppLayout>
    );
}