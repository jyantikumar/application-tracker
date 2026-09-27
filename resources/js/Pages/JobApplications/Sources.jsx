import React, { useEffect, useMemo, useState } from "react"
import { Head, router, useForm, usePage } from "@inertiajs/react"
import { Alert, AlertDescription } from "@/components/ui/alert"
import { Button } from "@/components/ui/button"
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog"
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
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table"
import { PencilIcon, PlusIcon, Trash2Icon } from "lucide-react"

import { cn } from "@/lib/utils"

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

export default function Sources({ sources = [] }) {
  const { flash } = usePage().props
  const [editing, setEditing] = useState(null)
  const createForm = useForm({ name: "" })
  const editForm = useForm({ name: "" })

  // ---- Pagination state ----
  const [page, setPage] = useState(1)
  const [pageSize, setPageSize] = useState(10)

  // Reset to page 1 whenever the number of sources changes
  useEffect(() => {
    setPage(1)
  }, [sources.length, pageSize])

  const totalPages = Math.max(1, Math.ceil(sources.length / pageSize))
  const currentPage = Math.min(page, totalPages)

  const paginatedSources = useMemo(() => {
    const start = (currentPage - 1) * pageSize
    return sources.slice(start, start + pageSize)
  }, [sources, currentPage, pageSize])

  const openEdit = (source) => {
    setEditing(source)
    editForm.setData("name", source.name)
  }

  return (
    <>
      <Head title="Application Sources" />

      {flash?.success && (
        <Alert className="border-primary/30 bg-primary/10 text-primary">
          <AlertDescription>{flash.success}</AlertDescription>
        </Alert>
      )}

      <div className="grid gap-6 lg:grid-cols-[320px_1fr]">
        <Card className="h-fit">
          <CardHeader>
            <CardTitle className="font-heading text-base">Add source</CardTitle>
          </CardHeader>
          <CardContent>
            <form
              className="space-y-4"
              onSubmit={(e) => {
                e.preventDefault()
                createForm.post(route("application-sources.store"), {
                  onSuccess: () => createForm.reset(),
                })
              }}
            >
              <div className="space-y-2">
                <Label htmlFor="source-name">Name</Label>
                <Input
                  id="source-name"
                  value={createForm.data.name}
                  onChange={(e) => createForm.setData("name", e.target.value)}
                  placeholder="LinkedIn, Indeed..."
                  required
                />
              </div>
              <Button
                type="submit"
                disabled={createForm.processing}
                className="w-full"
              >
                <PlusIcon />
                Add source
              </Button>
            </form>
          </CardContent>
        </Card>

        <Card className="gap-0 overflow-hidden py-0">
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Name</TableHead>
                <TableHead className="w-[120px] text-right">Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {paginatedSources.length === 0 ? (
                <TableRow>
                  <TableCell
                    colSpan={2}
                    className="py-10 text-center text-muted-foreground"
                  >
                    No sources yet. Add your first one.
                  </TableCell>
                </TableRow>
              ) : (
                paginatedSources.map((source, index) => (
                  <TableRow
                    key={source.id}
                    className={cn(
                      index % 2 === 0
                        ? "bg-background"
                        : "bg-muted/40 hover:bg-muted/60"
                    )}
                  >
                    <TableCell className="font-medium">{source.name}</TableCell>
                    <TableCell className="text-right">
                      <div className="inline-flex gap-1">
                        <Button
                          variant="ghost"
                          size="icon-sm"
                          onClick={() => openEdit(source)}
                        >
                          <PencilIcon />
                        </Button>
                        <Button
                          variant="ghost"
                          size="icon-sm"
                          className="text-destructive"
                          onClick={() => {
                            if (confirm(`Delete "${source.name}"?`)) {
                              router.delete(
                                route("application-sources.destroy", source.id)
                              )
                            }
                          }}
                        >
                          <Trash2Icon />
                        </Button>
                      </div>
                    </TableCell>
                  </TableRow>
                ))
              )}
            </TableBody>
          </Table>

          {/* Pagination footer */}
          {sources.length > 0 && (
            <div className="flex flex-col items-center justify-between gap-3 border-t border-border/60 px-4 py-3 sm:flex-row">
              <div className="flex flex-wrap items-center gap-3 text-xs text-muted-foreground">
                <span>
                  Showing{" "}
                  <span className="font-medium text-foreground">
                    {(currentPage - 1) * pageSize + 1}
                  </span>
                  –
                  <span className="font-medium text-foreground">
                    {Math.min(currentPage * pageSize, sources.length)}
                  </span>{" "}
                  of{" "}
                  <span className="font-medium text-foreground">
                    {sources.length}
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

      <Dialog open={!!editing} onOpenChange={(open) => !open && setEditing(null)}>
        <DialogContent className="max-w-md">
          <DialogHeader>
            <DialogTitle>Rename source</DialogTitle>
            <DialogDescription>Update the display name for this source.</DialogDescription>
          </DialogHeader>
          <form
            onSubmit={(e) => {
              e.preventDefault()
              if (!editing) return
              editForm.put(route("application-sources.update", editing.id), {
                onSuccess: () => setEditing(null),
              })
            }}
            className="space-y-4"
          >
            <div className="space-y-2">
              <Label>Name</Label>
              <Input
                value={editForm.data.name}
                onChange={(e) => editForm.setData("name", e.target.value)}
                required
              />
            </div>
            <DialogFooter>
              <Button type="button" variant="outline" onClick={() => setEditing(null)}>
                Cancel
              </Button>
              <Button type="submit" disabled={editForm.processing}>
                Save
              </Button>
            </DialogFooter>
          </form>
        </DialogContent>
      </Dialog>
    </>
  )
}

Sources.layoutProps = {
  active: "sources",
  title: "Sources",
  breadcrumbs: [{ label: "Sources" }],
}