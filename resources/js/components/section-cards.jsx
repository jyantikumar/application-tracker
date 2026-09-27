import { Link } from "@inertiajs/react"
import {
  Card,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from "@/components/ui/card"
import { Badge } from "@/components/ui/badge"
import {
  BriefcaseIcon,
  CircleCheckIcon,
  MessageSquareIcon,
  TrendingUpIcon,
} from "lucide-react"

export function SectionCards({
  TotalApplications = 0,
  CurrentlyInterviewing = 0,
  HiredCount = 0,
  InterviewsDone = 0,
}) {
  const cards = [
    {
      title: "Total applications",
      value: TotalApplications,
      description: "All roles logged",
      icon: BriefcaseIcon,
      href: route("job-applications.applications"),
    },
    {
      title: "Interviewing",
      value: CurrentlyInterviewing,
      description: "Active interview loops",
      icon: MessageSquareIcon,
    },
    {
      title: "Interviewed",
      value: InterviewsDone,
      description: "Reached interview stage",
      icon: TrendingUpIcon,
    },
    {
      title: "Hired",
      value: HiredCount,
      description: "Offers accepted",
      icon: CircleCheckIcon,
    },
  ]

  return (
    <div className="grid grid-cols-1 gap-4 px-4 *:data-[slot=card]:shadow-xs @xl/main:grid-cols-2 @5xl/main:grid-cols-4 lg:px-6">
      {cards.map((card) => (
        <Card key={card.title} className="@container/card border-border/60 bg-card/80">
          <CardHeader className="relative">
            <CardDescription className="flex items-center gap-2">
              <card.icon className="size-3.5 text-primary" />
              {card.title}
            </CardDescription>
            <CardTitle className="font-heading text-3xl font-semibold tabular-nums @[250px]/card:text-4xl">
              {card.value}
            </CardTitle>
            <Badge variant="secondary" className="absolute top-4 right-4 rounded-lg">
              Live
            </Badge>
          </CardHeader>
          <CardFooter className="flex-col items-start gap-1 text-sm">
            <div className="text-muted-foreground">{card.description}</div>
            {card.href && (
              <Link
                href={card.href}
                className="text-xs font-medium text-primary hover:underline"
              >
                View applications
              </Link>
            )}
          </CardFooter>
        </Card>
      ))}
    </div>
  )
}
