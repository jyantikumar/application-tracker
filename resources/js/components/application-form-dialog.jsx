import React, { useEffect, useState } from "react"
import { router, useForm } from "@inertiajs/react"
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
import { Alert, AlertDescription, AlertTitle } from "@/components/ui/alert"
import { Button } from "@/components/ui/button"
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
import { CurrencyCombobox } from "@/components/currency-combobox"
import { ConvertToPhp, FormatPhpAmount } from "@/lib/currencies"
import {
  AlertCircleIcon,
  FileTextIcon,
  PlusIcon,
  XIcon,
} from "lucide-react"

function emptyForm(sources = [], statuses = []) {
  return {
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
    source: sources[0] || "LinkedIn",
    status: statuses[0] || "Applied",
    description: "",
    pros: "",
    cons: "",
    resume: null,
    remove_resume: false,
  }
}

function formFromApplication(app, sources = [], statuses = []) {
  return {
    company_name: app?.company_name || "",
    position: app?.position || "",
    job_type: app?.job_type || "Remote",
    city: app?.city || "",
    currency: app?.currency || "USD",
    salary: app?.salary ?? "",
    salary_min: app?.salary_min ?? "",
    salary_max: app?.salary_max ?? "",
    pay_rate_type: app?.pay_rate_type || "Hourly",
    hours_per_week: app?.hours_per_week ?? "40",
    ApplicationURL: app?.ApplicationURL || "",
    source: app?.source || sources[0] || "LinkedIn",
    status: app?.status || statuses[0] || "Applied",
    description: app?.description || "",
    pros: app?.pros || "",
    cons: app?.cons || "",
    resume: null,
    remove_resume: false,
  }
}

