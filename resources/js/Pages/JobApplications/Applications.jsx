import React, { useEffect, useMemo, useState } from "react"
import { Head, router, useForm } from "@inertiajs/react"
import { useApplicationModal } from "@/components/application-modal-provider"
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from "@/components/ui/alert-dialog"
import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"
import { Card } from "@/components/ui/card"
import {
  DropdownMenu,
  DropdownMenuCheckboxItem,
  DropdownMenuContent,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import {
  Pagination,
  PaginationContent,
  PaginationEllipsis,
  PaginationItem,
  PaginationLink,
  PaginationNext,
  PaginationPrevious,
} from "@/components/ui/pagination"
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select"
import {
  Sheet,
  SheetContent,
  SheetDescription,
  SheetHeader,
  SheetTitle,
} from "@/components/ui/sheet"
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table"
import {
  BriefcaseIcon,
  CalendarIcon,
  CheckIcon,
  ChevronRightIcon,
  Columns3Icon,
  DownloadIcon,
  ExternalLinkIcon,
  FileTextIcon,
  GlobeIcon,
  MapPinIcon,
  PencilIcon,
  SearchIcon,
  Trash2Icon,
} from "lucide-react"

import { cn } from "@/lib/utils"
import { ConvertToPhp, FormatPhpAmount } from "@/lib/currencies"

function formatDate(value) {
  if (!value) return "—"
  return new Date(value).toLocaleDateString("en-US", {
    month: "short",
    day: "numeric",
    year: "numeric",
  })
}

function getResumeKind(url = "") {
  const lower = url.toLowerCase().split("?")[0]
  if (lower.endsWith(".pdf")) return "pdf"
  if (/\.(png|jpe?g|gif|webp)$/.test(lower)) return "image"
  return "other"
}

function getPageNumbers(current, total) {
  const delta = 1
  const range = []
  const rangeWithDots = []
  let l

  for (let i = 1; i <= total; i++) {
    if (
      i === 1 ||
      i === total ||
      (i >= current - delta && i <= current + delta)
    ) {
      range.push(i)
    }
  }

  for (const i of range) {
    if (l) {
      if (i - l === 2) {
        rangeWithDots.push(l + 1)
      } else if (i - l !== 1) {
        rangeWithDots.push("...")
      }
    }
    rangeWithDots.push(i)
    l = i
  }

  return rangeWithDots
}

export default function Applications({
  JobApplications = [],
  CustomStatuses = [],
}) {
  const { openCreate, openEdit } = useApplicationModal()
  const [searchTerm, setSearchTerm] = useState("")
  const [jobTypeFilter, setJobTypeFilter] = useState("ALL")
  const [statusFilter, setStatusFilter] = useState("ALL")
  const [selectedApp, setSelectedApp] = useState(null)
  const [isDrawerOpen, setIsDrawerOpen] = useState(false)
  const [activeTab, setActiveTab] = useState("general")
  const [confirmDeleteOpen, setConfirmDeleteOpen] = useState(false)
  const [PhpConversion, setPhpConversion] = useState(null)
  const [PhpConversionLoading, setPhpConversionLoading] = useState(false)
  const [PhpConversionError, setPhpConversionError] = useState(null)

  const [page, setPage] = useState(1)
  const [pageSize, setPageSize] = useState(10)

  const [columnVisibility, setColumnVisibility] = useState({
    companyPosition: true,
    workSetup: true,
    compensation: true,
    dateApplied: true,
    interviewStatus: true,
    status: true,
  })

  const interviewForm = useForm({
    interview_type: "HR Screening",
    scheduled_at: "",
    status: "Scheduled",
    notes: "",
  })

  const workSetups = useMemo(
    () =>
      [...new Set(JobApplications.map((app) => app.job_type).filter(Boolean))].sort(),
    [JobApplications]
  )

  const statusOptions = useMemo(() => {
    const all = JobApplications.map((app) => app.status).concat(CustomStatuses || [])
    return [...new Set(all.filter(Boolean))].sort()
  }, [JobApplications, CustomStatuses])

  const filteredApplications = useMemo(() => {
    return JobApplications.filter((app) => {
      const matchesSearch =
        !searchTerm ||
        app.company_name?.toLowerCase().includes(searchTerm.toLowerCase()) ||
        app.position?.toLowerCase().includes(searchTerm.toLowerCase()) ||
        app.city?.toLowerCase().includes(searchTerm.toLowerCase())

      const matchesJobType =
        jobTypeFilter === "ALL" || app.job_type === jobTypeFilter

      let matchesStatus = true
      if (statusFilter !== "ALL") {
        if (statusFilter === "Interviewed") {
          const interviews = app.interviews || app.Interviews || []
          const hasCompleted = interviews.some((i) => i.status === "Completed")
          matchesStatus =
            app.has_been_interviewed ||
            hasCompleted ||
            app.status?.toLowerCase().includes("interviewed")
        } else {
          matchesStatus = app.status === statusFilter
        }
      }

      return matchesSearch && matchesJobType && matchesStatus
    })
  }, [JobApplications, searchTerm, jobTypeFilter, statusFilter])

  useEffect(() => {
    setPage(1)
  }, [searchTerm, jobTypeFilter, statusFilter, pageSize])

  const totalPages = Math.max(1, Math.ceil(filteredApplications.length / pageSize))
  const currentPage = Math.min(page, totalPages)

  const paginatedApplications = useMemo(() => {
    const start = (currentPage - 1) * pageSize
    return filteredApplications.slice(start, start + pageSize)
  }, [filteredApplications, currentPage, pageSize])

  const openDetails = (app) => {
    setSelectedApp(app)
    setActiveTab("general")
    setIsDrawerOpen(true)
    interviewForm.reset()
  }

  useEffect(() => {
    if (!isDrawerOpen || !selectedApp) {
      setPhpConversion(null)
      setPhpConversionError(null)
      setPhpConversionLoading(false)
      return
    }

    const Currency = String(selectedApp.currency || "").toUpperCase()
    const Monthly = Number(selectedApp.calculated_earnings?.Monthly)

    if (!Currency || Currency === "PHP" || !Number.isFinite(Monthly) || Monthly <= 0) {
      setPhpConversion(null)
      setPhpConversionError(null)
      setPhpConversionLoading(false)
      return
    }

    let Cancelled = false
    setPhpConversionLoading(true)
    setPhpConversionError(null)

    ConvertToPhp(Currency, Monthly)
      .then((Result) => {
        if (!Cancelled) setPhpConversion(Result)
      })
      .catch(() => {
        if (!Cancelled) {
          setPhpConversion(null)
          setPhpConversionError("Could not load PHP conversion right now.")
        }
      })
      .finally(() => {
        if (!Cancelled) setPhpConversionLoading(false)
      })

    return () => {
      Cancelled = true
    }
  }, [isDrawerOpen, selectedApp?.id, selectedApp?.currency, selectedApp?.calculated_earnings?.Monthly])

  const handleInterviewSubmit = (e) => {
    e.preventDefault()
    if (!selectedApp) return
    interviewForm.post(route("interviews.store", selectedApp.id), {
      preserveScroll: true,
      onSuccess: () => {
        interviewForm.reset()
        setIsDrawerOpen(false)
      },
    })
  }

  const handleDeleteApplication = () => {
    if (!selectedApp) return
    setConfirmDeleteOpen(true)
  }

  const confirmDelete = () => {
    if (!selectedApp) return
    router.delete(route("job-applications.destroy", selectedApp.id), {
      onSuccess: () => {
        setConfirmDeleteOpen(false)
        setIsDrawerOpen(false)
        setSelectedApp(null)
      },
    })
  }

  const visibleColumnCount = Object.values(columnVisibility).filter(Boolean).length

  return (
    <>
      <Head title="All Job Applications" />

      <div className="space-y-6">
        <div className="flex flex-col justify-between gap-4 md:flex-row md:items-end">
          <div>
            <h1 className="font-heading text-2xl font-semibold tracking-tight">
              All applications
            </h1>
            <p className="mt-1 text-sm text-muted-foreground">
              Click a row to inspect details, interviews, and actions.
            </p>
          </div>

          <div className="flex flex-wrap items-center gap-2">
            <Button size="sm" className="rounded-sm p-4" onClick={openCreate}>
              Log application
            </Button>
            <div className="relative w-full sm:w-56">
              <SearchIcon className="pointer-events-none absolute top-2.5 left-3 size-4 text-muted-foreground" />
              <Input
                placeholder="Search records..."
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
                className="pl-9"
              />
            </div>

            <Select value={jobTypeFilter} onValueChange={setJobTypeFilter}>
              <SelectTrigger className="w-[150px]">
                <SelectValue placeholder="Work setup" />
              </SelectTrigger>
              <SelectContent position="popper">
                <SelectItem value="ALL">All work setups</SelectItem>
                {workSetups.map((setup) => (
                  <SelectItem key={setup} value={setup}>
                    {setup}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>

            <Select value={statusFilter} onValueChange={setStatusFilter}>
              <SelectTrigger className="w-[150px]">
                <SelectValue placeholder="Status" />
              </SelectTrigger>
              <SelectContent position="popper">
                <SelectItem value="ALL">All statuses</SelectItem>
                {statusOptions.map((status) => (
                  <SelectItem key={status} value={status}>
                    {status}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>

            <DropdownMenu>
              <DropdownMenuTrigger asChild>
                <Button variant="outline" size="sm" className="gap-1.5">
                  <Columns3Icon className="size-3.5" />
                  Columns
                </Button>
              </DropdownMenuTrigger>
              <DropdownMenuContent align="end" className="w-48">
                <DropdownMenuLabel>Toggle columns</DropdownMenuLabel>
                <DropdownMenuSeparator />
                {[
                  ["companyPosition", "Company & position"],
                  ["workSetup", "Work setup"],
                  ["compensation", "Compensation"],
                  ["dateApplied", "Date applied"],
                  ["interviewStatus", "Interview status"],
                  ["status", "Status"],
                ].map(([key, label]) => (
                  <DropdownMenuCheckboxItem
                    key={key}
                    checked={columnVisibility[key]}
                    onCheckedChange={(v) =>
                      setColumnVisibility((prev) => ({ ...prev, [key]: v }))
                    }
                  >
                    {label}
                  </DropdownMenuCheckboxItem>
                ))}
              </DropdownMenuContent>
            </DropdownMenu>

            <Badge
              variant="secondary"
              className="rounded-lg px-3 py-1.5 tabular-nums"
            >
              {filteredApplications.length} total
            </Badge>
          </div>
        </div>

        <Card className="gap-0 overflow-hidden py-0">
          <Table>
            <TableHeader>
              <TableRow>
                {columnVisibility.companyPosition && (
                  <TableHead>Company & position</TableHead>
                )}
                {columnVisibility.workSetup && <TableHead>Work setup</TableHead>}
                {columnVisibility.compensation && (
                  <TableHead>Compensation</TableHead>
                )}
                {columnVisibility.dateApplied && (
                  <TableHead>Date applied</TableHead>
                )}
                {columnVisibility.interviewStatus && (
                  <TableHead>Interview</TableHead>
                )}
                {columnVisibility.status && <TableHead>Status</TableHead>}
              </TableRow>
            </TableHeader>
            <TableBody>
              {paginatedApplications.length > 0 ? (
                paginatedApplications.map((app, index) => {
                  const interviews = app.interviews || app.Interviews || []
                  const completed = interviews.filter(
                    (i) => i.status === "Completed"
                  ).length
                  const scheduled = interviews.filter(
                    (i) => i.status === "Scheduled"
                  ).length

                  return (
                    <TableRow
                      key={app.id}
                      onClick={() => openDetails(app)}
                      className={cn(
                        "cursor-pointer",
                        index % 2 === 0
                          ? "bg-background"
                          : "bg-muted/40 hover:bg-muted/60"
                      )}
                    >
                      {columnVisibility.companyPosition && (
                        <TableCell>
                          <div className="font-medium">{app.company_name}</div>
                          <div className="mt-0.5 inline-flex items-center gap-1 text-xs font-medium text-primary">
                            {app.position}
                            {app.resume_path && (
                              <FileTextIcon
                                className="size-3 text-muted-foreground"
                                aria-label="Resume attached"
                              />
                            )}
                            <ChevronRightIcon className="size-3 opacity-0 transition-opacity group-hover:opacity-100" />
                          </div>
                        </TableCell>
                      )}
                      {columnVisibility.workSetup && (
                        <TableCell>
                          <div>{app.job_type || "—"}</div>
                          <div className="text-xs text-muted-foreground">
                            {app.city || "N/A"}
                          </div>
                        </TableCell>
                      )}
                      {columnVisibility.compensation && (
                        <TableCell>
                          <span className="font-semibold text-primary">
                            {app.formatted_salary || "N/A"}
                          </span>
                          {app.pay_rate_type && (
                            <span className="ml-1 text-xs text-muted-foreground">
                              / {app.pay_rate_type.toLowerCase()}
                            </span>
                          )}
                        </TableCell>
                      )}
                      {columnVisibility.dateApplied && (
                        <TableCell className="tabular-nums text-muted-foreground">
                          {formatDate(app.created_at)}
                        </TableCell>
                      )}
                      {columnVisibility.interviewStatus && (
                        <TableCell>
                          {app.has_been_interviewed || completed > 0 ? (
                            <Badge className="gap-1 border-emerald-500/20 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                              <CheckIcon className="size-3" />
                              Interviewed
                            </Badge>
                          ) : scheduled > 0 ? (
                            <Badge className="border-amber-500/20 bg-amber-500/10 text-amber-700 dark:text-amber-400">
                              Scheduled ({scheduled})
                            </Badge>
                          ) : (
                            <span className="text-xs text-muted-foreground">
                              None logged
                            </span>
                          )}
                        </TableCell>
                      )}
                      {columnVisibility.status && (
                        <TableCell>
                          <Badge variant="outline">{app.status}</Badge>
                        </TableCell>
                      )}
                    </TableRow>
                  )
                })
              ) : (
                <TableRow>
                  <TableCell
                    colSpan={Math.max(visibleColumnCount, 1)}
                    className="py-10 text-center text-muted-foreground"
                  >
                    No job applications match your filters.
                  </TableCell>
                </TableRow>
              )}
            </TableBody>
          </Table>

          {filteredApplications.length > 0 && (
            <div className="flex flex-col items-center justify-between gap-3 border-t border-border/60 px-4 py-3 sm:flex-row">
              <div className="flex flex-wrap items-center gap-3 text-xs text-muted-foreground">
                <span>
                  Showing{" "}
                  <span className="font-medium text-foreground">
                    {(currentPage - 1) * pageSize + 1}
                  </span>
                  –
                  <span className="font-medium text-foreground">
                    {Math.min(currentPage * pageSize, filteredApplications.length)}
                  </span>{" "}
                  of{" "}
                  <span className="font-medium text-foreground">
                    {filteredApplications.length}
                  </span>
                </span>

                <div className="flex items-center gap-1.5">
                  <span>Rows</span>
                  <Select
                    value={String(pageSize)}
                    onValueChange={(v) => {
                      setPageSize(Number(v))
                      setPage(1)
                    }}
                  >
                    <SelectTrigger className="h-7 w-[68px] text-xs">
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent position="popper">
                      {[5, 10, 20, 50].map((n) => (
                        <SelectItem key={n} value={String(n)}>
                          {n}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>
              </div>

              <Pagination className="mx-0 w-auto">
                <PaginationContent>
                  <PaginationItem>
                    <PaginationPrevious
                      href="#"
                      onClick={(e) => {
                        e.preventDefault()
                        setPage((p) => Math.max(1, p - 1))
                      }}
                      className={
                        currentPage === 1 ? "pointer-events-none opacity-50" : ""
                      }
                    />
                  </PaginationItem>

                  {getPageNumbers(currentPage, totalPages).map((p, i) =>
                    p === "..." ? (
                      <PaginationItem key={`ellipsis-${i}`}>
                        <PaginationEllipsis />
                      </PaginationItem>
                    ) : (
                      <PaginationItem key={p}>
                        <PaginationLink
                          href="#"
                          isActive={p === currentPage}
                          onClick={(e) => {
                            e.preventDefault()
                            setPage(p)
                          }}
                        >
                          {p}
                        </PaginationLink>
                      </PaginationItem>
                    )
                  )}

                  <PaginationItem>
                    <PaginationNext
                      href="#"
                      onClick={(e) => {
                        e.preventDefault()
                        setPage((p) => Math.min(totalPages, p + 1))
                      }}
                      className={
                        currentPage === totalPages
                          ? "pointer-events-none opacity-50"
                          : ""
                      }
                    />
                  </PaginationItem>
                </PaginationContent>
              </Pagination>
            </div>
          )}
        </Card>
      </div>

      <Sheet open={isDrawerOpen} onOpenChange={setIsDrawerOpen}>
        <SheetContent
          side="right"
          className="flex h-full w-full flex-col overflow-hidden p-0 data-[side=right]:w-full data-[side=right]:sm:max-w-[50vw]"
        >
          {selectedApp && (
            <div className="flex h-full flex-col">
              <SheetHeader className="shrink-0 space-y-2 border-b border-border/60 p-6 text-left">
                <SheetDescription>
                  Applications /{" "}
                  <span className="font-medium text-foreground">
                    {selectedApp.company_name}
                  </span>
                </SheetDescription>
                <SheetTitle className="font-heading text-xl">
                  {selectedApp.company_name}
                </SheetTitle>
                <p className="text-sm font-semibold text-primary">
                  {selectedApp.position}
                </p>
                <div className="flex flex-wrap gap-2 pt-1 text-xs text-muted-foreground">
                  <span className="inline-flex items-center gap-1 rounded-md border border-border bg-muted/50 px-2.5 py-1">
                    <BriefcaseIcon className="size-3.5" />
                    {selectedApp.job_type || "N/A"}
                  </span>
                  <span className="inline-flex items-center gap-1 rounded-md border border-border bg-muted/50 px-2.5 py-1">
                    <MapPinIcon className="size-3.5" />
                    {selectedApp.city || "N/A"}
                  </span>
                  <span className="inline-flex items-center gap-1 rounded-md border border-border bg-muted/50 px-2.5 py-1">
                    <CalendarIcon className="size-3.5" />
                    {selectedApp.hours_per_week || 40} hrs/wk
                  </span>
                  <span className="inline-flex items-center gap-1 rounded-md border border-border bg-muted/50 px-2.5 py-1">
                    <GlobeIcon className="size-3.5" />
                    {selectedApp.source || "—"}
                  </span>
                </div>
              </SheetHeader>

              {/* Section switcher — replaces TabsList */}
              <div className="shrink-0 border-b border-border/60 px-6 py-3">
                <Select value={activeTab} onValueChange={setActiveTab}>
                  <SelectTrigger className="w-full">

                    <SelectValue placeholder="Select section" />
                  </SelectTrigger>
                  <SelectContent position="popper">
                    <SelectItem value="general">General</SelectItem>
                    <SelectItem value="description">Job description</SelectItem>
                    <SelectItem value="resume">
                      Resume{selectedApp.resume_path ? " •" : ""}
                    </SelectItem>
                    <SelectItem value="interviews">
                      Interviews (
                      {(selectedApp.interviews || selectedApp.Interviews || [])
                        .length}
                      )
                    </SelectItem>
                  </SelectContent>
                </Select>
              </div>

              {/* Content — replaces TabsContent */}
              <div className="flex-1 space-y-6 overflow-y-auto p-6">
                {activeTab === "general" && (
                  <div className="space-y-6">
                    <div className="rounded-xl border border-border/60 bg-primary/5 p-4">
                      <h4 className="mb-3 text-[11px] font-bold tracking-widest text-muted-foreground uppercase">
                        Estimated compensation
                      </h4>
                      <div className="grid grid-cols-2 gap-3 text-center sm:grid-cols-4">
                        {["Hourly", "Daily", "Weekly", "Monthly"].map((period) => (
                          <div key={period}>
                            <span className="block text-[10px] font-bold text-muted-foreground uppercase">
                              {period}
                            </span>
                            <span className="pointer-events-none text-sm font-semibold tabular-nums select-none">
                              {selectedApp.currency || ""}{" "}
                              {selectedApp.calculated_earnings?.[period] || "0.00"}
                            </span>
                          </div>
                        ))}
                      </div>
                    </div>

                    {String(selectedApp.currency || "").toUpperCase() !== "PHP" && (
                      <div
                        className="pointer-events-none select-none rounded-xl border border-border/60 bg-muted/30 p-4"
                        aria-live="polite"
                      >
                        <h4 className="mb-1 text-[11px] font-bold tracking-widest text-muted-foreground uppercase">
                          Monthly equivalent in PHP
                        </h4>
                        {PhpConversionLoading ? (
                          <p className="text-sm text-muted-foreground">Converting…</p>
                        ) : PhpConversionError ? (
                          <p className="text-sm text-muted-foreground">{PhpConversionError}</p>
                        ) : PhpConversion ? (
                          <>
                            <p className="text-lg font-semibold tabular-nums text-foreground">
                              {FormatPhpAmount(PhpConversion.amount)}
                            </p>
                            <p className="mt-1 text-xs text-muted-foreground">
                              Based on{" "}
                              {selectedApp.currency}{" "}
                              {selectedApp.calculated_earnings?.Monthly ?? "0.00"}{" "}
                              monthly · rate ≈{" "}
                              {Number(PhpConversion.rate).toFixed(4)} PHP per{" "}
                              {selectedApp.currency}
                            </p>
                          </>
                        ) : (
                          <p className="text-sm text-muted-foreground">—</p>
                        )}
                      </div>
                    )}

                    <div className="space-y-3">
                      <h4 className="text-[11px] font-bold tracking-widest text-muted-foreground uppercase">
                        Assessment
                      </h4>
                      <div>
                        <span className="text-xs font-bold text-emerald-600 uppercase dark:text-emerald-400">
                          Pros
                        </span>
                        <p className="mt-1 text-sm text-muted-foreground">
                          {selectedApp.pros || "None specified"}
                        </p>
                      </div>
                      <div>
                        <span className="text-xs font-bold text-rose-600 uppercase dark:text-rose-400">
                          Cons
                        </span>
                        <p className="mt-1 text-sm text-muted-foreground">
                          {selectedApp.cons || "None specified"}
                        </p>
                      </div>
                    </div>
                  </div>
                )}

                {activeTab === "description" && (
                  <div className="space-y-4">
                    {selectedApp.ApplicationURL && (
                      <Button asChild variant="outline" size="sm">
                        <a
                          href={selectedApp.ApplicationURL}
                          target="_blank"
                          rel="noopener noreferrer"
                        >
                          <ExternalLinkIcon />
                          Open original listing
                        </a>
                      </Button>
                    )}
                    <h4 className="text-[11px] font-bold tracking-widest text-muted-foreground uppercase">
                      Full description
                    </h4>
                    <p className="text-sm leading-relaxed whitespace-pre-line text-muted-foreground">
                      {selectedApp.description || "No description provided."}
                    </p>
                  </div>
                )}

                {activeTab === "resume" && (
                  <div className="space-y-4">
                    {!selectedApp.resume_path ? (
                      <div className="flex flex-col items-center justify-center gap-3 rounded-lg border border-dashed border-border/60 bg-muted/20 py-14 text-center">
                        <FileTextIcon className="size-8 text-muted-foreground" />
                        <div>
                          <p className="text-sm font-medium">No resume attached</p>
                          <p className="text-xs text-muted-foreground">
                            Edit this application to upload the resume you sent.
                          </p>
                        </div>
                      </div>
                    ) : (
                      <>
                        <div className="flex flex-wrap items-center gap-2 rounded-lg border border-border/60 bg-muted/30 p-3">
                          <FileTextIcon className="size-4 shrink-0 text-primary" />
                          <span
                            className="min-w-0 flex-1 truncate text-sm font-medium"
                            title={selectedApp.resume_original_name}
                          >
                            {selectedApp.resume_original_name || "Resume"}
                          </span>
                          <Button asChild variant="outline" size="sm">
                            <a
                              href={selectedApp.resume_url}
                              target="_blank"
                              rel="noopener noreferrer"
                            >
                              <ExternalLinkIcon />
                              Open
                            </a>
                          </Button>
                          <Button asChild variant="outline" size="sm">
                            <a href={selectedApp.resume_url} download>
                              <DownloadIcon />
                              Download
                            </a>
                          </Button>
                        </div>

                        {(() => {
                          const kind = getResumeKind(selectedApp.resume_url || "")
                          if (kind === "pdf") {
                            return (
                              <div className="overflow-hidden rounded-lg border border-border/60 bg-muted/10">
                                <iframe
                                  src={selectedApp.resume_url}
                                  title="Resume preview"
                                  className="h-[520px] w-full"
                                />
                              </div>
                            )
                          }
                          if (kind === "image") {
                            return (
                              <div className="overflow-hidden rounded-lg border border-border/60 bg-muted/10 p-2">
                                <img
                                  src={selectedApp.resume_url}
                                  alt={selectedApp.resume_original_name || "Resume"}
                                  className="mx-auto max-h-[520px] w-auto rounded"
                                />
                              </div>
                            )
                          }
                          return (
                            <div className="rounded-lg border border-border/60 bg-muted/20 p-6 text-center text-sm text-muted-foreground">
                              Inline preview isn't available for this file type. Use{" "}
                              <span className="font-medium text-foreground">Open</span>{" "}
                              or{" "}
                              <span className="font-medium text-foreground">
                                Download
                              </span>{" "}
                              above.
                            </div>
                          )
                        })()}
                      </>
                    )}
                  </div>
                )}

                {activeTab === "interviews" && (
                  <div className="space-y-6">
                    <div className="space-y-3">
                      <h4 className="text-[11px] font-bold tracking-widest text-muted-foreground uppercase">
                        Logged rounds
                      </h4>
                      {(selectedApp.interviews || selectedApp.Interviews || [])
                        .length === 0 && (
                        <p className="text-sm text-muted-foreground">
                          No interviews logged yet.
                        </p>
                      )}
                      {(selectedApp.interviews || selectedApp.Interviews || []).map(
                        (interview) => (
                          <div
                            key={interview.id}
                            className="rounded-lg border border-border/60 bg-muted/30 p-3"
                          >
                            <div className="mb-1 flex items-center justify-between gap-2">
                              <span className="text-sm font-semibold">
                                {interview.interview_type}
                              </span>
                              <Badge
                                variant="outline"
                                className="text-[10px] uppercase"
                              >
                                {interview.status}
                              </Badge>
                            </div>
                            <p className="mb-1 text-xs text-muted-foreground">
                              {interview.scheduled_at
                                ? new Date(interview.scheduled_at).toLocaleString()
                                : "Date not set"}
                            </p>
                            <p className="text-xs text-muted-foreground">
                              {interview.notes || "No notes added."}
                            </p>
                          </div>
                        )
                      )}
                    </div>

                    <form
                      id="interview-form"
                      onSubmit={handleInterviewSubmit}
                      className="space-y-4 border-t border-border/60 pt-4"
                    >
                      <h4 className="text-[11px] font-bold tracking-widest text-muted-foreground uppercase">
                        Log new round
                      </h4>
                      <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div className="space-y-1.5">
                          <Label>Interview type</Label>
                          <Select
                            value={interviewForm.data.interview_type}
                            onValueChange={(v) =>
                              interviewForm.setData("interview_type", v)
                            }
                          >
                            <SelectTrigger className="w-full">
                              <SelectValue />
                            </SelectTrigger>
                            <SelectContent position="popper">
                              <SelectItem value="HR Screening">
                                HR Screening
                              </SelectItem>
                              <SelectItem value="Technical Assessment">
                                Technical Assessment
                              </SelectItem>
                              <SelectItem value="Technical Interview">
                                Technical Interview
                              </SelectItem>
                              <SelectItem value="Final / Executive">
                                Final / Executive
                              </SelectItem>
                            </SelectContent>
                          </Select>
                        </div>
                        <div className="space-y-1.5">
                          <Label>Scheduled date & time</Label>
                          <Input
                            type="datetime-local"
                            value={interviewForm.data.scheduled_at}
                            onChange={(e) =>
                              interviewForm.setData("scheduled_at", e.target.value)
                            }
                            required
                          />
                        </div>
                        <div className="space-y-1.5">
                          <Label>Status</Label>
                          <Select
                            value={interviewForm.data.status}
                            onValueChange={(v) =>
                              interviewForm.setData("status", v)
                            }
                          >
                            <SelectTrigger className="w-full">
                              <SelectValue />
                            </SelectTrigger>
                            <SelectContent position="popper">
                              <SelectItem value="Scheduled">Scheduled</SelectItem>
                              <SelectItem value="Completed">Completed</SelectItem>
                              <SelectItem value="Cancelled">Cancelled</SelectItem>
                            </SelectContent>
                          </Select>
                        </div>
                        <div className="space-y-1.5">
                          <Label>Notes / feedback</Label>
                          <Input
                            placeholder="Interviewer, tech stack..."
                            value={interviewForm.data.notes}
                            onChange={(e) =>
                              interviewForm.setData("notes", e.target.value)
                            }
                          />
                        </div>
                      </div>
                    </form>
                  </div>
                )}
              </div>

              <div className="flex shrink-0 items-center justify-between gap-3 border-t border-border/60 bg-background/90 p-4 backdrop-blur">
                {activeTab === "interviews" ? (
                  <>
                    <Button
                      type="button"
                      variant="outline"
                      onClick={() => {
                        openEdit(selectedApp)
                        setIsDrawerOpen(false)
                      }}
                    >
                      <PencilIcon />
                      Edit application
                    </Button>
                    <Button
                      type="submit"
                      form="interview-form"
                      disabled={interviewForm.processing}
                      className="flex-1"
                    >
                      Save interview
                    </Button>
                  </>
                ) : (
                  <>
                    <Button
                      variant="outline"
                      size="icon"
                      onClick={handleDeleteApplication}
                      className="border-destructive/30 text-destructive hover:bg-destructive/10"
                    >
                      <Trash2Icon />
                    </Button>
                    <Button
                      className="flex-1"
                      onClick={() => {
                        openEdit(selectedApp)
                        setIsDrawerOpen(false)
                      }}
                    >
                      <PencilIcon />
                      Edit application
                    </Button>
                  </>
                )}
              </div>
            </div>
          )}
        </SheetContent>
      </Sheet>

      <AlertDialog open={confirmDeleteOpen} onOpenChange={setConfirmDeleteOpen}>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>Delete this application?</AlertDialogTitle>
            <AlertDialogDescription>
              This permanently removes{" "}
              <span className="font-medium text-foreground">
                {selectedApp?.company_name}
              </span>{" "}
              and its interview history. This action cannot be undone.
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel>Cancel</AlertDialogCancel>
            <AlertDialogAction
              className="bg-destructive text-white hover:bg-destructive/90"
              onClick={confirmDelete}
            >
              Delete
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </>
  )
}

Applications.layoutProps = {
  active: "applications",
  title: "Applications",
  breadcrumbs: [
    { label: "Applications", href: route("job-applications.applications") },
  ],
}