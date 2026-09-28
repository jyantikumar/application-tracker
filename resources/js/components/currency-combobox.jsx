import React from "react"
import { Combobox } from "@/components/ui/combobox"
import { CURRENCIES, FindCurrency } from "@/lib/currencies"

export function CurrencyCombobox({
  value = "USD",
  onValueChange,
  disabled = false,
  className,
  currencies = CURRENCIES,
}) {
  return (
    <Combobox
      options={currencies}
      value={value}
      onValueChange={onValueChange}
      disabled={disabled}
      className={className}
      placeholder="Select currency"
      searchPlaceholder="Search currency…"
      emptyMessage="No currency found."
      getOptionValue={(Currency) => Currency.code}
      getOptionLabel={(Currency) =>
        `${Currency.code} — ${Currency.name}`
      }
      getOptionKeywords={(Currency) => [
        Currency.code,
        Currency.name,
        Currency.symbol,
      ]}
      renderValue={(Currency) => (
        <span className="inline-flex items-center gap-2">
          <span className="font-medium tabular-nums">{Currency.symbol}</span>
          <span>{Currency.code}</span>
          <span className="text-muted-foreground">· {Currency.name}</span>
        </span>
      )}
      renderOption={(Currency) => (
        <span className="inline-flex min-w-0 items-center gap-2">
          <span className="w-8 shrink-0 font-medium tabular-nums text-muted-foreground">
            {Currency.symbol}
          </span>
          <span className="w-10 shrink-0 font-semibold">{Currency.code}</span>
          <span className="truncate text-muted-foreground">{Currency.name}</span>
        </span>
      )}
    />
  )
}

export function CurrencyDisplay({
  code,
  amount,
  className = "",
  showName = false,
}) {
  const Currency = FindCurrency(code)
  const Label = Currency
    ? showName
      ? `${Currency.symbol} ${Currency.code} · ${Currency.name}`
      : `${Currency.symbol} ${Currency.code}`
    : code || "—"

  return (
    <span
      className={`pointer-events-none select-none tabular-nums ${className}`}
      aria-hidden={false}
    >
      {amount != null ? (
        <>
          {Label}{" "}
          {typeof amount === "number" ? amount.toLocaleString() : amount}
        </>
      ) : (
        Label
      )}
    </span>
  )
}
