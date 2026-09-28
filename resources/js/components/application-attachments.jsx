import { useRef, useState } from "react"
import { router, useForm } from "@inertiajs/react"
import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select"
import {
  DownloadIcon,
  FileTextIcon,
  PaperclipIcon,
  Trash2Icon,
  UploadIcon,
} from "lucide-react"

const FILE_TYPES = [
  { value: "cover_letter", label: "Cover Letter" },
  { value: "portfolio", label: "Portfolio" },
  { value: "cert", label: "Certificate" },
  { value: "other", label: "Other" },
]

function formatBytes(bytes = 0) {
  if (bytes < 1024) return `${bytes} B`
  if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`
  return `${(bytes / (1024 * 1024)).toFixed(1)} MB`
}

export function ApplicationAttachments({ application }) {
  const fileInputRef = useRef(null)
  const [fileType, setFileType] = useState("cover_letter")
  const form = useForm({
    file: null,
    file_type: "cover_letter",
  })

  const attachments = application?.attachments || []

  const onFileChange = (event) => {
    const file = event.target.files?.[0] || null
    form.setData("file", file)
  }

  const onSubmit = (event) => {
    event.preventDefault()
    if (!form.data.file) return

    form.post(route("attachments.store", application.id), {
      forceFormData: true,
      preserveScroll: true,
      onSuccess: () => {
        form.reset("file")
        if (fileInputRef.current) fileInputRef.current.value = ""
      },
    })
  }

  const removeAttachment = (id) => {
    router.delete(route("attachments.destroy", id), {
      preserveScroll: true,
    })
  }

  return (
    <div className="space-y-6">
      <div className="space-y-3">
        <h4 className="text-[11px] font-bold tracking-widest text-muted-foreground uppercase">
          Documents
        </h4>

        {attachments.length === 0 ? (
          <div className="flex flex-col items-center justify-center gap-3 rounded-lg border border-dashed border-border/60 bg-muted/20 py-10 text-center">
            <PaperclipIcon className="size-8 text-muted-foreground" />
            <div>
              <p className="text-sm font-medium">No attachments yet</p>
              <p className="text-xs text-muted-foreground">
                Upload cover letters, portfolios, or certificates (PDF, DOCX, TXT
                · max 5 MB).
              </p>
            </div>
          </div>
        ) : (
          <div className="space-y-2">
            {attachments.map((attachment) => (
              <div
                key={attachment.id}
                className="flex flex-wrap items-center gap-2 rounded-lg border border-border/60 bg-muted/30 p-3"
              >
                <FileTextIcon className="size-4 shrink-0 text-primary" />
                <div className="min-w-0 flex-1">
                  <p
                    className="truncate text-sm font-medium"
                    title={attachment.file_name}
                  >
                    {attachment.file_name}
                  </p>
                  <div className="mt-0.5 flex flex-wrap items-center gap-2">
                    <Badge variant="outline" className="text-[10px] uppercase">
                      {attachment.file_type_label || attachment.file_type}
                    </Badge>
                    <span className="text-[11px] text-muted-foreground">
                      {attachment.formatted_size ||
                        formatBytes(attachment.file_size)}
                    </span>
                  </div>
                </div>
                <Button asChild variant="outline" size="sm">
                  <a href={route("attachments.download", attachment.id)}>
                    <DownloadIcon />
                    Download
                  </a>
                </Button>
                <Button
                  type="button"
                  variant="ghost"
                  size="icon"
                  className="text-destructive hover:bg-destructive/10"
                  onClick={() => removeAttachment(attachment.id)}
                >
                  <Trash2Icon />
                </Button>
              </div>
            ))}
          </div>
        )}
      </div>

      <form
        onSubmit={onSubmit}
        className="space-y-4 border-t border-border/60 pt-4"
      >
        <h4 className="text-[11px] font-bold tracking-widest text-muted-foreground uppercase">
          Upload document
        </h4>
        <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
          <div className="space-y-1.5">
            <Label>Document type</Label>
            <Select
              value={fileType}
              onValueChange={(value) => {
                setFileType(value)
                form.setData("file_type", value)
              }}
            >
              <SelectTrigger className="w-full">
                <SelectValue />
              </SelectTrigger>
              <SelectContent position="popper">
                {FILE_TYPES.map((type) => (
                  <SelectItem key={type.value} value={type.value}>
                    {type.label}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>
          <div className="space-y-1.5">
            <Label>File</Label>
            <Input
              ref={fileInputRef}
              type="file"
              accept=".pdf,.docx,.txt,application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document,text/plain"
              onChange={onFileChange}
              required
            />
          </div>
        </div>
        <Button type="submit" disabled={form.processing || !form.data.file}>
          <UploadIcon />
          {form.processing ? "Uploading…" : "Upload attachment"}
        </Button>
        {form.errors.file && (
          <p className="text-sm text-destructive">{form.errors.file}</p>
        )}
        {form.errors.file_type && (
          <p className="text-sm text-destructive">{form.errors.file_type}</p>
        )}
      </form>
    </div>
  )
}
