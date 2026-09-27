import { Link } from "@inertiajs/react"
import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"
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
    <section className="relative overflow-hidden px-4 lg:px-6">
      <div className="dashboard-surface relative overflow-hidden p-6 sm:p-8">
        <div
          aria-hidden
          className="pointer-events-none absolute -right-16 -top-20 size-64 rounded-full bg-primary/15 blur-3xl"
        />
        <div
          aria-hidden
          className="pointer-events-none absolute -bottom-24 left-1/3 size-56 rounded-full bg-chart-2/10 blur-3xl"
        />

        <div className="relative flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
          <div className="max-w-xl space-y-3">
            <Badge variant="secondary" className="gap-1.5 rounded-lg px-2.5 py-1">
              <SparklesIcon className="size-3.5" />
              Pipeline overview
            </Badge>
            <h2 className="font-heading text-3xl font-semibold tracking-tight sm:text-4xl">
              Your job search at a glance
            </h2>
            <p className="text-sm leading-relaxed text-muted-foreground sm:text-base">
              Track applications, interview momentum, and outcomes in one place —
              ready for your Laravel API props.
            </p>
            <div className="flex flex-wrap gap-2 pt-1">
              <Button asChild className="rounded-lg shadow-sm shadow-primary/20">
                <Link href={route("job-applications.create")}>
                  <PlusIcon />
                  Log application
                </Link>
              </Button>
              <Button asChild variant="outline" className="rounded-lg">
                <Link href={route("job-applications.applications")}>
                  View all applications
                </Link>
              </Button>
            </div>
          </div>

          <div className="grid grid-cols-3 gap-3 sm:gap-4">
            {metrics.map((metric) => (
              <div
                key={metric.label}
                className="min-w-[5.5rem] rounded-xl border border-border/70 bg-background/60 px-3 py-3 backdrop-blur-sm sm:min-w-[7rem] sm:px-4"
              >
                <metric.icon className="mb-2 size-4 text-primary" />
                <p className="font-heading text-2xl font-semibold tabular-nums tracking-tight">
                  {metric.value}
                </p>
                <p className="mt-0.5 text-[11px] font-medium uppercase tracking-[0.12em] text-muted-foreground">
                  {metric.label}
                </p>
              </div>
            ))}
          </div>
        </div>
      </div>
    </section>
  )
}
