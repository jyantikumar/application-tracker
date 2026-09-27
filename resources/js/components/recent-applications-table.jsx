import { Link } from "@inertiajs/react"
import { useApplicationModal } from "@/components/application-modal-provider"
import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"
import {
  Card,
  CardAction,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "@/components/ui/card"
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
  const { openCreate, openEdit } = useApplicationModal()

  return (
    <section className="px-4 lg:px-6">
      <Card className="gap-0 py-0">
        <CardHeader className="border-b py-5 [.border-b]:pb-5">
          <CardTitle>Recent applications</CardTitle>
          <CardDescription>
            Latest entries from your application pipeline
          </CardDescription>
          <CardAction>
            <Button asChild variant="outline" size="sm">
              <Link href={route("job-applications.applications")}>
                Open applications
                <ArrowRightIcon />
              </Link>
            </Button>
          </CardAction>
        </CardHeader>

        <CardContent className="px-0">
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
              <Button size="sm" className="mt-1" onClick={openCreate}>
                Log application
              </Button>
            </div>
          ) : (
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead className="pl-6">Company</TableHead>
                  <TableHead className="hidden sm:table-cell">Position</TableHead>
                  <TableHead>Status</TableHead>
                  <TableHead className="hidden md:table-cell">Applied</TableHead>
                  <TableHead className="pr-6 text-right">Action</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {RecentApplications.map((app) => (
                  <TableRow key={app.id}>
                    <TableCell className="pl-6">
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
                    <TableCell className="hidden text-muted-foreground tabular-nums md:table-cell">
                      {formatDate(app.created_at)}
                    </TableCell>
                    <TableCell className="pr-6 text-right">
                      <Button
                        variant="ghost"
                        size="sm"
                        onClick={() => openEdit(app)}
                      >
                        Edit
                      </Button>
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          )}
        </CardContent>
      </Card>
    </section>
  )
}
