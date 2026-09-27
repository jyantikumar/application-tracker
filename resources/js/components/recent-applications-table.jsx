import React, { useEffect, useMemo, useState } from "react"
import { Link } from "@inertiajs/react"
import {
  ArrowUpRightIcon,
  BriefcaseIcon,
  ChevronRightIcon,
} from "lucide-react"

import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"
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
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table"

import { cn } from "@/lib/utils"

/**
 * Build a compact pagination range like [1, "...", 4, 5, 6, "...", 20]
 */
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

function formatDate(value) {
  if (!value) return "—"
  return new Date(value).toLocaleDateString("en-US", {
    month: "short",
    day: "numeric",
    year: "numeric",
  })
}

export function RecentApplicationsTable({ RecentApplications = [] }) {
  const [page, setPage] = useState(1)
  const [pageSize, setPageSize] = useState(5)

  // Reset to page 1 whenever data or page size changes
  useEffect(() => {
    setPage(1)
  }, [RecentApplications.length, pageSize])

  const totalPages = Math.max(1, Math.ceil(RecentApplications.length / pageSize))
  const currentPage = Math.min(page, totalPages)

  const paginated = useMemo(() => {
    const start = (currentPage - 1) * pageSize
    return RecentApplications.slice(start, start + pageSize)
  }, [RecentApplications, currentPage, pageSize])

  return (
    <section className="px-4 lg:px-6">
      <Card className="gap-0 overflow-hidden py-0">
        <CardHeader className="flex flex-row items-center justify-between gap-2 border-b border-border/60 px-6 py-4">
          <div>
            <CardTitle className="font-heading text-base">
              Recent applications
            </CardTitle>
            <p className="mt-0.5 text-xs text-muted-foreground">
              The latest records you've logged.
            </p>
          </div>
          <Button asChild variant="outline" size="sm" className="gap-1.5">
            <Link href={route("job-applications.applications")}>
              View all
              <ArrowUpRightIcon className="size-3.5" />
            </Link>
          </Button>
        </CardHeader>

        <CardContent className="p-0">
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Company & position</TableHead>
                <TableHead>Work setup</TableHead>
                <TableHead>Compensation</TableHead>
                <TableHead>Date applied</TableHead>
                <TableHead>Status</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {paginated.length > 0 ? (
                paginated.map((app, index) => (
                  <TableRow
                    key={app.id}
                    className={cn(
                      "cursor-pointer",
                      index % 2 === 0
                        ? "bg-background"
                        : "bg-muted/40 hover:bg-muted/60"
                    )}
                  >
                    <TableCell>
                      <div className="font-medium">{app.company_name}</div>
                      <div className="mt-0.5 inline-flex items-center gap-1 text-xs font-medium text-primary">
                        {app.position}
                        <ChevronRightIcon className="size-3 opacity-0 transition-opacity group-hover:opacity-100" />
                      </div>
                    </TableCell>
                    <TableCell>
                      <div className="inline-flex items-center gap-1.5">
                        <BriefcaseIcon className="size-3.5 text-muted-foreground" />
                        <span>{app.job_type || "—"}</span>
                      </div>
                      <div className="text-xs text-muted-foreground">
                        {app.city || "N/A"}
                      </div>
                    </TableCell>
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
                    <TableCell className="tabular-nums text-muted-foreground">
                      {formatDate(app.created_at)}
                    </TableCell>
                    <TableCell>
                      <Badge variant="outline">{app.status}</Badge>
                    </TableCell>
                  </TableRow>
                ))
              ) : (
                <TableRow>
                  <TableCell
                    colSpan={5}
                    className="py-10 text-center text-muted-foreground"
                  >
                    No recent applications yet.
                  </TableCell>
                </TableRow>
              )}
            </TableBody>
          </Table>

          {/* Pagination footer */}
          {RecentApplications.length > 0 && (
            <div className="flex flex-col items-center justify-between gap-3 border-t border-border/60 px-6 py-3 sm:flex-row">
              <div className="flex flex-wrap items-center gap-3 text-xs text-muted-foreground">
                <span>
                  Showing{" "}
                  <span className="font-medium text-foreground">
                    {(currentPage - 1) * pageSize + 1}
                  </span>
                  –
                  <span className="font-medium text-foreground">
                    {Math.min(currentPage * pageSize, RecentApplications.length)}
                  </span>{" "}
                  of{" "}
                  <span className="font-medium text-foreground">
                    {RecentApplications.length}
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
                      {[5, 10, 20].map((n) => (
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
        </CardContent>
      </Card>
    </section>
  )
}