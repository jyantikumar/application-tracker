import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card"
import { cn } from "cn"

function BreakdownList({ title, data = {}, emptyLabel = "No data yet" }) {
  const rows = Object.entries(data)
    .map(([label, count]) => ({ label, count: Number(count) || 0 }))
    .filter((row) => row.label)
    .sort((a, b) => b.count - a.count)
    .slice(0, 6)

  const max = Math.max(...rows.map((row) => row.count), 1)

  return (
    <Card className="border-border/60 bg-card/80 shadow-none">
      <CardHeader className="pb-3">
        <CardTitle className="font-heading text-base font-semibold tracking-tight">
          {title}
        </CardTitle>
      </CardHeader>
      <CardContent className="space-y-3">
        {rows.length === 0 ? (
          <p className="py-6 text-center text-sm text-muted-foreground">
            {emptyLabel}
          </p>
        ) : (
          rows.map((row) => (
            <div key={row.label} className="space-y-1.5">
              <div className="flex items-center justify-between gap-3 text-sm">
                <span className="truncate font-medium">{row.label}</span>
                <span className="shrink-0 tabular-nums text-muted-foreground">
                  {row.count}
                </span>
              </div>
              <div className="h-1.5 overflow-hidden rounded-full bg-muted">
                <div
                  className={cn("h-full rounded-full bg-primary transition-all")}
                  style={{ width: `${(row.count / max) * 100}%` }}
                />
              </div>
            </div>
          ))
        )}
      </CardContent>
    </Card>
  )
}

export function DashboardBreakdownCharts({
  ApplicationsBySource = {},
  ApplicationsByJobType = {},
  ApplicationsByLocation = {},
  InterviewsDone = 0,
  TotalApplications = 0,
}) {
  const interviewRate =
    TotalApplications > 0
      ? Math.round((InterviewsDone / TotalApplications) * 100)
      : 0

  return (
    <div className="flex h-full flex-col gap-4">
      <div className="dashboard-surface flex items-center justify-between gap-4 p-5">
        <div>
          <p className="text-[11px] font-semibold uppercase tracking-[0.14em] text-muted-foreground">
            Interview conversion
          </p>
          <p className="mt-1 font-heading text-3xl font-semibold tabular-nums tracking-tight">
            {interviewRate}%
          </p>
          <p className="mt-1 text-sm text-muted-foreground">
            {InterviewsDone} of {TotalApplications} reached interview
          </p>
        </div>
        <div
          className="relative size-16 shrink-0 rounded-full"
          style={{
            background: `conic-gradient(var(--primary) ${interviewRate}%, color-mix(in oklab, var(--muted) 80%, transparent) 0)`,
          }}
          aria-hidden
        >
          <div className="absolute inset-2 rounded-full bg-card" />
        </div>
      </div>

      <BreakdownList title="By source" data={ApplicationsBySource} />
      <BreakdownList title="By work setup" data={ApplicationsByJobType} />
      <BreakdownList title="By location" data={ApplicationsByLocation} />
    </div>
  )
}
