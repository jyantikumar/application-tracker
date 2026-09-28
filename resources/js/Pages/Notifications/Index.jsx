import React, { useEffect, useMemo, useState } from "react"
import { Head, Link, router } from "@inertiajs/react"
import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"
import { Card } from "@/components/ui/card"
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
  BellIcon,
  BriefcaseIcon,
  CalendarIcon,
  ClockIcon,
  ExternalLinkIcon,
} from "lucide-react"

import { cn } from "@/lib/utils"

const FILTERS = [
  { value: "all", label: "All" },
  { value: "active", label: "Active" },
  { value: "pending", label: "Pending" },
  { value: "scheduled", label: "Scheduled" },
  { value: "dismissed", label: "Dismissed" },
]

const STATUS_BADGE = {
  active: "default",
  pending: "secondary",
  scheduled: "outline",
  dismissed: "outline",
}

const TYPE_BADGE = {
  follow_up: "border-amber-500/40 text-amber-700 dark:text-amber-300",
  interview: "border-sky-500/40 text-sky-700 dark:text-sky-300",
  stale_app: "border-rose-500/40 text-rose-700 dark:text-rose-300",
}

function formatDateTime(value) {
  if (!value) return "—"
  return new Date(value).toLocaleString("en-US", {
    month: "short",
    day: "numeric",
    year: "numeric",
    hour: "numeric",
    minute: "2-digit",
  })
}

function DetailRow({ label, children }) {
  return (
    <div className="grid gap-1 border-b border-border/50 py-3 last:border-b-0 sm:grid-cols-[9rem_1fr] sm:gap-4">
      <dt className="text-[11px] font-bold tracking-widest text-muted-foreground uppercase">
        {label}
      </dt>
      <dd className="text-sm text-foreground">{children}</dd>
    </div>
  )
}

