import { Head, usePage } from "@inertiajs/react"
import { DashboardHero } from "@/components/dashboard-hero"
import { PipelineStages } from "@/components/pipeline-stages"
import { ChartAreaInteractive } from "@/components/chart-area-interactive"
import { DashboardBreakdownCharts } from "@/components/dashboard-breakdown-charts"
import { RecentApplicationsTable } from "@/components/recent-applications-table"
import { Alert, AlertDescription } from "@/components/ui/alert"

export default function Index({
  TotalApplications = 0,
  InterviewsDone = 0,
  AppliedCount = 0,
  UnderReviewCount = 0,
  CurrentlyInterviewing = 0,
  HiredCount = 0,
  RejectedCount = 0,
  WaitingResponse = 0,
  StatusCounts = {},
  ApplicationsBySource = {},
  ApplicationsByDate = {},
  ApplicationsByJobType = {},
  ApplicationsByLocation = {},
  RecentApplications = [],
}) {
  const { flash, applicationStatuses = [] } = usePage().props

  return (
    <>
      <Head title="Dashboard" />

      {flash?.success && (
        <div className="px-4 lg:px-6">
          <Alert className="border-primary/30 bg-primary/10 text-primary">
            <AlertDescription>{flash.success}</AlertDescription>
          </Alert>
        </div>
      )}

      <DashboardHero
        TotalApplications={TotalApplications}
        CurrentlyInterviewing={CurrentlyInterviewing}
        HiredCount={HiredCount}
      />

      <PipelineStages
        AppliedCount={AppliedCount}
        UnderReviewCount={UnderReviewCount}
        WaitingResponse={WaitingResponse}
        CurrentlyInterviewing={CurrentlyInterviewing}
        HiredCount={HiredCount}
        RejectedCount={RejectedCount}
        StatusCounts={StatusCounts}
        CustomStatuses={applicationStatuses}
      />

      <section className="grid grid-cols-1 gap-4 px-4 lg:grid-cols-[1.4fr_1fr] lg:px-6">
        <ChartAreaInteractive ApplicationsByDate={ApplicationsByDate} />
        <DashboardBreakdownCharts
          ApplicationsBySource={ApplicationsBySource}
          ApplicationsByJobType={ApplicationsByJobType}
          ApplicationsByLocation={ApplicationsByLocation}
          InterviewsDone={InterviewsDone}
          TotalApplications={TotalApplications}
        />
      </section>

      <RecentApplicationsTable RecentApplications={RecentApplications} />
    </>
  )
}

Index.layoutProps = {
  active: "dashboard",
  title: "Overview",
  breadcrumbs: [{ label: "Overview" }],
  flush: true,
}