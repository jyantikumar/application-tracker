import { Head } from "@inertiajs/react"
import AppLayout from "@/layouts/AppLayout"

/** Legacy route — controller redirects to Applications. Kept for Inertia resolve safety. */
export default function Edit() {
  return (
    <AppLayout active="applications" title="Edit Application">
      <Head title="Edit Application" />
      <p className="text-sm text-muted-foreground">Opening form…</p>
    </AppLayout>
  )
}
