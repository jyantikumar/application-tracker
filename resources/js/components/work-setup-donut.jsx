import * as React from "react"
import { Cell, Pie, PieChart } from "recharts"

import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "@/components/ui/card"
import {
  ChartContainer,
  ChartLegend,
  ChartLegendContent,
  ChartTooltip,
  ChartTooltipContent,
} from "@/components/ui/chart"

const COLORS = [
  "var(--chart-1)",
  "var(--chart-2)",
  "var(--chart-3)",
  "var(--chart-4)",
  "var(--chart-5)",
  "var(--primary)",
]

export function WorkSetupDonut({ ApplicationsByJobType = {} }) {
  const data = React.useMemo(
    () =>
      Object.entries(ApplicationsByJobType)
        .map(([name, value]) => ({ name, value: Number(value) || 0 }))
        .filter((row) => row.name && row.value > 0)
        .sort((a, b) => b.value - a.value),
    [ApplicationsByJobType]
  )

  const total = data.reduce((sum, row) => sum + row.value, 0)

  const chartConfig = React.useMemo(() => {
    const config = { value: { label: "Applications" } }
    data.forEach((row, index) => {
      config[row.name] = {
        label: row.name,
        color: COLORS[index % COLORS.length],
      }
    })
    return config
  }, [data])

  return (
    <Card className="flex h-full flex-col">
      <CardHeader>
        <CardTitle>Work setup</CardTitle>
        <CardDescription>
          {total > 0 ? (
            <>
              Distribution across{" "}
              <span className="font-medium text-foreground tabular-nums">
                {total}
              </span>{" "}
              applications
            </>
          ) : (
            "No work setups recorded yet"
          )}
        </CardDescription>
      </CardHeader>

      <CardContent className="flex-1">
        {data.length === 0 ? (
          <div className="flex h-[220px] items-center justify-center text-sm text-muted-foreground">
            No work setup data yet.
          </div>
        ) : (
          <ChartContainer
            config={chartConfig}
            className="mx-auto aspect-square h-[220px]"
          >
            <PieChart>
              <ChartTooltip
                cursor={false}
                content={<ChartTooltipContent hideLabel nameKey="name" />}
              />
              <Pie
                data={data}
                dataKey="value"
                nameKey="name"
                innerRadius={55}
                outerRadius={85}
                paddingAngle={2}
                stroke="var(--background)"
                strokeWidth={2}
              >
                {data.map((row, index) => (
                  <Cell
                    key={row.name}
                    fill={COLORS[index % COLORS.length]}
                  />
                ))}
              </Pie>
              <ChartLegend
                content={<ChartLegendContent nameKey="name" />}
                className="mt-2 flex-wrap gap-2"
              />
            </PieChart>
          </ChartContainer>
        )}
      </CardContent>
    </Card>
  )
}