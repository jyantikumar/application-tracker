import React, { useState } from "react"
import { Head, Link, useForm } from "@inertiajs/react"
import AppLayout from "@/layouts/AppLayout"
import { toOptionNames } from "@/lib/options"
import { Alert, AlertDescription, AlertTitle } from "@/components/ui/alert"
import { Button } from "@/components/ui/button"
import { Card, CardContent } from "@/components/ui/card"
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
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select"
import { Textarea } from "@/components/ui/textarea"
import { AlertCircleIcon, ArrowLeftIcon, PlusIcon } from "lucide-react"

const DEFAULT_SOURCES = ["LinkedIn", "Indeed", "Referral"]
const DEFAULT_STATUSES = ["Applied", "Under Review", "Interviewing"]

export default function Create({
  sources = [],
  statuses = [],
  CustomSources,
  CustomStatuses,
}) {
  const [sourcesList, setSourcesList] = useState(() =>
    toOptionNames(CustomSources ?? sources, DEFAULT_SOURCES)
  )
  const [statusesList, setStatusesList] = useState(() =>
    toOptionNames(CustomStatuses ?? statuses, DEFAULT_STATUSES)
  )
  const [isAddSourceOpen, setIsAddSourceOpen] = useState(false)
  const [isAddStatusOpen, setIsAddStatusOpen] = useState(false)
  const [newSourceInput, setNewSourceInput] = useState("")
  const [newStatusInput, setNewStatusInput] = useState("")
  const [isAddingOption, setIsAddingOption] = useState(false)

  const form = useForm({
    company_name: "",
    position: "",
    job_type: "Remote",
    city: "",
    currency: "USD",
    salary: "",
    salary_min: "",
    salary_max: "",
    pay_rate_type: "Hourly",
    hours_per_week: "40",
    ApplicationURL: "",
    source: sourcesList[0] || "LinkedIn",
    status: statusesList[0] || "Applied",
    description: "",
    pros: "",
    cons: "",
  })

  const handleSubmit = (e) => {
    e.preventDefault()
    form.post(route("job-applications.store"))
  }

  const persistOption = async (endpoint, name, onSuccess) => {
    if (!name.trim()) return
    setIsAddingOption(true)
    try {
      const response = await fetch(endpoint, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN":
            document
              .querySelector('meta[name="csrf-token"]')
              ?.getAttribute("content") || "",
          Accept: "application/json",
        },
        body: JSON.stringify({ name: name.trim() }),
      })
      if (!response.ok) throw new Error("Failed")
      onSuccess(name.trim())
    } catch {
      alert("Could not save option. Please try again.")
    } finally {
      setIsAddingOption(false)
    }
  }

  return (
    <AppLayout
      active="applications"
      title="Log Application"
      breadcrumbs={[
        { label: "Applications", href: route("job-applications.applications") },
        { label: "Log application" },
      ]}
    >
      <Head title="Log New Application" />

      <div className="mx-auto max-w-6xl space-y-6">
        <div className="flex items-start justify-between gap-4">
          <div>
            <h1 className="font-heading text-2xl font-semibold tracking-tight">
              Log new application
            </h1>
            <p className="mt-1 text-sm text-muted-foreground">
              Record a submitted role so you can track pipeline progress.
            </p>
          </div>
          <Button asChild variant="ghost" size="sm">
            <Link href={route("job-applications.applications")}>
              <ArrowLeftIcon />
              Back
            </Link>
          </Button>
        </div>

        {Object.keys(form.errors).length > 0 && (
          <Alert variant="destructive">
            <AlertCircleIcon />
            <AlertTitle>Please fix validation errors</AlertTitle>
            <AlertDescription className="mt-2 space-y-1 text-xs">
              {Object.entries(form.errors).map(([field, message]) => (
                <p key={field}>• {message}</p>
              ))}
            </AlertDescription>
          </Alert>
        )}

        <Card className="border-border/60 bg-card/80 shadow-none">
          <CardContent>
            <form
              onSubmit={handleSubmit}
              className="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-12"
            >
              <div className="space-y-2 lg:col-span-4">
                <Label>Company name *</Label>
                <Input
                  value={form.data.company_name}
                  onChange={(e) => form.setData("company_name", e.target.value)}
                  placeholder="e.g. Stripe"
                  required
                />
              </div>
              <div className="space-y-2 lg:col-span-4">
                <Label>Position *</Label>
                <Input
                  value={form.data.position}
                  onChange={(e) => form.setData("position", e.target.value)}
                  placeholder="e.g. Frontend Developer"
                  required
                />
              </div>
              <div className="space-y-2 lg:col-span-2">
                <Label>Work setup *</Label>
                <Select
                  value={form.data.job_type}
                  onValueChange={(v) => form.setData("job_type", v)}
                >
                  <SelectTrigger>
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    {["Remote", "Hybrid", "Onsite", "Contract", "Freelance"].map(
                      (type) => (
                        <SelectItem key={type} value={type}>
                          {type}
                        </SelectItem>
                      )
                    )}
                  </SelectContent>
                </Select>
              </div>
              <div className="space-y-2 lg:col-span-2">
                <Label>City *</Label>
                <Input
                  value={form.data.city}
                  onChange={(e) => form.setData("city", e.target.value)}
                  placeholder="Manila / Remote"
                  required
                />
              </div>

              <div className="space-y-4 rounded-xl border border-border/60 bg-muted/30 p-4 lg:col-span-12">
                <div className="flex flex-wrap items-center justify-between gap-2">
                  <h3 className="text-xs font-bold tracking-wider text-primary uppercase">
                    Compensation log
                  </h3>
                  <span className="text-[11px] text-muted-foreground">
                    {form.data.job_type === "Remote"
                      ? "Exact remote rate"
                      : "Onsite / hybrid salary range"}
                  </span>
                </div>

                {form.data.job_type === "Remote" ? (
                  <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div className="space-y-2">
                      <Label>Currency *</Label>
                      <Select
                        value={form.data.currency}
                        onValueChange={(v) => form.setData("currency", v)}
                      >
                        <SelectTrigger>
                          <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                          {["USD", "PHP", "EUR", "GBP", "AUD"].map((c) => (
                            <SelectItem key={c} value={c}>
                              {c}
                            </SelectItem>
                          ))}
                        </SelectContent>
                      </Select>
                    </div>
                    <div className="space-y-2">
                      <Label>Amount *</Label>
                      <Input
                        type="number"
                        step="0.01"
                        value={form.data.salary}
                        onChange={(e) => form.setData("salary", e.target.value)}
                        required
                      />
                    </div>
                    <div className="space-y-2">
                      <Label>Pay frequency *</Label>
                      <Select
                        value={form.data.pay_rate_type}
                        onValueChange={(v) => form.setData("pay_rate_type", v)}
                      >
                        <SelectTrigger>
                          <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                          <SelectItem value="Hourly">Hourly rate</SelectItem>
                          <SelectItem value="Monthly">Monthly rate</SelectItem>
                        </SelectContent>
                      </Select>
                    </div>
                    <div className="space-y-2">
                      <Label>Hours / week *</Label>
                      <Input
                        type="number"
                        step="0.5"
                        value={form.data.hours_per_week}
                        onChange={(e) =>
                          form.setData("hours_per_week", e.target.value)
                        }
                        required
                      />
                    </div>
                  </div>
                ) : (
                  <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div className="space-y-2">
                      <Label>Minimum monthly salary (PHP) *</Label>
                      <Input
                        type="number"
                        step="0.01"
                        value={form.data.salary_min}
                        onChange={(e) =>
                          form.setData("salary_min", e.target.value)
                        }
                        required
                      />
                    </div>
                    <div className="space-y-2">
                      <Label>Maximum monthly salary (PHP) *</Label>
                      <Input
                        type="number"
                        step="0.01"
                        value={form.data.salary_max}
                        onChange={(e) =>
                          form.setData("salary_max", e.target.value)
                        }
                        required
                      />
                    </div>
                  </div>
                )}
              </div>

              <div className="space-y-2 lg:col-span-6">
                <Label>Application URL *</Label>
                <Input
                  type="url"
                  value={form.data.ApplicationURL}
                  onChange={(e) =>
                    form.setData("ApplicationURL", e.target.value)
                  }
                  placeholder="https://company.com/careers/123"
                  required
                />
              </div>

              <div className="space-y-2 lg:col-span-3">
                <div className="flex items-center justify-between">
                  <Label>Source *</Label>
                  <button
                    type="button"
                    onClick={() => setIsAddSourceOpen(true)}
                    className="inline-flex items-center gap-0.5 text-xs font-semibold text-primary hover:underline"
                  >
                    <PlusIcon className="size-3" /> Add
                  </button>
                </div>
                <Select
                  value={form.data.source}
                  onValueChange={(v) => form.setData("source", v)}
                >
                  <SelectTrigger>
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    {sourcesList.map((src) => (
                      <SelectItem key={src} value={src}>
                        {src}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>

              <div className="space-y-2 lg:col-span-3">
                <div className="flex items-center justify-between">
                  <Label>Status *</Label>
                  <button
                    type="button"
                    onClick={() => setIsAddStatusOpen(true)}
                    className="inline-flex items-center gap-0.5 text-xs font-semibold text-primary hover:underline"
                  >
                    <PlusIcon className="size-3" /> Add
                  </button>
                </div>
                <Select
                  value={form.data.status}
                  onValueChange={(v) => form.setData("status", v)}
                >
                  <SelectTrigger>
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    {statusesList.map((st) => (
                      <SelectItem key={st} value={st}>
                        {st}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>

              <div className="space-y-2 lg:col-span-12">
                <Label>Job description *</Label>
                <Textarea
                  rows={3}
                  value={form.data.description}
                  onChange={(e) => form.setData("description", e.target.value)}
                  placeholder="Paste job description or requirements..."
                  required
                />
              </div>

              <div className="space-y-2 lg:col-span-6">
                <Label>Pros (optional)</Label>
                <Input
                  value={form.data.pros}
                  onChange={(e) => form.setData("pros", e.target.value)}
                  placeholder="Good benefits, flexible schedule..."
                />
              </div>
              <div className="space-y-2 lg:col-span-6">
                <Label>Cons (optional)</Label>
                <Input
                  value={form.data.cons}
                  onChange={(e) => form.setData("cons", e.target.value)}
                  placeholder="Late hours, high workload..."
                />
              </div>

              <div className="flex items-center justify-end gap-3 border-t border-border/60 pt-4 lg:col-span-12">
                <Button asChild variant="ghost">
                  <Link href={route("job-applications.applications")}>Cancel</Link>
                </Button>
                <Button type="submit" disabled={form.processing}>
                  Save application
                </Button>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>

      <Dialog open={isAddSourceOpen} onOpenChange={setIsAddSourceOpen}>
        <DialogContent className="max-w-md">
          <DialogHeader>
            <DialogTitle>Add custom job source</DialogTitle>
            <DialogDescription>
              This source will be saved for future applications.
            </DialogDescription>
          </DialogHeader>
          <Input
            value={newSourceInput}
            onChange={(e) => setNewSourceInput(e.target.value)}
            onKeyDown={(e) =>
              e.key === "Enter" &&
              (e.preventDefault(),
              persistOption(route("application-sources.store"), newSourceInput, (name) => {
                if (!sourcesList.includes(name)) setSourcesList([...sourcesList, name])
                form.setData("source", name)
                setNewSourceInput("")
                setIsAddSourceOpen(false)
              }))
            }
            placeholder="e.g. Wellfound, Discord..."
          />
          <DialogFooter className="gap-2">
            <Button variant="outline" onClick={() => setIsAddSourceOpen(false)}>
              Cancel
            </Button>
            <Button
              disabled={isAddingOption}
              onClick={() =>
                persistOption(route("application-sources.store"), newSourceInput, (name) => {
                  if (!sourcesList.includes(name)) setSourcesList([...sourcesList, name])
                  form.setData("source", name)
                  setNewSourceInput("")
                  setIsAddSourceOpen(false)
                })
              }
            >
              Add source
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      <Dialog open={isAddStatusOpen} onOpenChange={setIsAddStatusOpen}>
        <DialogContent className="max-w-md">
          <DialogHeader>
            <DialogTitle>Add custom application status</DialogTitle>
            <DialogDescription>
              This status will be saved for future applications.
            </DialogDescription>
          </DialogHeader>
          <Input
            value={newStatusInput}
            onChange={(e) => setNewStatusInput(e.target.value)}
            onKeyDown={(e) =>
              e.key === "Enter" &&
              (e.preventDefault(),
              persistOption(route("application-statuses.store"), newStatusInput, (name) => {
                if (!statusesList.includes(name)) setStatusesList([...statusesList, name])
                form.setData("status", name)
                setNewStatusInput("")
                setIsAddStatusOpen(false)
              }))
            }
            placeholder="e.g. Assessment Sent, Negotiation..."
          />
          <DialogFooter className="gap-2">
            <Button variant="outline" onClick={() => setIsAddStatusOpen(false)}>
              Cancel
            </Button>
            <Button
              disabled={isAddingOption}
              onClick={() =>
                persistOption(route("application-statuses.store"), newStatusInput, (name) => {
                  if (!statusesList.includes(name)) setStatusesList([...statusesList, name])
                  form.setData("status", name)
                  setNewStatusInput("")
                  setIsAddStatusOpen(false)
                })
              }
            >
              Add status
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </AppLayout>
  )
}
