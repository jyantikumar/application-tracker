import { Head, usePage } from "@inertiajs/react"
import { DashboardHero } from "@/components/dashboard-hero"
import { PipelineStages } from "@/components/pipeline-stages"
import { ChartAreaInteractive } from "@/components/chart-area-interactive"
import {
  BreakdownList,
  InterviewConversionCard,
} from "@/components/dashboard-breakdown-charts"
import { RecentApplicationsTable } from "@/components/recent-applications-table"
import { WorkSetupDonut } from "@/components/work-setup-donut"
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
  const { flash, applicationStatuses } = usePage().props

  const customStatuses = Array.isArray(applicationStatuses)
    ? applicationStatuses
    : []

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
        CustomStatuses={customStatuses}
      />

      {/* Section 1: Activity + Interview conversion, side by side */}
      <section className="px-4 lg:px-6">
  <ChartAreaInteractive ApplicationsByDate={ApplicationsByDate} />
</section>

      {/* Section 2: Source, Location, Work setup — three equal columns */}
      <section className="grid grid-cols-1 gap-4 px-4 md:grid-cols-2 lg:grid-cols-3 lg:px-6">
        <BreakdownList title="By source" data={ApplicationsBySource} />
        <BreakdownList title="By location" data={ApplicationsByLocation} />
        <WorkSetupDonut ApplicationsByJobType={ApplicationsByJobType} />
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