import React, { useState } from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import {
    Sidebar,
    SidebarContent,
    SidebarGroup,
    SidebarGroupContent,
    SidebarGroupLabel,
    SidebarHeader,
    SidebarFooter,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarProvider,
    SidebarTrigger,
    SidebarInset,
} from '@/components/ui/sidebar';
import { TooltipProvider } from '@/components/ui/tooltip';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription, DialogFooter } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import {
    LayoutDashboard,
    Briefcase,
    FolderKanban,
    Tag,
    User,
    Upload,
    Plus,
    LogOut,
    Building2
} from 'lucide-react';

export default function AppLayout({ active = 'dashboard', children }) {
    const { auth } = usePage().props;
    const [isImportModalOpen, setIsImportModalOpen] = useState(false);

    const mainNavItems = [
        { title: 'Dashboard', url: route('dashboard'), icon: LayoutDashboard, id: 'dashboard' },
        { title: 'Applications', url: route('job-applications.applications'), icon: Briefcase, id: 'applications' },
    ];

    const configNavItems = [
        { title: 'Sources', url: route('application-sources.index'), icon: FolderKanban, id: 'sources' },
        { title: 'Statuses', url: route('application-statuses.index'), icon: Tag, id: 'statuses' },
        { title: 'Profile Settings', url: route('profile.edit'), icon: User, id: 'profile' },
    ];

    return (
    <TooltipProvider>
        <SidebarProvider defaultOpen={true} className="min-h-screen w-full flex bg-background overflow-hidden">
            {/* Collapsible Sidebar */}
            <Sidebar collapsible="icon" className="border-r border-sidebar-border">
                <SidebarHeader className="p-4 border-b border-sidebar-border flex flex-row items-center justify-between">
                    <div className="flex items-center gap-3 overflow-hidden">
                        <div className="p-2 bg-primary/15 rounded-lg border border-primary/30 shrink-0">
                            <Building2 className="w-5 h-5 text-primary" />
                        </div>
                        <span className="font-bold text-lg tracking-tight text-sidebar-foreground group-data-[collapsible=icon]:hidden">
                            Job Tracker
                        </span>
                    </div>
                    <SidebarTrigger className="text-muted-foreground hover:text-sidebar-foreground hover:bg-sidebar-accent shrink-0" />
                </SidebarHeader>

                <SidebarContent className="px-2 py-4">
                    <SidebarGroup>
                        <SidebarGroupLabel className="px-3 text-[10px] font-bold uppercase tracking-wider text-muted-foreground group-data-[collapsible=icon]:hidden">
                            Navigation
                        </SidebarGroupLabel>
                        <SidebarGroupContent className="mt-1">
                            <SidebarMenu>
                                {mainNavItems.map((item) => {
                                    const Icon = item.icon;
                                    const isActive = active === item.id;
                                    return (
                                        <SidebarMenuItem key={item.id}>
                                            <SidebarMenuButton
                                                asChild
                                                tooltip={item.title}
                                                className={`w-full justify-start gap-3 px-3 py-2.5 rounded-xl font-semibold transition-colors ${
                                                    isActive
                                                        ? 'bg-primary/15 text-primary border border-primary/30'
                                                        : 'text-muted-foreground hover:text-sidebar-foreground hover:bg-sidebar-accent'
                                                }`}
                                            >
                                                <Link href={item.url}>
                                                    <Icon className={`h-4 w-4 shrink-0 ${isActive ? 'text-primary' : 'text-muted-foreground'}`} />
                                                    <span className="group-data-[collapsible=icon]:hidden">{item.title}</span>
                                                </Link>
                                            </SidebarMenuButton>
                                        </SidebarMenuItem>
                                    );
                                })}
                            </SidebarMenu>
                        </SidebarGroupContent>
                    </SidebarGroup>

                    <SidebarGroup className="mt-4 pt-4 border-t border-sidebar-border">
                        <SidebarGroupLabel className="px-3 text-[10px] font-bold uppercase tracking-wider text-muted-foreground group-data-[collapsible=icon]:hidden">
                            Configuration
                        </SidebarGroupLabel>
                        <SidebarGroupContent className="mt-1">
                            <SidebarMenu>
                                {configNavItems.map((item) => {
                                    const Icon = item.icon;
                                    const isActive = active === item.id;
                                    return (
                                        <SidebarMenuItem key={item.id}>
                                            <SidebarMenuButton
                                                asChild
                                                tooltip={item.title}
                                                className={`w-full justify-start gap-3 px-3 py-2.5 rounded-xl font-semibold transition-colors ${
                                                    isActive
                                                        ? 'bg-primary/15 text-primary border border-primary/30'
                                                        : 'text-muted-foreground hover:text-sidebar-foreground hover:bg-sidebar-accent'
                                                }`}
                                            >
                                                <Link href={item.url}>
                                                    <Icon className={`h-4 w-4 shrink-0 ${isActive ? 'text-primary' : 'text-muted-foreground'}`} />
                                                    <span className="group-data-[collapsible=icon]:hidden">{item.title}</span>
                                                </Link>
                                            </SidebarMenuButton>
                                        </SidebarMenuItem>
                                    );
                                })}
                            </SidebarMenu>
                        </SidebarGroupContent>
                    </SidebarGroup>
                </SidebarContent>

                <SidebarFooter className="p-4 border-t border-sidebar-border space-y-3">
                    <Button
                        variant="outline"
                        onClick={() => setIsImportModalOpen(true)}
                        className="w-full justify-center gap-2 text-xs font-semibold text-sidebar-foreground bg-sidebar-accent hover:bg-sidebar-accent/80 border-sidebar-border rounded-xl group-data-[collapsible=icon]:px-0"
                        title="Import CSV"
                    >
                        <Upload className="w-3.5 h-3.5 text-primary shrink-0" />
                        <span className="group-data-[collapsible=icon]:hidden">Import CSV</span>
                    </Button>

                    <Button
                        asChild
                        className="w-full justify-center gap-1.5 text-xs font-semibold text-primary-foreground bg-primary hover:bg-primary/90 rounded-xl shadow-md shadow-primary/20 group-data-[collapsible=icon]:px-0"
                        title="Log Application"
                    >
                        <Link href={route('job-applications.create')}>
                            <Plus className="w-4 h-4 shrink-0" />
                            <span className="group-data-[collapsible=icon]:hidden">Log Application</span>
                        </Link>
                    </Button>

                    {auth?.user && (
                        <div className="pt-3 border-t border-sidebar-border flex items-center justify-between overflow-hidden">
                            <Link href={route('profile.edit')} className="truncate pr-2 group group-data-[collapsible=icon]:hidden">
                                <span className="block text-xs font-bold text-sidebar-foreground group-hover:text-primary transition-colors truncate">
                                    {auth.user.name}
                                </span>
                                <span className="block text-[10px] text-muted-foreground truncate">
                                    {auth.user.email}
                                </span>
                            </Link>

                            <Link
                                href={route('logout')}
                                method="post"
                                as="button"
                                title="Log Out"
                                className="p-1.5 text-rose-400 hover:text-rose-300 hover:bg-rose-500/10 border border-transparent hover:border-rose-500/30 rounded-lg transition-all shrink-0 group-data-[collapsible=icon]:mx-auto"
                            >
                                <LogOut className="w-4 h-4" />
                            </Link>
                        </div>
                    )}
                </SidebarFooter>
            </Sidebar>

            <SidebarInset className="flex-1 min-w-0 bg-background text-foreground flex flex-col overflow-x-hidden
                md:pl-[var(--sidebar-width)]
                transition-[padding] duration-200 ease-linear
                group-data-[state=collapsed]/sidebar-wrapper:md:pl-[var(--sidebar-width-icon)]">
                {/* Mobile Header Bar */}
                <header className="lg:hidden h-14 border-b border-border bg-card/80 backdrop-blur px-4 flex items-center justify-between sticky top-0 z-30 shrink-0">
                    <div className="flex items-center gap-2">
                        <SidebarTrigger className="text-muted-foreground hover:text-foreground" />
                        <span className="font-bold text-sm text-foreground">Job Tracker</span>
                    </div>
                    <Button asChild size="sm" className="bg-primary text-primary-foreground font-semibold hover:bg-primary/90 text-xs">
                        <Link href={route('job-applications.create')}>+ Log</Link>
                    </Button>
                </header>

                <main className="flex-1 p-4 sm:p-6 lg:p-8 w-full min-w-0 overflow-x-hidden">
                    {children}
                </main>
            </SidebarInset>

            {/* Import Modal — also switch to semantic tokens */}
            <Dialog open={isImportModalOpen} onOpenChange={setIsImportModalOpen}>
                <DialogContent className="bg-popover border-border text-popover-foreground max-w-md">
                    <DialogHeader>
                        <DialogTitle className="text-lg font-bold">Import CSV Applications</DialogTitle>
                        <DialogDescription className="text-xs text-muted-foreground">
                            Select a CSV file containing your past job application logs.
                        </DialogDescription>
                    </DialogHeader>

                    <form
                        action={route('job-applications.import')}
                        method="POST"
                        encType="multipart/form-data"
                        className="space-y-4 py-2"
                    >
                        <Input
                            type="file"
                            name="file"
                            accept=".csv"
                            className="bg-input/40 border-border text-xs text-foreground"
                            required
                        />
                        <DialogFooter className="gap-2">
                            <Button type="button" variant="outline" onClick={() => setIsImportModalOpen(false)}>
                                Cancel
                            </Button>
                            <Button type="submit" className="bg-primary text-primary-foreground font-bold hover:bg-primary/90">
                                Upload & Import
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </SidebarProvider>
    </TooltipProvider>
);
}