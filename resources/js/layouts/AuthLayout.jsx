import { Link } from "@inertiajs/react"
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "@/components/ui/card"
import { BriefcaseIcon } from "lucide-react"

export default function AuthLayout({ children, title, subtitle }) {
  return (
    <div className="flex min-h-svh flex-col items-center justify-center bg-muted/30 p-6">
      <div className="w-full max-w-md space-y-6">
        {/* Logo */}
        <div className="flex flex-col items-center gap-3">
          <Link
            href="/"
            className="flex items-center gap-2 text-lg font-semibold tracking-tight"
          >
            <span className="flex size-10 items-center justify-center rounded-xl bg-primary text-primary-foreground shadow-sm shadow-primary/25">
              <BriefcaseIcon className="size-5" />
            </span>
            <span className="font-heading">Job Tracker</span>
          </Link>
        </div>

        {/* Card */}
        <Card>
          {(title || subtitle) && (
            <CardHeader className="text-center">
              {title && <CardTitle className="text-xl">{title}</CardTitle>}
              {subtitle && <CardDescription>{subtitle}</CardDescription>}
            </CardHeader>
          )}
          <CardContent>{children}</CardContent>
        </Card>

        {/* Optional footer note — edit or remove */}
        <p className="text-center text-xs text-muted-foreground">
          Secure user-level tracking for your job search.
        </p>
      </div>
    </div>
  )
}