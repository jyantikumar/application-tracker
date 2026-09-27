import { Head } from "@inertiajs/react"
import { router } from "@inertiajs/react"
import { useEffect } from "react"

/** Legacy page name — dashboard lives at JobApplications/Index via /dashboard redirect. */
export default function Dashboard() {
  useEffect(() => {
    router.visit(route("dashboard"), { replace: true })
  }, [])

  return <Head title="Dashboard" />
}
