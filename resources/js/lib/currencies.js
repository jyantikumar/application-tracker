export const CURRENCIES = [
  { code: "PHP", name: "Philippine Peso", symbol: "₱" },
  { code: "USD", name: "US Dollar", symbol: "$" },
  { code: "EUR", name: "Euro", symbol: "€" },
  { code: "GBP", name: "British Pound", symbol: "£" },
  { code: "AUD", name: "Australian Dollar", symbol: "A$" },
  { code: "CAD", name: "Canadian Dollar", symbol: "C$" },
  { code: "NZD", name: "New Zealand Dollar", symbol: "NZ$" },
  { code: "SGD", name: "Singapore Dollar", symbol: "S$" },
  { code: "HKD", name: "Hong Kong Dollar", symbol: "HK$" },
  { code: "JPY", name: "Japanese Yen", symbol: "¥" },
  { code: "CNY", name: "Chinese Yuan", symbol: "¥" },
  { code: "KRW", name: "South Korean Won", symbol: "₩" },
  { code: "INR", name: "Indian Rupee", symbol: "₹" },
  { code: "IDR", name: "Indonesian Rupiah", symbol: "Rp" },
  { code: "MYR", name: "Malaysian Ringgit", symbol: "RM" },
  { code: "THB", name: "Thai Baht", symbol: "฿" },
  { code: "CHF", name: "Swiss Franc", symbol: "CHF" },
  { code: "SEK", name: "Swedish Krona", symbol: "kr" },
  { code: "NOK", name: "Norwegian Krone", symbol: "kr" },
  { code: "DKK", name: "Danish Krone", symbol: "kr" },
  { code: "PLN", name: "Polish Zloty", symbol: "zł" },
  { code: "CZK", name: "Czech Koruna", symbol: "Kč" },
  { code: "HUF", name: "Hungarian Forint", symbol: "Ft" },
  { code: "TRY", name: "Turkish Lira", symbol: "₺" },
  { code: "ZAR", name: "South African Rand", symbol: "R" },
  { code: "BRL", name: "Brazilian Real", symbol: "R$" },
  { code: "MXN", name: "Mexican Peso", symbol: "Mex$" },
  { code: "ILS", name: "Israeli Shekel", symbol: "₪" },
  { code: "BGN", name: "Bulgarian Lev", symbol: "лв" },
  { code: "RON", name: "Romanian Leu", symbol: "lei" },
  { code: "ISK", name: "Icelandic Krona", symbol: "kr" },
]

export function FindCurrency(Code) {
  const Normalized = String(Code || "").toUpperCase()
  return CURRENCIES.find((Currency) => Currency.code === Normalized) ?? null
}

export function FormatCurrencyAmount(Amount, CurrencyCode = "PHP", Locale = "en-PH") {
  const Numeric = Number(Amount)
  if (!Number.isFinite(Numeric)) return "—"

  try {
    return new Intl.NumberFormat(Locale, {
      style: "currency",
      currency: CurrencyCode,
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    }).format(Numeric)
  } catch {
    return `${CurrencyCode} ${Numeric.toFixed(2)}`
  }
}

export function FormatPhpAmount(Amount) {
  return FormatCurrencyAmount(Amount, "PHP", "en-PH")
}

export function FormatPhpRange(MinAmount, MaxAmount) {
  const Min = Number(MinAmount)
  const Max = Number(MaxAmount)
  if (!Number.isFinite(Min) || !Number.isFinite(Max)) return "—"
  if (Min === Max) return FormatPhpAmount(Min)
  return `${FormatPhpAmount(Min)} – ${FormatPhpAmount(Max)}`
}

/** Convert a pay-rate amount into an estimated monthly figure. */
export function ToMonthlyAmount(Amount, PayRateType = "Monthly", HoursPerWeek = 40) {
  const Numeric = Number(Amount)
  if (!Number.isFinite(Numeric) || Numeric < 0) return null

  if (PayRateType === "Hourly") {
    const Hours = Number(HoursPerWeek) || 40
    return (Numeric * Hours * 52) / 12
  }

  return Numeric
}

/**
 * Fetch PHP conversion from the Laravel currency endpoint.
 * @returns {Promise<{ amount: number, rate: number, from: string, to: string, formatted: string } | null>}
 */
export async function ConvertToPhp(FromCurrency, Amount) {
  const Code = String(FromCurrency || "").toUpperCase()
  const Numeric = Number(Amount)

  if (!Code || !Number.isFinite(Numeric) || Numeric < 0) {
    return null
  }

  if (Code === "PHP") {
    return {
      amount: Numeric,
      rate: 1,
      from: "PHP",
      to: "PHP",
      formatted: FormatPhpAmount(Numeric),
    }
  }

  const Url = `${route("currencies.convert")}?${new URLSearchParams({
    from: Code,
    amount: String(Numeric),
  }).toString()}`

  const Response = await fetch(Url, {
    headers: {
      Accept: "application/json",
      "X-Requested-With": "XMLHttpRequest",
    },
    credentials: "same-origin",
  })

  if (!Response.ok) {
    throw new Error("Conversion request failed")
  }

  return Response.json()
}

/**
 * Convert a min/max monthly range to PHP using one rate lookup.
 * @returns {Promise<{ rate: number, from: string, min: number, max: number, formatted: string } | null>}
 */
export async function ConvertRangeToPhp(FromCurrency, MinMonthly, MaxMonthly) {
  const Min = Number(MinMonthly)
  const Max = Number(MaxMonthly)
  if (!Number.isFinite(Min) || !Number.isFinite(Max) || Min < 0 || Max < 0) {
    return null
  }

  const [Low, High] = Min <= Max ? [Min, Max] : [Max, Min]
  const Base = await ConvertToPhp(FromCurrency, Low)
  if (!Base) return null

  const Rate = Number(Base.rate) || 0
  const MinPhp = Number(Base.amount)
  const MaxPhp = Math.round(High * Rate * 100) / 100

  return {
    rate: Rate,
    from: Base.from,
    min: MinPhp,
    max: MaxPhp,
    formatted: FormatPhpRange(MinPhp, MaxPhp),
  }
}
