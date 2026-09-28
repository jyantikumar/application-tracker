import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"
import { useApplicationModal } from "@/components/application-modal-provider"
import {
  SidebarGroup,
  SidebarGroupContent,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
  useSidebar,
} from "@/components/ui/sidebar"
import { Link } from "@inertiajs/react"
import { CirclePlusIcon, UploadIcon } from "lucide-react"

export function NavMain({ items = [], onImport }) {
  const { isMobile, setOpenMobile } = useSidebar()
  const { openCreate } = useApplicationModal()

  const closeMobile = () => {
    if (isMobile) setOpenMobile(false)
  }

  return (
    <SidebarGroup>
      <SidebarGroupContent className="flex flex-col gap-2">
        <SidebarMenu>
          <SidebarMenuItem className="flex items-center gap-2">
            <SidebarMenuButton
              tooltip="Log Application"
              className="min-w-8 bg-primary text-primary-foreground duration-200 ease-linear hover:bg-primary/90 hover:text-primary-foreground active:bg-primary/90 active:text-primary-foreground"
              onClick={() => {
                closeMobile()
                openCreate()
              }}
            >
              <CirclePlusIcon />
              <span>Log Application</span>
            </SidebarMenuButton>
            <Button
              size="icon"
              className="size-8 group-data-[collapsible=icon]:opacity-0"
              variant="outline"
              onClick={onImport}
              title="Import CSV"
            >
              <UploadIcon />
              <span className="sr-only">Import CSV</span>
            </Button>
          </SidebarMenuItem>
        </SidebarMenu>
        <SidebarMenu>
          {items.map((item) => (
            <SidebarMenuItem key={item.title}>
              <SidebarMenuButton
                asChild
                tooltip={item.title}
                isActive={item.isActive}
              >
                <Link href={item.url} onClick={closeMobile}>
                  {item.icon}
                  <span>{item.title}</span>
                  {item.badge != null && (
                    <Badge
                      variant="destructive"
                      className="ml-auto h-5 min-w-5 justify-center rounded-full px-1.5 text-[10px] group-data-[collapsible=icon]:hidden"
                    >
                      {item.badge > 9 ? "9+" : item.badge}
                    </Badge>
                  )}
                </Link>
              </SidebarMenuButton>
            </SidebarMenuItem>
          ))}
        </SidebarMenu>
      </SidebarGroupContent>
    </SidebarGroup>
  )
}
