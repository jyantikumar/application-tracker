import React from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';

import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    BarElement,
    PointElement,
    LineElement,
    ArcElement,
    Title,
    Tooltip,
    Legend,
    Filler
} from 'chart.js';
import { Bar, Line, Doughnut } from 'react-chartjs-2';
import ChartDataLabels from 'chartjs-plugin-datalabels';

ChartJS.register(
    CategoryScale,
    LinearScale,
    BarElement,
    PointElement,
    LineElement,
    ArcElement,
    Title,
    Tooltip,
    Legend,
    Filler,
    ChartDataLabels
);

export default function Index({
    TotalApplications = 0,
    InterviewsDone = 0,
    AppliedCount = 0,
    UnderReviewCount = 0,
    CurrentlyInterviewing = 0,
    HiredCount = 0,
    RejectedCount = 0,
    WaitingResponse = 0,
    StatusCounts = {},
    ApplicationsBySource = {},
    ApplicationsByDate = {},
    ApplicationsByJobType = {},
    ApplicationsByLocation = {},
    RecentApplications = [],
}) {
    const { flash } = usePage().props;

    const customStatuses = Object.entries(StatusCounts || {}).filter(
        ([name]) => !['Applied', 'Under Review', 'Currently Interviewing', 'Hired', 'Rejected', 'Waiting response'].includes(name)
    );

    const horizontalBarOptions = {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false }, datalabels: { display: false } },
        scales: {
            x: { ticks: { color: '#9ca3af' }, grid: { color: '#374151' } },
            y: { ticks: { color: '#9ca3af' }, grid: { display: false } }
        }
    };

    // CHANGED: Bar chart now uses brand green
    const sourceChartData = {
        labels: Object.keys(ApplicationsBySource || {}),
        datasets: [{ data: Object.values(ApplicationsBySource || {}), backgroundColor: '#92BA56', borderRadius: 4 }]
    };

    // CHANGED: Line chart now uses brand green instead of sky blue
    const dateChartData = {
        labels: Object.keys(ApplicationsByDate || {}),
        datasets: [{
            data: Object.values(ApplicationsByDate || {}),
            borderColor: '#92BA56',
            backgroundColor: 'rgba(146, 186, 86, 0.1)',
            borderWidth: 2,
            tension: 0.4,
            fill: true
        }]
    };

    const dateChartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false }, datalabels: { display: false } },
        scales: {
            x: { ticks: { color: '#9ca3af' }, grid: { color: '#374151' } },
            y: { ticks: { color: '#9ca3af' }, grid: { color: '#374151' } }
        }
    };

    // CHANGED: Doughnut palette replaced purple with amber, kept brand green
    const workSetupChartData = {
        labels: Object.keys(ApplicationsByJobType || {}),
        datasets: [{
            data: Object.values(ApplicationsByJobType || {}),
            backgroundColor: ['#92BA56', '#f59e0b', '#38bdf8', '#f43f5e', '#a3a3a3'],
            borderWidth: 2,
            borderColor: '#1f2937'
        }]
    };

    const workSetupChartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom', labels: { color: '#d1d5db', padding: 12 } },
            datalabels: {
                color: '#ffffff',
                font: { weight: 'bold', size: 11 },
                formatter: (val, ctx) => {
                    const total = ctx.chart.data.datasets[0].data.reduce((a, b) => a + Number(b), 0);
                    return total ? `${((val / total) * 100).toFixed(1)}%` : '0%';
                }
            }
        },
        cutout: '60%'
    };

    // CHANGED: Location bar chart uses brand green
    const locationChartData = {
        labels: Object.keys(ApplicationsByLocation || {}),
        datasets: [{ data: Object.values(ApplicationsByLocation || {}), backgroundColor: '#92BA56', borderRadius: 4 }]
    };

    return (
        <AppLayout active="dashboard">
            <Head title="Dashboard" />

            <div className="space-y-6 w-full min-w-0">
                {flash?.success && (
                    <Alert className="bg-emerald-500/10 border-emerald-500/30 text-emerald-400">
                        <AlertDescription>{flash.success}</AlertDescription>
                    </Alert>
                )}

                {/* Metric Cards Grid — colors now match brand palette */}
                {/* Metric Cards Grid */}
<div className="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-4 xl:grid-cols-8 gap-3 w-full">
    <Card size="sm" className="text-center">
        <CardHeader className="items-center">
            <CardDescription className="text-[11px] font-semibold uppercase tracking-wider truncate">
                Total Applications
            </CardDescription>
        </CardHeader>
        <CardContent>
            <span className="text-2xl font-black text-foreground">{TotalApplications}</span>
        </CardContent>
    </Card>

    <Card size="sm" className="text-center">
        <CardHeader className="items-center">
            <CardDescription className="text-[11px] font-semibold uppercase tracking-wider truncate">
                Interviewed Roles
            </CardDescription>
        </CardHeader>
        <CardContent>
            <span className="text-2xl font-black text-amber-400">{InterviewsDone}</span>
        </CardContent>
    </Card>

    <Card size="sm" className="text-center">
        <CardHeader className="items-center">
            <CardDescription className="text-[11px] font-semibold uppercase tracking-wider truncate">
                Applied
            </CardDescription>
        </CardHeader>
        <CardContent>
            <span className="text-2xl font-black text-primary">{AppliedCount}</span>
        </CardContent>
    </Card>

    <Card size="sm" className="text-center">
        <CardHeader className="items-center">
            <CardDescription className="text-[11px] font-semibold uppercase tracking-wider truncate">
                Under Review
            </CardDescription>
        </CardHeader>
        <CardContent>
            <span className="text-2xl font-black text-sky-400">{UnderReviewCount}</span>
        </CardContent>
    </Card>

    <Card size="sm" className="text-center">
        <CardHeader className="items-center">
            <CardDescription className="text-[11px] font-semibold uppercase tracking-wider truncate">
                Interviewing
            </CardDescription>
        </CardHeader>
        <CardContent>
            <span className="text-2xl font-black text-primary">{CurrentlyInterviewing}</span>
        </CardContent>
    </Card>

    <Card size="sm" className="text-center">
        <CardHeader className="items-center">
            <CardDescription className="text-[11px] font-semibold uppercase tracking-wider truncate">
                Hired
            </CardDescription>
        </CardHeader>
        <CardContent>
            <span className="text-2xl font-black text-primary">{HiredCount}</span>
        </CardContent>
    </Card>

    <Card size="sm" className="text-center">
        <CardHeader className="items-center">
            <CardDescription className="text-[11px] font-semibold uppercase tracking-wider truncate">
                Rejected
            </CardDescription>
        </CardHeader>
        <CardContent>
            <span className="text-2xl font-black text-rose-400">{RejectedCount}</span>
        </CardContent>
    </Card>

    <Card size="sm" className="text-center">
        <CardHeader className="items-center">
            <CardDescription className="text-[11px] font-semibold uppercase tracking-wider truncate">
                Waiting Response
            </CardDescription>
        </CardHeader>
        <CardContent>
            <span className="text-2xl font-black text-muted-foreground">{WaitingResponse}</span>
        </CardContent>
    </Card>

    {customStatuses.map(([statusName, statusCount]) => (
        <Card key={statusName} size="sm" className="text-center">
            <CardHeader className="items-center">
                <CardDescription
                    className="text-[11px] font-semibold uppercase tracking-wider truncate"
                    title={statusName}
                >
                    {statusName}
                </CardDescription>
            </CardHeader>
            <CardContent>
                <span className="text-2xl font-black text-primary">{statusCount}</span>
            </CardContent>
        </Card>
    ))}
</div>

                {/* Charts Grid */}
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 w-full min-w-0">
                    <Card className="bg-[#1E293B]/40 border-gray-800 p-5 min-w-0 w-full overflow-hidden">
                        <CardHeader className="p-0 mb-4">
                            <CardTitle className="text-sm font-bold text-gray-200 uppercase tracking-wider">Applications by Source</CardTitle>
                        </CardHeader>
                        <CardContent className="p-0 h-64 w-full min-w-0 relative">
                            <Bar data={sourceChartData} options={horizontalBarOptions} />
                        </CardContent>
                    </Card>

                    <Card className="bg-[#1E293B]/40 border-gray-800 p-5 min-w-0 w-full overflow-hidden">
                        <CardHeader className="p-0 mb-4">
                            <CardTitle className="text-sm font-bold text-gray-200 uppercase tracking-wider">Applications over Time</CardTitle>
                        </CardHeader>
                        <CardContent className="p-0 h-64 w-full min-w-0 relative">
                            <Line data={dateChartData} options={dateChartOptions} />
                        </CardContent>
                    </Card>

                    <Card className="bg-[#1E293B]/40 border-gray-800 p-5 min-w-0 w-full overflow-hidden">
                        <CardHeader className="p-0 mb-4">
                            <CardTitle className="text-sm font-bold text-gray-200 uppercase tracking-wider">Applications by Work Setup</CardTitle>
                        </CardHeader>
                        <CardContent className="p-0 h-64 w-full min-w-0 relative flex items-center justify-center">
                            <Doughnut data={workSetupChartData} options={workSetupChartOptions} />
                        </CardContent>
                    </Card>
                </div>

                <Card className="bg-[#1E293B]/40 border-gray-800 p-5 min-w-0 w-full overflow-hidden">
                    <CardHeader className="p-0 mb-4">
                        <CardTitle className="text-sm font-bold text-gray-200 uppercase tracking-wider">Applications by Location</CardTitle>
                    </CardHeader>
                    <CardContent className="p-0 h-64 w-full min-w-0 relative">
                        <Bar data={locationChartData} options={horizontalBarOptions} />
                    </CardContent>
                </Card>

                {/* Recent Applications Table */}
                <Card className="bg-[#1E293B]/40 border-gray-800 p-4 min-w-0 w-full overflow-hidden">
                    <div className="pb-4 border-b border-gray-800 flex items-center justify-between">
                        <div>
                            <h3 className="text-base font-bold text-white">Top 10 Recent Applications</h3>
                            <p className="text-xs text-gray-400">Latest logged records</p>
                        </div>
                        <Button asChild variant="link" className="text-xs font-semibold text-[#92BA56] hover:underline p-0">
                            <Link href={route('job-applications.applications')}>
                                View All &rarr;
                            </Link>
                        </Button>
                    </div>

                    <div className="overflow-x-auto w-full min-w-0 pt-4">
                        <Table className="w-full">
                            <TableHeader className="bg-gray-900/60 border-b border-gray-800 text-xs uppercase text-gray-400">
                                <TableRow>
                                    <TableHead className="text-gray-400">Company & Position</TableHead>
                                    <TableHead className="text-gray-400">Setup & City</TableHead>
                                    <TableHead className="text-gray-400">Compensation</TableHead>
                                    <TableHead className="text-gray-400">Date Applied</TableHead>
                                    <TableHead className="text-gray-400">Status</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {RecentApplications.length > 0 ? (
                                    RecentApplications.map((application) => (
                                        <TableRow key={application.id || application.company_name} className="hover:bg-gray-800/50 border-gray-800/50">
                                            <TableCell>
                                                <div className="font-semibold text-white">{application.company_name}</div>
                                                <div className="text-gray-400 text-xs mt-0.5">{application.position}</div>
                                            </TableCell>
                                            <TableCell className="whitespace-nowrap">
                                                <div className="text-gray-200">{application.job_type}</div>
                                                <div className="text-gray-400 text-xs">{application.city || 'N/A'}</div>
                                            </TableCell>
                                            <TableCell className="whitespace-nowrap font-medium text-[#92BA56]">
                                                {application.formatted_salary}
                                            </TableCell>
                                            <TableCell className="whitespace-nowrap text-xs text-gray-300">
                                                {application.created_at ? new Date(application.created_at).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' }) : 'N/A'}
                                            </TableCell>
                                            <TableCell className="whitespace-nowrap">
                                                <Badge variant="outline" className="px-2.5 py-1 rounded-full text-xs font-medium bg-gray-800 text-gray-300 border-gray-700">
                                                    {application.status}
                                                </Badge>
                                            </TableCell>
                                        </TableRow>
                                    ))
                                ) : (
                                    <TableRow>
                                        <TableCell colSpan={5} className="py-8 text-center text-gray-400">
                                            No applications recorded yet.
                                        </TableCell>
                                    </TableRow>
                                )}
                            </TableBody>
                        </Table>
                    </div>
                </Card>
            </div>
        </AppLayout>
    );
}