export function ApplicationFormDialog({
  open,
  onOpenChange,
  mode = "create",
  application = null,
  sources = [],
  statuses = [],
}) {
  const isEdit = mode === "edit" && application?.id
  const [sourcesList, setSourcesList] = useState(sources)
  const [statusesList, setStatusesList] = useState(statuses)
  const [isAddSourceOpen, setIsAddSourceOpen] = useState(false)
  const [isAddStatusOpen, setIsAddStatusOpen] = useState(false)
  const [newSourceInput, setNewSourceInput] = useState("")
  const [newStatusInput, setNewStatusInput] = useState("")
  const [isAddingOption, setIsAddingOption] = useState(false)
  const [confirmSaveOpen, setConfirmSaveOpen] = useState(false)
  const [PhpPreview, setPhpPreview] = useState(null)
  const [PhpPreviewLoading, setPhpPreviewLoading] = useState(false)

  const form = useForm(emptyForm(sources, statuses))

  useEffect(() => {
    setSourcesList(sources)
  }, [sources])

  useEffect(() => {
    setStatusesList(statuses)
  }, [statuses])

  useEffect(() => {
    if (!open) return
    const nextSources = [...sources]
    const nextStatuses = [...statuses]
    if (application?.source && !nextSources.includes(application.source)) {
      nextSources.push(application.source)
    }
    if (application?.status && !nextStatuses.includes(application.status)) {
      nextStatuses.push(application.status)
    }
    setSourcesList(nextSources)
    setStatusesList(nextStatuses)
    form.setData(
      isEdit
        ? formFromApplication(application, nextSources, nextStatuses)
        : emptyForm(nextSources, nextStatuses)
    )
    form.clearErrors()
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [open, isEdit, application?.id])

  // Live PHP conversion preview for remote compensation
  useEffect(() => {
    if (!open || form.data.job_type !== "Remote") {
      setPhpPreview(null)
      return
    }

    const Currency = String(form.data.currency || "").toUpperCase()
    const Salary = Number(form.data.salary)
    if (!Currency || Currency === "PHP" || !Number.isFinite(Salary) || Salary <= 0) {
      setPhpPreview(null)
      return
    }

    let Hours = Number(form.data.hours_per_week) || 40
    let Monthly = Salary
    if (form.data.pay_rate_type === "Hourly") {
      Monthly = (Salary * Hours * 52) / 12
    }

    let Cancelled = false
    setPhpPreviewLoading(true)
    const Timer = window.setTimeout(() => {
      ConvertToPhp(Currency, Monthly)
        .then((Result) => {
          if (!Cancelled) setPhpPreview(Result)
        })
        .catch(() => {
          if (!Cancelled) setPhpPreview(null)
        })
        .finally(() => {
          if (!Cancelled) setPhpPreviewLoading(false)
        })
    }, 350)

    return () => {
      Cancelled = true
      window.clearTimeout(Timer)
    }
  }, [
    open,
    form.data.job_type,
    form.data.currency,
    form.data.salary,
    form.data.pay_rate_type,
    form.data.hours_per_week,
  ])

  const persistOption = async (endpoint, name, onSuccess) => {
    const trimmed = name.trim()
    if (!trimmed) return
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
        body: JSON.stringify({ name: trimmed }),
      })
      if (!response.ok) throw new Error("Failed")
      const data = await response.json().catch(() => null)
      const savedName = data?.name ?? trimmed

      onSuccess(savedName)

      router.reload({
        only: ["applicationSources", "applicationStatuses"],
        preserveScroll: true,
        preserveState: true,
      })
    } catch {
      alert("Could not save option. Please try again.")
    } finally {
      setIsAddingOption(false)
    }
  }

  const submitForm = () => {
    const options = {
      preserveScroll: true,
      forceFormData: true, // 👈 required so multipart/form-data is used for the file
      onSuccess: () => onOpenChange(false),
    }
    if (isEdit) {
      // Laravel doesn't support multipart + PUT natively; use POST with _method spoof
      form.transform((data) => ({ ...data, _method: "put" }))
      form.post(route("job-applications.update", application.id), options)
    } else {
      form.post(route("job-applications.store"), options)
    }
  }

  const handleSubmit = (e) => {
    e.preventDefault()
    if (isEdit) {
      setConfirmSaveOpen(true)
      return
    }
    submitForm()
  }

  const handleResumeChange = (e) => {
    const file = e.target.files?.[0] ?? null
    form.setData("resume", file)
    if (file) {
      // Clear "remove" intent if a new file is selected
      form.setData("remove_resume", false)
    }
  }

  const clearResumeSelection = () => {
    form.setData("resume", null)
  }

  const toggleRemoveExistingResume = () => {
    form.setData("remove_resume", !form.data.remove_resume)
  }

  const existingResumeName = application?.resume_original_name
  const existingResumeUrl = application?.resume_url
  const hasExistingResume = isEdit && existingResumeName && !form.data.remove_resume
  const newResumeSelected = form.data.resume instanceof File

  return (
    <>
      <Dialog open={open} onOpenChange={onOpenChange}>
        <DialogContent className="flex max-h-[90vh] w-full max-w-3xl flex-col gap-0 overflow-hidden p-0 sm:max-w-3xl">
          <DialogHeader className="shrink-0 border-b border-border px-6 py-5 pr-12">
            <DialogTitle>
              {isEdit ? "Edit application" : "Log new application"}
            </DialogTitle>
            <DialogDescription>
              {isEdit
                ? `Update details for ${application?.company_name || "this role"}.`
                : "Record a submitted role so you can track pipeline progress."}
            </DialogDescription>
          </DialogHeader>

          <form
            id="application-form"
            onSubmit={handleSubmit}
            className="flex min-h-0 flex-1 flex-col"
          >
            <div className="flex-1 space-y-5 overflow-y-auto px-6 py-5">
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

              <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div className="space-y-2">
                  <Label>Company name *</Label>
                  <Input
                    value={form.data.company_name}
                    onChange={(e) => form.setData("company_name", e.target.value)}
                    required
                  />
                </div>
                <div className="space-y-2">
                  <Label>Position *</Label>
                  <Input
                    value={form.data.position}
                    onChange={(e) => form.setData("position", e.target.value)}
                    required
                  />
                </div>
                <div className="space-y-2">
                  <Label>Work setup *</Label>
                  <Select
                    value={form.data.job_type}
                    onValueChange={(v) => form.setData("job_type", v)}
                  >
                    <SelectTrigger className="w-full">
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent position="popper">
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
                <div className="space-y-2">
                  <Label>City *</Label>
                  <Input
                    value={form.data.city}
                    onChange={(e) => form.setData("city", e.target.value)}
                    required
                  />
                </div>
              </div>

              <div className="space-y-4 rounded-xl border border-border bg-muted/30 p-4">
                <h3 className="text-xs font-bold tracking-wider text-primary uppercase">
                  Compensation
                </h3>
                {form.data.job_type === "Remote" ? (
                  <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div className="space-y-2 sm:col-span-2">
                      <Label>Currency *</Label>
                      <CurrencyCombobox
                        value={form.data.currency}
                        onValueChange={(Code) => form.setData("currency", Code)}
                      />
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
                        <SelectTrigger className="w-full">
                          <SelectValue />
                        </SelectTrigger>
                        <SelectContent position="popper">
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
                    {String(form.data.currency || "").toUpperCase() !== "PHP" &&
                      Number(form.data.salary) > 0 && (
                        <div className="pointer-events-none sm:col-span-2 rounded-lg border border-border/60 bg-background/80 px-3 py-2.5 select-none">
                          <p className="text-[10px] font-bold tracking-wider text-muted-foreground uppercase">
                            Est. monthly in PHP
                          </p>
                          <p className="mt-0.5 text-sm font-semibold tabular-nums text-foreground">
                            {PhpPreviewLoading
                              ? "Converting…"
                              : PhpPreview
                                ? FormatPhpAmount(PhpPreview.amount)
                                : "—"}
                          </p>
                          {PhpPreview?.rate ? (
                            <p className="mt-0.5 text-[11px] text-muted-foreground">
                              Rate ≈ {Number(PhpPreview.rate).toFixed(4)} PHP per{" "}
                              {form.data.currency}
                            </p>
                          ) : null}
                        </div>
                      )}
                  </div>
                ) : (
                  <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div className="space-y-2">
                      <Label>Min monthly salary (PHP) *</Label>
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
                      <Label>Max monthly salary (PHP) *</Label>
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

              <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div className="space-y-2 sm:col-span-2">
                  <Label>Application URL *</Label>
                  <Input
                    type="url"
                    value={form.data.ApplicationURL}
                    onChange={(e) =>
                      form.setData("ApplicationURL", e.target.value)
                    }
                    required
                  />
                </div>
                <div className="space-y-2">
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
                    <SelectTrigger className="w-full">
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent position="popper">
                      {sourcesList.map((src) => (
                        <SelectItem key={src} value={src}>
                          {src}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>
                <div className="space-y-2">
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
                    <SelectTrigger className="w-full">
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent position="popper">
                      {statusesList.map((st) => (
                        <SelectItem key={st} value={st}>
                          {st}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>

                {/* 👇 Resume upload */}
                <div className="space-y-2 sm:col-span-2">
                  <Label>Resume (optional)</Label>

                  {/* Existing resume preview (edit mode) */}
                  {hasExistingResume && !newResumeSelected && (
                    <div className="flex items-center gap-2 rounded-md border border-border bg-muted/40 px-3 py-2 text-sm">
                      <FileTextIcon className="size-4 shrink-0 text-primary" />
                      <a
                        href={existingResumeUrl}
                        target="_blank"
                        rel="noopener noreferrer"
                        className="min-w-0 flex-1 truncate font-medium hover:underline"
                        title={existingResumeName}
                      >
                        {existingResumeName}
                      </a>
                      <button
                        type="button"
                        onClick={toggleRemoveExistingResume}
                        className="rounded p-1 text-muted-foreground hover:bg-destructive/10 hover:text-destructive"
                        title="Remove this resume"
                      >
                        <XIcon className="size-4" />
                      </button>
                    </div>
                  )}

                  {/* Warning if marked for removal */}
                  {isEdit && form.data.remove_resume && !newResumeSelected && (
                    <div className="flex items-center gap-2 rounded-md border border-destructive/30 bg-destructive/10 px-3 py-2 text-xs text-destructive">
                      <XIcon className="size-3.5" />
                      <span className="flex-1">
                        Current resume will be removed on save.
                      </span>
                      <button
                        type="button"
                        className="font-semibold hover:underline"
                        onClick={() => form.setData("remove_resume", false)}
                      >
                        Undo
                      </button>
                    </div>
                  )}

                  {/* New file selected preview */}
                  {newResumeSelected && (
                    <div className="flex items-center gap-2 rounded-md border border-primary/30 bg-primary/5 px-3 py-2 text-sm">
                      <FileTextIcon className="size-4 shrink-0 text-primary" />
                      <span className="min-w-0 flex-1 truncate font-medium">
                        {form.data.resume.name}
                      </span>
                      <span className="shrink-0 text-xs text-muted-foreground">
                        {(form.data.resume.size / 1024).toFixed(0)} KB
                      </span>
                      <button
                        type="button"
                        onClick={clearResumeSelection}
                        className="rounded p-1 text-muted-foreground hover:bg-destructive/10 hover:text-destructive"
                      >
                        <XIcon className="size-4" />
                      </button>
                    </div>
                  )}

                  <Input
                    type="file"
                    accept=".pdf,.doc,.docx,.png,.jpg,.jpeg"
                    onChange={handleResumeChange}
                    className="cursor-pointer file:mr-3 file:cursor-pointer file:rounded file:border-0 file:bg-muted file:px-3 file:py-1 file:text-xs file:font-medium"
                  />
                  <p className="text-xs text-muted-foreground">
                    PDF, DOC, DOCX, PNG, JPG — max 10 MB.
                  </p>
                  {form.errors.resume && (
                    <p className="text-xs text-destructive">
                      {form.errors.resume}
                    </p>
                  )}
                </div>

                <div className="space-y-2 sm:col-span-2">
                  <Label>Job description *</Label>
                  <Textarea
                    rows={3}
                    value={form.data.description}
                    onChange={(e) => form.setData("description", e.target.value)}
                    required
                  />
                </div>
                <div className="space-y-2">
                  <Label>Pros (optional)</Label>
                  <Input
                    value={form.data.pros}
                    onChange={(e) => form.setData("pros", e.target.value)}
                  />
                </div>
                <div className="space-y-2">
                  <Label>Cons (optional)</Label>
                  <Input
                    value={form.data.cons}
                    onChange={(e) => form.setData("cons", e.target.value)}
                  />
                </div>
              </div>
            </div>

            <DialogFooter className="shrink-0 border-t border-border px-6 py-4">
              <Button
                type="button"
                variant="outline"
                onClick={() => onOpenChange(false)}
              >
                Cancel
              </Button>
              <Button type="submit" disabled={form.processing}>
                {isEdit ? "Save changes" : "Save application"}
              </Button>
            </DialogFooter>
          </form>
        </DialogContent>
      </Dialog>

      <AlertDialog open={confirmSaveOpen} onOpenChange={setConfirmSaveOpen}>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>Confirm changes?</AlertDialogTitle>
            <AlertDialogDescription>
              This will update the application record for{" "}
              <span className="font-medium text-foreground">
                {application?.company_name}
              </span>
              . Continue?
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel>Cancel</AlertDialogCancel>
            <AlertDialogAction
              onClick={() => {
                setConfirmSaveOpen(false)
                submitForm()
              }}
            >
              Confirm update
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>

      {/* Add source modal (unchanged) */}
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
            placeholder="e.g. Wellfound..."
          />
          <DialogFooter>
            <Button variant="outline" onClick={() => setIsAddSourceOpen(false)}>
              Cancel
            </Button>
            <Button
              disabled={isAddingOption}
              onClick={() =>
                persistOption(
                  route("application-sources.store"),
                  newSourceInput,
                  (name) => {
                    if (!sourcesList.includes(name)) {
                      setSourcesList([...sourcesList, name])
                    }
                    form.setData("source", name)
                    setNewSourceInput("")
                    setIsAddSourceOpen(false)
                  }
                )
              }
            >
              Add source
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      {/* Add status modal (unchanged) */}
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
            placeholder="e.g. Negotiation..."
          />
          <DialogFooter>
            <Button variant="outline" onClick={() => setIsAddStatusOpen(false)}>
              Cancel
            </Button>
            <Button
              disabled={isAddingOption}
              onClick={() =>
                persistOption(
                  route("application-statuses.store"),
                  newStatusInput,
                  (name) => {
                    if (!statusesList.includes(name)) {
                      setStatusesList([...statusesList, name])
                    }
                    form.setData("status", name)
                    setNewStatusInput("")
                    setIsAddStatusOpen(false)
                  }
                )
              }
            >
              Add status
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </>
  )
}