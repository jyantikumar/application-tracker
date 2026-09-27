import { cn } from "cn"

const STAGE_META = [
  { key: "Applied", label: "Applied", color: "bg-chart-1" },
  { key: "Under Review", label: "Under review", color: "bg-chart-3" },
  { key: "Waiting response", label: "Waiting", color: "bg-chart-2" },
  { key: "Currently Interviewing", label: "Interviewing", color: "bg-chart-4" },
  { key: "Hired", label: "Hired", color: "bg-primary" },
  { key: "Rejected", label: "Rejected", color: "bg-destructive" },
]

export function PipelineStages({
  AppliedCount = 0,
  UnderReviewCount = 0,
  WaitingResponse = 0,
  CurrentlyInterviewing = 0,
  HiredCount = 0,
  RejectedCount = 0,
  StatusCounts = {},
}) {
  const counts = {
    Applied: AppliedCount,
    "Under Review": UnderReviewCount,
    "Waiting response": WaitingResponse,
    "Currently Interviewing": CurrentlyInterviewing,
    Hired: HiredCount,
    Rejected: RejectedCount,
    ...StatusCounts,
  }

  const stages = STAGE_META.map((stage) => ({
    ...stage,
    value: Number(counts[stage.key] ?? 0),
  }))

  const total = stages.reduce((sum, stage) => sum + stage.value, 0) || 1

  return (
    <section className="px-4 lg:px-6">
      <div className="dashboard-surface p-5 sm:p-6">
        <div className="mb-5 flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
          <div>
            <h3 className="font-heading text-lg font-semibold tracking-tight">
              Pipeline stages
            </h3>
            <p className="text-sm text-muted-foreground">
              Distribution across your current statuses
            </p>
          </div>
          <p className="text-xs font-medium uppercase tracking-[0.14em] text-muted-foreground">
            {total === 1 && stages.every((s) => s.value === 0) ? "0" : stages.reduce((s, x) => s + x.value, 0)}{" "}
            applications
          </p>
        </div>

        <div className="mb-5 flex h-2.5 overflow-hidden rounded-full bg-muted">
          {stages.map((stage) => (
            <div
              key={stage.key}
              className={cn(stage.color, "transition-all duration-500")}
              style={{ width: `${(stage.value / total) * 100}%` }}
              title={`${stage.label}: ${stage.value}`}
            />
          ))}
        </div>

        <div className="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
          {stages.map((stage) => (
            <div
              key={stage.key}
              className="rounded-xl border border-border/60 bg-background/50 px-3 py-3"
            >
              <div className="mb-2 flex items-center gap-2">
                <span className={cn("size-2 rounded-full", stage.color)} />
                <span className="truncate text-[11px] font-semibold uppercase tracking-[0.1em] text-muted-foreground">
                  {stage.label}
                </span>
              </div>
              <p className="font-heading text-xl font-semibold tabular-nums">
                {stage.value}
              </p>
              <p className="text-[11px] text-muted-foreground tabular-nums">
                {Math.round((stage.value / total) * 100)}%
              </p>
            </div>
          ))}
        </div>
      </div>
    </section>
  )
}
