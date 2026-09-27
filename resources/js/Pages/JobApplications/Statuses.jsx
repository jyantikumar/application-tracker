import React, { useEffect, useMemo, useState } from "react"
import { Head, router, useForm, usePage } from "@inertiajs/react"
import { Alert, AlertDescription } from "@/components/ui/alert"
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

export default function Statuses({ statuses = [] }) {
  const { flash } = usePage().props
  const [editing, setEditing] = useState(null)
  const [deleteTarget, setDeleteTarget] = useState(null)  // 👈 new
  const createForm = useForm({ name: "" })
  const editForm = useForm({ name: "" })

  // ---- Pagination state ----
  const [page, setPage] = useState(1)
  const [pageSize, setPageSize] = useState(10)

  useEffect(() => {
    setPage(1)
  }, [statuses.length, pageSize])

  const totalPages = Math.max(1, Math.ceil(statuses.length / pageSize))
  const currentPage = Math.min(page, totalPages)

  const paginatedStatuses = useMemo(() => {
    const start = (currentPage - 1) * pageSize
    return statuses.slice(start, start + pageSize)
  }, [statuses, currentPage, pageSize])

  const openEdit = (status) => {
    setEditing(status)
    editForm.setData("name", status.name)
  }

  const confirmDelete = () => {
    if (!deleteTarget) return
    router.delete(route("application-statuses.destroy", deleteTarget.id), {
      onSuccess: () => setDeleteTarget(null),
    })
  }

  return (
    <>
      <Head title="Application Statuses" />

      {flash?.success && (
        <Alert className="border-primary/30 bg-primary/10 text-primary">
          <AlertDescription>{flash.success}</AlertDescription>
        </Alert>
      )}

      <div className="grid gap-6 lg:grid-cols-[320px_1fr]">
        <Card className="h-fit">
          <CardHeader>
            <CardTitle className="font-heading text-base">Add status</CardTitle>
          </CardHeader>
          <CardContent>
            <form
              className="space-y-4"
              onSubmit={(e) => {
                e.preventDefault()
                createForm.post(route("application-statuses.store"), {
                  onSuccess: () => createForm.reset(),
                })
              }}
            >
              <div className="space-y-2">
                <Label htmlFor="status-name">Name</Label>
                <Input
                  id="status-name"
                  value={createForm.data.name}
                  onChange={(e) => createForm.setData("name", e.target.value)}
                  placeholder="Applied, Interviewing..."
                  required
                />
              </div>
              <Button
                type="submit"
                disabled={createForm.processing}
                className="w-full"
              >
                <PlusIcon />
                Add status
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
              {paginatedStatuses.length === 0 ? (
                <TableRow>
                  <TableCell
                    colSpan={2}
                    className="py-10 text-center text-muted-foreground"
                  >
                    No statuses yet. Add your first one.
                  </TableCell>
                </TableRow>
              ) : (
                paginatedStatuses.map((status, index) => (
                  <TableRow
                    key={status.id}
                    className={cn(
                      index % 2 === 0
                        ? "bg-background"
                        : "bg-muted/40 hover:bg-muted/60"
                    )}
                  >
                    <TableCell className="font-medium">{status.name}</TableCell>
                    <TableCell className="text-right">
                      <div className="inline-flex gap-1">
                        <Button
                          variant="ghost"
                          size="icon-sm"
                          onClick={() => openEdit(status)}
                        >
                          <PencilIcon />
                        </Button>
                        <Button
                          variant="ghost"
                          size="icon-sm"
                          className="text-destructive"
                          onClick={() => setDeleteTarget(status)}  // 👈 changed
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
          {statuses.length > 0 && (
            <div className="flex flex-col items-center justify-between gap-3 border-t border-border/60 px-4 py-3 sm:flex-row">
              <div className="flex flex-wrap items-center gap-3 text-xs text-muted-foreground">
                <span>
                  Showing{" "}
                  <span className="font-medium text-foreground">
                    {(currentPage - 1) * pageSize + 1}
                  </span>
                  –
                  <span className="font-medium text-foreground">
                    {Math.min(currentPage * pageSize, statuses.length)}
                  </span>{" "}
                  of{" "}
                  <span className="font-medium text-foreground">
                    {statuses.length}
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

      {/* Rename dialog */}
      <Dialog open={!!editing} onOpenChange={(open) => !open && setEditing(null)}>
        <DialogContent className="max-w-md">
          <DialogHeader>
            <DialogTitle>Rename status</DialogTitle>
            <DialogDescription>Update the display name for this status.</DialogDescription>
          </DialogHeader>
          <form
            onSubmit={(e) => {
              e.preventDefault()
              if (!editing) return
              editForm.put(route("application-statuses.update", editing.id), {
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

      {/* 👇 Delete confirmation dialog */}
      <AlertDialog
        open={!!deleteTarget}
        onOpenChange={(open) => !open && setDeleteTarget(null)}
      >
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>Delete this status?</AlertDialogTitle>
            <AlertDialogDescription>
              This permanently removes{" "}
              <span className="font-medium text-foreground">
                {deleteTarget?.name}
              </span>
              . Applications using this status will keep their text but the
              status will no longer appear in your list.
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel>Cancel</AlertDialogCancel>
            <AlertDialogAction
              className="bg-destructive text-white hover:bg-destructive/90"
              onClick={(e) => {
                e.preventDefault()
                confirmDelete()
              }}
            >
              Delete
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </>
  )
}

Statuses.layoutProps = {
  active: "statuses",
  title: "Statuses",
  breadcrumbs: [{ label: "Statuses" }],
}