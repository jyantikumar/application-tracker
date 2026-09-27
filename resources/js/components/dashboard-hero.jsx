import { Link } from "@inertiajs/react"
import { useApplicationModal } from "@/components/application-modal-provider"
import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "@/components/ui/card"
import {
  BriefcaseIcon,
  CircleCheckIcon,
  MessageSquareIcon,
  PlusIcon,
  SparklesIcon,
} from "lucide-react"

export function DashboardHero({
  TotalApplications = 0,
  CurrentlyInterviewing = 0,
  HiredCount = 0,
}) {
  const { openCreate } = useApplicationModal()
  const metrics = [
    {
      label: "Total logged",
      value: TotalApplications,
      icon: BriefcaseIcon,
    },
    {
      label: "In interviews",
      value: CurrentlyInterviewing,
      icon: MessageSquareIcon,
    },
    {
      label: "Hired",
      value: HiredCount,
      icon: CircleCheckIcon,
    },
  ]

  return (
    <section className="px-4 lg:px-6">
      <Card className="relative overflow-hidden">
        <div
          aria-hidden
          className="pointer-events-none absolute -top-20 -right-16 size-64 rounded-full bg-primary/10 blur-3xl"
        />
        <CardHeader className="relative gap-3">
          <Badge variant="secondary" className="w-fit gap-1.5 rounded-lg px-2.5 py-1">
            <SparklesIcon className="size-3.5" />
            Pipeline overview
          </Badge>
          <div className="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div className="max-w-xl space-y-2">
              <CardTitle className="text-3xl sm:text-4xl">
                Your job search at a glance
              </CardTitle>
              <CardDescription className="text-sm leading-relaxed sm:text-base">
                Track applications, interview momentum, and outcomes in one place.
              </CardDescription>
              <div className="flex flex-wrap gap-2 pt-2">
                <Button asChild variant="outline">
                  <Link href={route("job-applications.applications")}>
                    View all applications
                  </Link>
                </Button>
              </div>
            </div>

            <div className="grid grid-cols-3 gap-3">
              {metrics.map((metric) => (
                <Card key={metric.label} size="sm" className="min-w-[5.5rem] shadow-none sm:min-w-[7rem]">
                  <CardContent className="pt-0">
                    <metric.icon className="mb-2 size-4 text-primary" />
                    <p className="font-heading text-2xl font-semibold tabular-nums tracking-tight">
                      {metric.value}
                    </p>
                    <p className="mt-0.5 text-[11px] font-medium tracking-[0.12em] text-muted-foreground uppercase">
                      {metric.label}
                    </p>
                  </CardContent>
                </Card>
              ))}
            </div>
          </div>
        </CardHeader>
      </Card>
    </section>
  )
}
