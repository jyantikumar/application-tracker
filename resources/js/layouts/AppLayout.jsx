import React, { useState } from "react"
import { AppSidebar } from "@/components/app-sidebar"
import { ApplicationModalProvider } from "@/components/application-modal-provider"
import { SiteHeader } from "@/components/site-header"
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
import { SidebarInset, SidebarProvider } from "@/components/ui/sidebar"
import { cn } from "cn"

export default function AppLayout({
  active = "dashboard",
  title = "Dashboard",
  breadcrumbs,
  flush = false,
  children,
}) {
  const [isImportModalOpen, setIsImportModalOpen] = useState(false)

  return (
    <ApplicationModalProvider>
      <SidebarProvider
        defaultOpen
        style={{
          "--sidebar-width": "16rem",
          "--sidebar-width-icon": "3.5rem",
          "--header-height": "3.5rem",
        }}
      >
        <AppSidebar
          variant="sidebar"
          collapsible="icon"
          active={active}
          onImport={() => setIsImportModalOpen(true)}
        />
        <SidebarInset className="min-h-svh min-w-0 overflow-x-hidden outline-none focus:outline-none focus-visible:ring-0 focus-visible:outline-none">
          <SiteHeader title={title} breadcrumbs={breadcrumbs} />
          <div className="flex flex-1 flex-col">
            <div className="@container/main flex flex-1 flex-col gap-2">
              <div
                className={cn(
                  "flex w-full min-w-0 flex-col gap-5 py-5 md:gap-6 md:py-7",
                  !flush && "px-4 lg:px-6"
                )}
              >
                {children}
              </div>
            </div>
          </div>
        </SidebarInset>

        <Dialog open={isImportModalOpen} onOpenChange={setIsImportModalOpen}>
          <DialogContent className="max-w-md">
            <DialogHeader>
              <DialogTitle>Import CSV Applications</DialogTitle>
              <DialogDescription>
                Select a CSV file containing your past job application logs.
              </DialogDescription>
            </DialogHeader>

            <form
              action={route("job-applications.import")}
              method="POST"
              encType="multipart/form-data"
              className="space-y-4 py-2"
            >
              <input
                type="hidden"
                name="_token"
                value={
                  document
                    .querySelector('meta[name="csrf-token"]')
                    ?.getAttribute("content") ?? ""
                }
              />
              <Input type="file" name="file" accept=".csv" required />
              <DialogFooter className="gap-2">
                <Button
                  type="button"
                  variant="outline"
                  onClick={() => setIsImportModalOpen(false)}
                >
                  Cancel
                </Button>
                <Button type="submit">Upload & Import</Button>
              </DialogFooter>
            </form>
          </DialogContent>
        </Dialog>
      </SidebarProvider>
    </ApplicationModalProvider>
  )
}