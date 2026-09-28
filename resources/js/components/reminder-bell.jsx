import { Link, usePage, router } from "@inertiajs/react"
import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from "@/components/ui/popover"
import { Separator } from "@/components/ui/separator"
import { BellIcon, ClockIcon } from "lucide-react"

export function ReminderBell() {
  const { activeReminders = [] } = usePage().props
  const reminders = activeReminders || []
  const count = reminders.length

  const dismiss = (id) => {
    router.post(route("reminders.dismiss", id), {}, { preserveScroll: true })
  }

  const snooze = (id) => {
    router.post(
      route("reminders.snooze", id),
      { hours: 24 },
      { preserveScroll: true }
    )
  }

  const dismissAll = () => {
    router.post(route("reminders.dismiss-all"), {}, { preserveScroll: true })
  }

  return (
    <Popover>
      <PopoverTrigger asChild>
        <Button
          type="button"
          variant="ghost"
          size="icon"
          className="relative"
          aria-label={
            count ? `${count} active reminders` : "No active reminders"
          }
        >
          <BellIcon className="size-4" />
          {count > 0 && (
            <Badge
              variant="destructive"
              className="absolute -top-1 -right-1 h-4 min-w-4 justify-center rounded-full px-1 text-[10px]"
            >
              {count > 9 ? "9+" : count}
            </Badge>
          )}
        </Button>
      </PopoverTrigger>
      <PopoverContent align="end" className="w-80 p-0">
        <div className="flex items-center justify-between gap-2 px-4 py-3">
          <div>
            <p className="text-sm font-semibold">Reminders</p>
            <p className="text-xs text-muted-foreground">
              {count
                ? `${count} active nudge${count === 1 ? "" : "s"}`
                : "You're all caught up"}
            </p>
          </div>
          {count > 0 && (
            <Button type="button" variant="ghost" size="sm" onClick={dismissAll}>
              Dismiss all
            </Button>
          )}
        </div>
        <Separator />
        <div className="max-h-80 overflow-y-auto">
          {count === 0 ? (
            <p className="px-4 py-8 text-center text-sm text-muted-foreground">
              No follow-ups or interview alerts right now.
            </p>
          ) : (
            reminders.map((reminder) => (
              <div
                key={reminder.id}
                className="space-y-2 border-b border-border/50 px-4 py-3 last:border-b-0"
              >
                <p className="text-sm font-medium">{reminder.title}</p>
                <p className="text-xs text-muted-foreground">{reminder.body}</p>
                <div className="flex gap-2">
                  <Button
                    type="button"
                    size="sm"
                    variant="outline"
                    onClick={() => snooze(reminder.id)}
                  >
                    <ClockIcon />
                    Snooze
                  </Button>
                  <Button
                    type="button"
                    size="sm"
                    variant="ghost"
                    onClick={() => dismiss(reminder.id)}
                  >
                    Dismiss
                  </Button>
                </div>
              </div>
            ))
          )}
        </div>
        <Separator />
        <div className="p-2">
          <Button asChild variant="ghost" size="sm" className="w-full justify-center">
            <Link href={route("notifications.index")}>View all notifications</Link>
          </Button>
        </div>
      </PopoverContent>
    </Popover>
  )
}
