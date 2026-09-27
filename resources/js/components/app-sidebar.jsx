import * as React from "react"
import { Link, usePage } from "@inertiajs/react"
import { NavMain } from "@/components/nav-main"
import { NavSecondary } from "@/components/nav-secondary"
import { NavUser } from "@/components/nav-user"
import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarHeader,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
} from "@/components/ui/sidebar"
import {
  LayoutDashboardIcon,
  BriefcaseIcon,
  FolderKanbanIcon,
  TagIcon,
  UserIcon,
} from "lucide-react"

export function AppSidebar({ active = "dashboard", onImport, ...props }) {
  const { auth } = usePage().props

  const navMain = [
    {
      title: "Overview",
      url: route("dashboard"),
      icon: <LayoutDashboardIcon />,
      isActive: active === "dashboard",
    },
    {
      title: "Applications",
      url: route("job-applications.applications"),
      icon: <BriefcaseIcon />,
      isActive: active === "applications",
    },
  ]

  const navSecondary = [
    {
      title: "Sources",
      url: route("application-sources.index"),
      icon: <FolderKanbanIcon />,
      isActive: active === "sources",
    },
    {
      title: "Statuses",
      url: route("application-statuses.index"),
      icon: <TagIcon />,
      isActive: active === "statuses",
    },
    {
      title: "Profile",
      url: route("profile.edit"),
      icon: <UserIcon />,
      isActive: active === "profile",
    },
  ]

  const user = {
    name: auth?.user?.name ?? "User",
    email: auth?.user?.email ?? "",
    avatar: auth?.user?.avatar ?? "",
  }

  return (
    <Sidebar collapsible="icon" {...props}>
      <SidebarHeader className="border-b border-sidebar-border/60">
        <SidebarMenu>
          <SidebarMenuItem>
            <SidebarMenuButton
              asChild
              size="lg"
              className="data-[slot=sidebar-menu-button]:gap-3"
            >
              <Link href={route("dashboard")}>
                <span className="flex size-8 shrink-0 items-center justify-center rounded-xl bg-primary text-sm font-bold text-primary-foreground shadow-sm shadow-primary/25">
                  JT
                </span>
                <span className="flex min-w-0 flex-col items-start leading-tight group-data-[collapsible=icon]:hidden">
                  <span className="truncate text-sm font-semibold tracking-tight">
                    Job Tracker
                  </span>
                  <span className="text-[10px] font-medium uppercase tracking-[0.14em] text-muted-foreground">
                    Pipeline
                  </span>
                </span>
              </Link>
            </SidebarMenuButton>
          </SidebarMenuItem>
        </SidebarMenu>
      </SidebarHeader>
      <SidebarContent className="gap-0 pt-2">
        <NavMain
          items={navMain}
          onImport={onImport}
        />
        <NavSecondary items={navSecondary} className="mt-auto" />
      </SidebarContent>
      <SidebarFooter className="border-t border-sidebar-border/60">
        <NavUser user={user} />
      </SidebarFooter>
    </Sidebar>
  )
}
