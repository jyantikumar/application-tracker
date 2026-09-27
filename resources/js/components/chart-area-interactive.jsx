import * as React from "react"
import { Area, AreaChart, CartesianGrid, XAxis, YAxis } from "recharts"

import { useIsMobile } from "@/hooks/use-mobile"
import {
  Card,
  CardAction,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "@/components/ui/card"
import {
  ChartContainer,
  ChartTooltip,
  ChartTooltipContent,
} from "@/components/ui/chart"
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select"
import { ToggleGroup, ToggleGroupItem } from "@/components/ui/toggle-group"

const chartConfig = {
  applications: {
    label: "Applications",
    color: "var(--primary)",
  },
}

function toChartSeries(ApplicationsByDate = {}) {
  return Object.entries(ApplicationsByDate)
    .map(([date, count]) => ({
      date,
      applications: Number(count) || 0,
    }))
    .filter((row) => row.date && row.date !== "Unknown")
    .sort((a, b) => new Date(a.date) - new Date(b.date))
}

export function ChartAreaInteractive({ ApplicationsByDate = {} }) {
  const isMobile = useIsMobile()
  const [timeRange, setTimeRange] = React.useState("90d")
  const chartData = React.useMemo(
    () => toChartSeries(ApplicationsByDate),
    [ApplicationsByDate]
  )

  React.useEffect(() => {
    if (isMobile) setTimeRange("30d")
  }, [isMobile])

  const filteredData = React.useMemo(() => {
    if (!chartData.length) return []

    const referenceDate = new Date(chartData[chartData.length - 1].date)
    let daysToSubtract = 90
    if (timeRange === "30d") daysToSubtract = 30
    if (timeRange === "7d") daysToSubtract = 7

    const startDate = new Date(referenceDate)
    startDate.setDate(startDate.getDate() - daysToSubtract)

    return chartData.filter((item) => new Date(item.date) >= startDate)
  }, [chartData, timeRange])

  const total = filteredData.reduce((sum, row) => sum + row.applications, 0)
  const peak = filteredData.reduce(
    (max, row) => Math.max(max, row.applications),
    0
  )

  return (
    <Card className="flex flex-col">
      <CardHeader>
        <CardTitle>Activity</CardTitle>
        <CardDescription>
          <span className="font-medium text-foreground tabular-nums">{total}</span>
          {" "}logged
          {peak > 0 && (
            <>
              {" · "}peak{" "}
              <span className="font-medium text-foreground tabular-nums">{peak}</span>
              /day
            </>
          )}
        </CardDescription>
        <CardAction>
          <ToggleGroup
            type="single"
            value={timeRange}
            onValueChange={(value) => value && setTimeRange(value)}
            variant="outline"
            className="hidden sm:flex"
          >
            <ToggleGroupItem value="90d" className="px-3 text-xs">
              90d
            </ToggleGroupItem>
            <ToggleGroupItem value="30d" className="px-3 text-xs">
              30d
            </ToggleGroupItem>
            <ToggleGroupItem value="7d" className="px-3 text-xs">
              7d
            </ToggleGroupItem>
          </ToggleGroup>

          <Select value={timeRange} onValueChange={setTimeRange}>
            <SelectTrigger className="w-[7.5rem] sm:hidden" size="sm" aria-label="Time range">
              <SelectValue />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="90d">Last 90 days</SelectItem>
              <SelectItem value="30d">Last 30 days</SelectItem>
              <SelectItem value="7d">Last 7 days</SelectItem>
            </SelectContent>
          </Select>
        </CardAction>
      </CardHeader>

      <CardContent className="flex-1">
        <ChartContainer config={chartConfig} className="aspect-auto h-[180px] w-full">
          {filteredData.length === 0 ? (
            <div className="flex h-full items-center justify-center text-sm text-muted-foreground">
              No activity in this range yet.
            </div>
          ) : (
            <AreaChart data={filteredData} margin={{ top: 8, right: 8, left: -16, bottom: 0 }}>
              <defs>
                <linearGradient id="fillApplications" x1="0" y1="0" x2="0" y2="1">
                  <stop offset="0%" stopColor="var(--color-applications)" stopOpacity={0.35} />
                  <stop offset="100%" stopColor="var(--color-applications)" stopOpacity={0.02} />
                </linearGradient>
              </defs>
              <CartesianGrid vertical={false} strokeDasharray="3 6" className="stroke-border/50" />
              <XAxis
                dataKey="date"
                tickLine={false}
                axisLine={false}
                tickMargin={10}
                minTickGap={28}
                tick={{ fontSize: 11 }}
                tickFormatter={(value) =>
                  new Date(value).toLocaleDateString("en-US", {
                    month: "short",
                    day: "numeric",
                  })
                }
              />
              <YAxis
                allowDecimals={false}
                tickLine={false}
                axisLine={false}
                width={28}
                tick={{ fontSize: 11 }}
              />
              <ChartTooltip
                cursor={{ stroke: "var(--primary)", strokeOpacity: 0.25 }}
                content={
                  <ChartTooltipContent
                    labelFormatter={(value) =>
                      new Date(value).toLocaleDateString("en-US", {
                        month: "short",
                        day: "numeric",
                        year: "numeric",
                      })
                    }
                    indicator="dot"
                  />
                }
              />
              <Area
                dataKey="applications"
                type="monotone"
                fill="url(#fillApplications)"
                stroke="var(--color-applications)"
                strokeWidth={2.25}
              />
            </AreaChart>
          )}
        </ChartContainer>
      </CardContent>
    </Card>
  )
}