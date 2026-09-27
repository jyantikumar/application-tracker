import { useEffect } from "react"
import { usePage } from "@inertiajs/react"
import { toast } from "sonner"

export function ToastProvider() {
  const { flash, errors } = usePage().props

  // Flash messages from Laravel -> with('success', '...')
  useEffect(() => {
    if (flash?.success) toast.success(flash.success)
    if (flash?.error) toast.error(flash.error)
    if (flash?.info) toast.info(flash.info)
    if (flash?.warning) toast.warning(flash.warning)
    if (flash?.status) toast.success(flash.status) // Breeze "status" (e.g. reset link sent)
  }, [flash])

  // Validation errors (including "These credentials do not match our records.")
  useEffect(() => {
    if (errors && Object.keys(errors).length > 0) {
      const firstError = Object.values(errors)[0]
      if (firstError) toast.error(firstError)
    }
  }, [errors])

  return null
}