export default function NotificationsIndex({
  Reminders = [],
  Filter = "all",
  Counts = {},
}) {
  const [selected, setSelected] = useState(null)
  const [isOpen, setIsOpen] = useState(false)

  useEffect(() => {
    if (!selected?.id) return
    const fresh = Reminders.find((item) => item.id === selected.id)
    if (fresh) setSelected(fresh)
    else {
      setSelected(null)
      setIsOpen(false)
    }
  }, [Reminders])

  const openDetails = (reminder) => {
    setSelected(reminder)
    setIsOpen(true)
  }

  const setFilter = (value) => {
    router.get(
      route("notifications.index"),
      { filter: value === "all" ? undefined : value },
      { preserveState: true, preserveScroll: true }
    )
  }

  const dismiss = (id) => {
    router.post(route("reminders.dismiss", id), {}, { preserveScroll: true })
  }

  const snooze = (id, hours = 24) => {
    router.post(
      route("reminders.snooze", id),
      { hours },
      { preserveScroll: true }
    )
  }

  const dismissAll = () => {
    router.post(route("reminders.dismiss-all"), {}, { preserveScroll: true })
  }

  const emptyLabel = useMemo(() => {
    if (Filter === "active") return "No active notifications."
    if (Filter === "pending") return "No pending notifications."
    if (Filter === "scheduled") return "No scheduled notifications."
    if (Filter === "dismissed") return "No dismissed notifications."
    return "No notifications yet. Run the reminder processor to generate some."
  }, [Filter])

  return (
    <>
      <Head title="Notifications" />

      <div className="space-y-6">
        <div className="flex flex-col justify-between gap-4 md:flex-row md:items-end">
          <div>
            <h1 className="font-heading text-2xl font-semibold tracking-tight">
              Notifications
            </h1>
            <p className="mt-1 text-sm text-muted-foreground">
              Follow-ups, interview alerts, and stale application flags with full
              detail.
            </p>
          </div>

          <div className="flex flex-wrap items-center gap-2">
            {(Counts.active || 0) > 0 && (
              <Button variant="outline" size="sm" onClick={dismissAll}>
                Dismiss all active
              </Button>
            )}
            <Select value={Filter || "all"} onValueChange={setFilter}>
              <SelectTrigger className="w-[160px]">
                <SelectValue placeholder="Filter" />
              </SelectTrigger>
              <SelectContent position="popper">
                {FILTERS.map((filter) => (
                  <SelectItem key={filter.value} value={filter.value}>
                    {filter.label}
                    {Counts[filter.value] != null
                      ? ` (${Counts[filter.value]})`
                      : ""}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>
        </div>

        <div className="grid grid-cols-2 gap-3 sm:grid-cols-5">
          {FILTERS.map((filter) => (
            <button
              key={filter.value}
              type="button"
              onClick={() => setFilter(filter.value)}
              className={cn(
                "rounded-xl border border-border/60 bg-muted/20 px-3 py-3 text-left transition-colors hover:bg-muted/40",
                Filter === filter.value && "border-primary/40 bg-primary/5"
              )}
            >
              <p className="text-[10px] font-bold tracking-widest text-muted-foreground uppercase">
                {filter.label}
              </p>
              <p className="mt-1 text-xl font-semibold tabular-nums">
                {Counts[filter.value] ?? 0}
              </p>
            </button>
          ))}
        </div>

        <Card className="overflow-hidden border-border/60">
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Notification</TableHead>
                <TableHead className="hidden md:table-cell">Application</TableHead>
                <TableHead className="hidden sm:table-cell">Scheduled</TableHead>
                <TableHead>Status</TableHead>
                <TableHead className="w-[1%] text-right"> </TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {Reminders.length === 0 ? (
                <TableRow>
                  <TableCell colSpan={5} className="py-16 text-center">
                    <div className="mx-auto flex max-w-sm flex-col items-center gap-2">
                      <BellIcon className="size-8 text-muted-foreground" />
                      <p className="text-sm font-medium">Nothing here</p>
                      <p className="text-xs text-muted-foreground">{emptyLabel}</p>
                    </div>
                  </TableCell>
                </TableRow>
              ) : (
                Reminders.map((reminder) => (
                  <TableRow
                    key={reminder.id}
                    className="cursor-pointer"
                    onClick={() => openDetails(reminder)}
                  >
                    <TableCell>
                      <div className="space-y-1">
                        <div className="flex flex-wrap items-center gap-2">
                          <span className="font-medium">{reminder.title}</span>
                          <Badge
                            variant="outline"
                            className={cn(
                              "text-[10px] uppercase",
                              TYPE_BADGE[reminder.type]
                            )}
                          >
                            {reminder.type_label || reminder.type}
                          </Badge>
                        </div>
                        <p className="line-clamp-1 text-xs text-muted-foreground md:max-w-xl">
                          {reminder.body}
                        </p>
                      </div>
                    </TableCell>
                    <TableCell className="hidden md:table-cell">
                      {reminder.application ? (
                        <div>
                          <p className="text-sm font-medium">
                            {reminder.application.company_name}
                          </p>
                          <p className="text-xs text-muted-foreground">
                            {reminder.application.position}
                          </p>
                        </div>
                      ) : (
                        "—"
                      )}
                    </TableCell>
                    <TableCell className="hidden text-sm text-muted-foreground sm:table-cell">
                      {formatDateTime(reminder.scheduled_for)}
                    </TableCell>
                    <TableCell>
                      <Badge
                        variant={STATUS_BADGE[reminder.status_label] || "outline"}
                        className="text-[10px] uppercase"
                      >
                        {reminder.status_label}
                      </Badge>
                    </TableCell>
                    <TableCell className="text-right">
                      <Button
                        type="button"
                        size="sm"
                        variant="ghost"
                        onClick={(e) => {
                          e.stopPropagation()
                          openDetails(reminder)
                        }}
                      >
                        Details
                      </Button>
                    </TableCell>
                  </TableRow>
                ))
              )}
            </TableBody>
          </Table>
        </Card>
      </div>

      <Sheet open={isOpen} onOpenChange={setIsOpen}>
        <SheetContent
          side="right"
          className="flex h-full w-full flex-col overflow-hidden p-0 data-[side=right]:w-full data-[side=right]:sm:max-w-lg"
        >
          {selected && (
            <div className="flex h-full flex-col">
              <SheetHeader className="shrink-0 space-y-2 border-b border-border/60 p-6 text-left">
                <SheetDescription>Notification details</SheetDescription>
                <SheetTitle className="font-heading text-xl">
                  {selected.title}
                </SheetTitle>
                <div className="flex flex-wrap gap-2 pt-1">
                  <Badge
                    variant="outline"
                    className={cn(
                      "text-[10px] uppercase",
                      TYPE_BADGE[selected.type]
                    )}
                  >
                    {selected.type_label || selected.type}
                  </Badge>
                  <Badge
                    variant={STATUS_BADGE[selected.status_label] || "outline"}
                    className="text-[10px] uppercase"
                  >
                    {selected.status_label}
                  </Badge>
                </div>
              </SheetHeader>

              <div className="flex-1 overflow-y-auto p-6">
                <p className="text-sm leading-relaxed text-muted-foreground">
                  {selected.body}
                </p>

                <dl className="mt-6">
                  <DetailRow label="Type">
                    {selected.type_label || selected.type}
                    {selected.lead_hours
                      ? ` · ${selected.lead_hours}h lead`
                      : ""}
                  </DetailRow>
                  <DetailRow label="Status">{selected.status_label}</DetailRow>
                  <DetailRow label="Scheduled for">
                    {formatDateTime(selected.scheduled_for)}
                  </DetailRow>
                  <DetailRow label="Sent at">
                    {formatDateTime(selected.sent_at)}
                  </DetailRow>
                  <DetailRow label="Created">
                    {formatDateTime(selected.created_at)}
                  </DetailRow>
                  <DetailRow label="Updated">
                    {formatDateTime(selected.updated_at)}
                  </DetailRow>
                  <DetailRow label="Dismissed">
                    {selected.is_dismissed ? "Yes" : "No"}
                  </DetailRow>
                </dl>

                {selected.application && (
                  <div className="mt-6 space-y-3 rounded-xl border border-border/60 bg-muted/20 p-4">
                    <h4 className="flex items-center gap-2 text-[11px] font-bold tracking-widest text-muted-foreground uppercase">
                      <BriefcaseIcon className="size-3.5" />
                      Application
                    </h4>
                    <div className="space-y-1">
                      <p className="text-sm font-semibold">
                        {selected.application.company_name}
                      </p>
                      <p className="text-sm text-primary">
                        {selected.application.position}
                      </p>
                      <p className="text-xs text-muted-foreground">
                        Status: {selected.application.status || "—"}
                        {selected.application.city
                          ? ` · ${selected.application.city}`
                          : ""}
                        {selected.application.source
                          ? ` · ${selected.application.source}`
                          : ""}
                      </p>
                    </div>
                    <Button asChild variant="outline" size="sm">
                      <Link href={route("job-applications.applications")}>
                        <ExternalLinkIcon />
                        Open applications
                      </Link>
                    </Button>
                  </div>
                )}

                {selected.interview && (
                  <div className="mt-4 space-y-3 rounded-xl border border-border/60 bg-muted/20 p-4">
                    <h4 className="flex items-center gap-2 text-[11px] font-bold tracking-widest text-muted-foreground uppercase">
                      <CalendarIcon className="size-3.5" />
                      Interview
                    </h4>
                    <div className="space-y-1 text-sm">
                      <p className="font-semibold">
                        {selected.interview.interview_type}
                      </p>
                      <p className="text-muted-foreground">
                        {formatDateTime(selected.interview.scheduled_at)}
                      </p>
                      <Badge variant="outline" className="text-[10px] uppercase">
                        {selected.interview.status}
                      </Badge>
                      {selected.interview.notes && (
                        <p className="pt-2 text-xs text-muted-foreground">
                          {selected.interview.notes}
                        </p>
                      )}
                    </div>
                  </div>
                )}
              </div>

              <div className="flex shrink-0 flex-wrap items-center gap-2 border-t border-border/60 bg-background/90 p-4 backdrop-blur">
                {!selected.is_dismissed && (
                  <>
                    <Button
                      type="button"
                      variant="outline"
                      onClick={() => snooze(selected.id, 24)}
                    >
                      <ClockIcon />
                      Snooze 24h
                    </Button>
                    <Button
                      type="button"
                      variant="ghost"
                      onClick={() => dismiss(selected.id)}
                    >
                      Dismiss
                    </Button>
                  </>
                )}
                {selected.is_dismissed && (
                  <p className="text-sm text-muted-foreground">
                    This notification has been dismissed.
                  </p>
                )}
              </div>
            </div>
          )}
        </SheetContent>
      </Sheet>
    </>
  )
}

NotificationsIndex.layoutProps = {
  active: "notifications",
  title: "Notifications",
  breadcrumbs: [{ label: "Notifications", href: route("notifications.index") }],
}
