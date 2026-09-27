import { Link } from "@inertiajs/react"
import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table"
import { ArrowRightIcon, BriefcaseIcon } from "lucide-react"

function formatDate(value) {
  if (!value) return "—"
  return new Date(value).toLocaleDateString("en-US", {
    month: "short",
    day: "numeric",
    year: "numeric",
  })
}

export function RecentApplicationsTable({ RecentApplications = [] }) {
  return (
    <section className="px-4 lg:px-6">
      <div className="dashboard-surface overflow-hidden">
        <div className="flex flex-col gap-3 border-b border-border/60 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
          <div>
            <h3 className="font-heading text-lg font-semibold tracking-tight">
              Recent applications
            </h3>
            <p className="text-sm text-muted-foreground">
              Latest entries from your Laravel API payload
            </p>
          </div>
          <Button asChild variant="outline" size="sm" className="rounded-lg">
            <Link href={route("job-applications.applications")}>
              Open applications
              <ArrowRightIcon />
            </Link>
          </Button>
        </div>

        {RecentApplications.length === 0 ? (
          <div className="flex flex-col items-center justify-center gap-3 px-6 py-16 text-center">
            <div className="flex size-12 items-center justify-center rounded-2xl bg-primary/10 text-primary">
              <BriefcaseIcon className="size-5" />
            </div>
            <div>
              <p className="font-medium">No applications yet</p>
              <p className="mt-1 text-sm text-muted-foreground">
                Log your first role to populate this table.
              </p>
            </div>
            <Button asChild size="sm" className="mt-1 rounded-lg">
              <Link href={route("job-applications.create")}>Log application</Link>
            </Button>
          </div>
        ) : (
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Company</TableHead>
                <TableHead className="hidden sm:table-cell">Position</TableHead>
                <TableHead>Status</TableHead>
                <TableHead className="hidden md:table-cell">Applied</TableHead>
                <TableHead className="text-right">Action</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {RecentApplications.map((app) => (
                <TableRow key={app.id}>
                  <TableCell>
                    <div className="font-medium">{app.company_name}</div>
                    <div className="text-xs text-muted-foreground sm:hidden">
                      {app.position}
                    </div>
                  </TableCell>
                  <TableCell className="hidden text-muted-foreground sm:table-cell">
                    {app.position}
                  </TableCell>
                  <TableCell>
                    <Badge variant="secondary" className="rounded-md font-medium">
                      {app.status || "—"}
                    </Badge>
                  </TableCell>
                  <TableCell className="hidden tabular-nums text-muted-foreground md:table-cell">
                    {formatDate(app.created_at)}
                  </TableCell>
                  <TableCell className="text-right">
                    <Button asChild variant="ghost" size="sm">
                      <Link href={route("job-applications.edit", app.id)}>
                        Edit
                      </Link>
                    </Button>
                  </TableCell>
                </TableRow>
              ))}
            </TableBody>
          </Table>
        )}
      </div>
    </section>
  )
}
