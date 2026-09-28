"use client"

import * as React from "react"
import { cn } from "cn"
import { CheckIcon, ChevronsUpDownIcon, SearchIcon } from "lucide-react"
import { Button } from "@/components/ui/button"
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from "@/components/ui/popover"
import { Input } from "@/components/ui/input"

function Combobox({
  options = [],
  value,
  onValueChange,
  placeholder = "Select…",
  searchPlaceholder = "Search…",
  emptyMessage = "No results found.",
  disabled = false,
  className,
  buttonClassName,
  contentClassName,
  getOptionValue = (Option) => Option?.value ?? Option?.code ?? String(Option),
  getOptionLabel = (Option) => Option?.label ?? Option?.name ?? String(Option),
  getOptionKeywords = (Option) => [
    getOptionValue(Option),
    getOptionLabel(Option),
    Option?.symbol,
  ].filter(Boolean),
  renderOption,
  renderValue,
  ...props
}) {
  const [Open, setOpen] = React.useState(false)
  const [Query, setQuery] = React.useState("")
  const InputRef = React.useRef(null)

  const Selected = React.useMemo(
    () => options.find((Option) => getOptionValue(Option) === value) ?? null,
    [options, value, getOptionValue]
  )

  const Filtered = React.useMemo(() => {
    const Needle = Query.trim().toLowerCase()
    if (!Needle) return options
    return options.filter((Option) =>
      getOptionKeywords(Option).some((Keyword) =>
        String(Keyword).toLowerCase().includes(Needle)
      )
    )
  }, [options, Query, getOptionKeywords])

  React.useEffect(() => {
    if (!Open) {
      setQuery("")
      return
    }
    const Id = window.setTimeout(() => InputRef.current?.focus(), 0)
    return () => window.clearTimeout(Id)
  }, [Open])

  return (
    <Popover open={Open} onOpenChange={setOpen} {...props}>
      <PopoverTrigger asChild>
        <Button
          type="button"
          variant="outline"
          role="combobox"
          aria-expanded={Open}
          disabled={disabled}
          className={cn(
            "w-full justify-between font-normal",
            !Selected && "text-muted-foreground",
            buttonClassName,
            className
          )}
        >
          <span className="truncate">
            {Selected
              ? renderValue
                ? renderValue(Selected)
                : getOptionLabel(Selected)
              : placeholder}
          </span>
          <ChevronsUpDownIcon className="ml-2 size-4 shrink-0 opacity-50" />
        </Button>
      </PopoverTrigger>
      <PopoverContent
        align="start"
        className={cn("w-[var(--radix-popover-trigger-width)] p-0", contentClassName)}
      >
        <div className="flex items-center gap-2 border-b border-border px-3">
          <SearchIcon className="size-4 shrink-0 text-muted-foreground" />
          <Input
            ref={InputRef}
            value={Query}
            onChange={(Event) => setQuery(Event.target.value)}
            placeholder={searchPlaceholder}
            className="h-10 border-0 bg-transparent px-0 shadow-none focus-visible:ring-0 dark:bg-transparent"
          />
        </div>
        <div
          role="listbox"
          className="max-h-60 overflow-y-auto p-1"
        >
          {Filtered.length === 0 ? (
            <p className="px-2 py-6 text-center text-sm text-muted-foreground">
              {emptyMessage}
            </p>
          ) : (
            Filtered.map((Option) => {
              const OptionValue = getOptionValue(Option)
              const IsSelected = OptionValue === value
              return (
                <button
                  key={OptionValue}
                  type="button"
                  role="option"
                  aria-selected={IsSelected}
                  className={cn(
                    "relative flex w-full cursor-default items-center gap-2 rounded-sm py-1.5 pr-8 pl-2 text-left text-sm outline-none select-none hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground",
                    IsSelected && "bg-accent/60"
                  )}
                  onClick={() => {
                    onValueChange?.(OptionValue)
                    setOpen(false)
                  }}
                >
                  {renderOption ? renderOption(Option) : getOptionLabel(Option)}
                  {IsSelected && (
                    <span className="absolute right-2 flex size-4 items-center justify-center">
                      <CheckIcon className="size-4" />
                    </span>
                  )}
                </button>
              )
            })
          )}
        </div>
      </PopoverContent>
    </Popover>
  )
}

export { Combobox }
