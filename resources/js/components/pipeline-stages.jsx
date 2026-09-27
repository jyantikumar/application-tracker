import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "@/components/ui/card"
import { cn } from "cn"
import { useMemo } from "react"

const STAGE_META = [
  { key: "Applied", label: "Applied", color: "bg-chart-1" },
  { key: "Under Review", label: "Under review", color: "bg-chart-3" },
  { key: "Waiting response", label: "Waiting", color: "bg-chart-2" },
  { key: "Currently Interviewing", label: "Interviewing", color: "bg-chart-4" },
  { key: "Interviewing", label: "Interviewing", color: "bg-chart-4" },
  { key: "Hired", label: "Hired", color: "bg-primary" },
  { key: "Rejected", label: "Rejected", color: "bg-destructive" },
]

const CUSTOM_COLORS = [
  "bg-chart-1",
  "bg-chart-2",
  "bg-chart-3",
  "bg-chart-4",
  "bg-chart-5",
  "bg-primary",
]

export function PipelineStages({
  AppliedCount = 0,
  UnderReviewCount = 0,
  WaitingResponse = 0,
  CurrentlyInterviewing = 0,
  HiredCount = 0,
  RejectedCount = 0,
  StatusCounts = {},
  CustomStatuses = [],
}) {
  const stages = useMemo(() => {
    const counts = {
      Applied: AppliedCount,
      "Under Review": UnderReviewCount,
      "Waiting response": WaitingResponse,
      "Currently Interviewing": CurrentlyInterviewing,
      Hired: HiredCount,
      Rejected: RejectedCount,
      ...StatusCounts,
    }

    const knownKeys = new Set(STAGE_META.map((s) => s.key))
    const base = STAGE_META.filter((stage, index, arr) => {
      // Prefer "Currently Interviewing" over plain "Interviewing" when both exist
      if (stage.key === "Interviewing" && counts["Currently Interviewing"] != null) {
        return Number(counts["Currently Interviewing"] || 0) === 0
      }
      return arr.findIndex((s) => s.key === stage.key) === index
    }).map((stage) => ({
      ...stage,
      value: Number(counts[stage.key] ?? 0),
    }))

    const customFromCounts = Object.keys(StatusCounts).filter(
      (key) => key && !knownKeys.has(key)
    )
    const customNamed = (CustomStatuses || [])
      .map((item) => (typeof item === "string" ? item : item?.name))
      .filter((name) => name && !knownKeys.has(name) && !customFromCounts.includes(name))

    const custom = [...customFromCounts, ...customNamed].map((key, index) => ({
      key,
      label: key,
      color: CUSTOM_COLORS[index % CUSTOM_COLORS.length],
      value: Number(counts[key] ?? 0),
    }))

    // Keep default stages that have counts, plus all custom statuses (even at 0)
    const defaults = base.filter(
      (stage) =>
        stage.value > 0 ||
        ["Applied", "Under Review", "Hired", "Rejected"].includes(stage.key)
    )

    return [...defaults, ...custom]
  }, [
    AppliedCount,
    UnderReviewCount,
    WaitingResponse,
    CurrentlyInterviewing,
    HiredCount,
    RejectedCount,
    StatusCounts,
    CustomStatuses,
  ])

  const stageTotal = stages.reduce((sum, stage) => sum + stage.value, 0)
  const total = stageTotal || 1

  return (
    <section className="px-4 lg:px-6">
      <Card>
        <CardHeader className="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
          <div className="space-y-1.5">
            <CardTitle>Pipeline stages</CardTitle>
            <CardDescription>
              Includes default and custom statuses from your applications
            </CardDescription>
          </div>
          <p className="text-xs font-medium tracking-[0.14em] text-muted-foreground uppercase">
            {stageTotal} applications
          </p>
        </CardHeader>
        <CardContent className="space-y-5">
          <div className="flex h-2.5 overflow-hidden rounded-full bg-muted">
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
              <Card key={stage.key} size="sm" className="shadow-none">
                <CardContent className="pt-0">
                  <div className="mb-2 flex items-center gap-2">
                    <span className={cn("size-2 shrink-0 rounded-full", stage.color)} />
                    <span className="truncate text-[11px] font-semibold tracking-[0.1em] text-muted-foreground uppercase">
                      {stage.label}
                    </span>
                  </div>
                  <p className="font-heading text-xl font-semibold tabular-nums">
                    {stage.value}
                  </p>
                  <p className="text-[11px] text-muted-foreground tabular-nums">
                    {Math.round((stage.value / total) * 100)}%
                  </p>
                </CardContent>
              </Card>
            ))}
          </div>
        </CardContent>
      </Card>
    </section>
  )
}
