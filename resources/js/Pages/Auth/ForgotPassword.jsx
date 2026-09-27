import { Head, Link, useForm } from "@inertiajs/react"
import AuthLayout from "@/layouts/AuthLayout"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"

export default function ForgotPassword({ status }) {
  const { data, setData, post, processing, errors } = useForm({
    email: "",
  })

  const submit = (e) => {
    e.preventDefault()
    post(route("password.email"))
  }

  return (
    <>
      <Head title="Forgot password" />

      <form onSubmit={submit} className="space-y-5">
        {status && (
          <div className="rounded-md border border-emerald-500/30 bg-emerald-500/10 px-3 py-2 text-xs text-emerald-600 dark:text-emerald-400">
            {status}
          </div>
        )}

        <p className="text-sm text-muted-foreground">
          Forgot your password? Enter your email and we'll send you a reset link.
        </p>

        <div className="space-y-2">
          <Label
            htmlFor="email"
            className="text-[11px] font-semibold tracking-wider text-muted-foreground uppercase"
          >
            Email address *
          </Label>
          <Input
            id="email"
            type="email"
            value={data.email}
            autoComplete="username"
            autoFocus
            required
            placeholder="name@example.com"
            onChange={(e) => setData("email", e.target.value)}
          />
          {errors.email && (
            <p className="text-xs text-destructive">{errors.email}</p>
          )}
        </div>

        <div className="flex items-center justify-end pt-2">
          <Button type="submit" disabled={processing}>
            Email password reset link
          </Button>
        </div>
      </form>
    </>
  )
}

ForgotPassword.layout = (page) => (
  <AuthLayout
    title="Reset your password"
    subtitle="We'll email you a secure reset link"
  >
    {page}
  </AuthLayout>
)