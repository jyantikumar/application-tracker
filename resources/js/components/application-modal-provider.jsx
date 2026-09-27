import React, { createContext, useCallback, useContext, useMemo, useState } from "react"
import { usePage } from "@inertiajs/react"
import { ApplicationFormDialog } from "@/components/application-form-dialog"
import { toOptionNames } from "@/lib/options"

const ApplicationModalContext = createContext(null)

export function useApplicationModal() {
  const ctx = useContext(ApplicationModalContext)
  if (!ctx) {
    throw new Error("useApplicationModal must be used within ApplicationModalProvider")
  }
  return ctx
}

export function ApplicationModalProvider({ children }) {
  const { applicationSources = [], applicationStatuses = [] } = usePage().props
  const [open, setOpen] = useState(false)
  const [mode, setMode] = useState("create")
  const [application, setApplication] = useState(null)

  const sources = useMemo(
    () => toOptionNames(applicationSources, ["LinkedIn", "Indeed", "Referral"]),
    [applicationSources]
  )
  const statuses = useMemo(
    () =>
      toOptionNames(applicationStatuses, [
        "Applied",
        "Under Review",
        "Interviewing",
        "Currently Interviewing",
        "Waiting response",
        "Hired",
        "Rejected",
      ]),
    [applicationStatuses]
  )

  const openCreate = useCallback(() => {
    setMode("create")
    setApplication(null)
    setOpen(true)
  }, [])

  const openEdit = useCallback((app) => {
    setMode("edit")
    setApplication(app)
    setOpen(true)
  }, [])

  const close = useCallback(() => {
    setOpen(false)
    setApplication(null)
  }, [])

  const value = useMemo(
    () => ({ openCreate, openEdit, close }),
    [openCreate, openEdit, close]
  )

  return (
    <ApplicationModalContext.Provider value={value}>
      {children}
      <ApplicationFormDialog
        open={open}
        onOpenChange={(next) => {
          if (!next) close()
          else setOpen(true)
        }}
        mode={mode}
        application={application}
        sources={sources}
        statuses={statuses}
      />
    </ApplicationModalContext.Provider>
  )
}
