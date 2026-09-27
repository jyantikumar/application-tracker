import React, { useState } from "react"
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
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table"
import { PencilIcon, PlusIcon, Trash2Icon } from "lucide-react"

export default function Statuses({ statuses = [] }) {
  const { flash } = usePage().props
  const [editing, setEditing] = useState(null)
  const createForm = useForm({ name: "" })
  const editForm = useForm({ name: "" })

  const openEdit = (status) => {
    setEditing(status)
    editForm.setData("name", status.name)
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
              <Button type="submit" disabled={createForm.processing} className="w-full">
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
              {statuses.length === 0 ? (
                <TableRow>
                  <TableCell colSpan={2} className="py-10 text-center text-muted-foreground">
                    No statuses yet. Add your first one.
                  </TableCell>
                </TableRow>
              ) : (
                statuses.map((status) => (
                  <TableRow key={status.id}>
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
                          onClick={() => {
                            if (confirm(`Delete "${status.name}"?`)) {
                              router.delete(
                                route("application-statuses.destroy", status.id)
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
        </Card>
      </div>

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
    </>
  )
}

Statuses.layoutProps = {
  active: "statuses",
  title: "Statuses",
  breadcrumbs: [{ label: "Statuses" }],
}