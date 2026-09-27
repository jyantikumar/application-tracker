import { Head, useForm } from "@inertiajs/react"
import AuthLayout from "@/layouts/AuthLayout"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"

export default function ResetPassword({ token, email }) {
  const { data, setData, post, processing, errors, reset } = useForm({
    token,
    email,
    password: "",
    password_confirmation: "",
  })

  const submit = (e) => {
    e.preventDefault()
    post(route("password.store"), {
      onFinish: () => reset("password", "password_confirmation"),
    })
  }

  return (
    <>
      <Head title="Reset password" />

      <form onSubmit={submit} className="space-y-5">
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
            required
            onChange={(e) => setData("email", e.target.value)}
          />
          {errors.email && (
            <p className="text-xs text-destructive">{errors.email}</p>
          )}
        </div>

        <div className="space-y-2">
          <Label
            htmlFor="password"
            className="text-[11px] font-semibold tracking-wider text-muted-foreground uppercase"
          >
            Password *
          </Label>
          <Input
            id="password"
            type="password"
            value={data.password}
            autoComplete="new-password"
            autoFocus
            required
            onChange={(e) => setData("password", e.target.value)}
          />
          {errors.password && (
            <p className="text-xs text-destructive">{errors.password}</p>
          )}
        </div>

        <div className="space-y-2">
          <Label
            htmlFor="password_confirmation"
            className="text-[11px] font-semibold tracking-wider text-muted-foreground uppercase"
          >
            Confirm password *
          </Label>
          <Input
            id="password_confirmation"
            type="password"
            value={data.password_confirmation}
            autoComplete="new-password"
            required
            onChange={(e) => setData("password_confirmation", e.target.value)}
          />
          {errors.password_confirmation && (
            <p className="text-xs text-destructive">
              {errors.password_confirmation}
            </p>
          )}
        </div>

        <div className="flex items-center justify-end pt-2">
          <Button type="submit" disabled={processing}>
            Reset password
          </Button>
        </div>
      </form>
    </>
  )
}

ResetPassword.layout = (page) => (
  <AuthLayout
    title="Set a new password"
    subtitle="Choose a strong password to secure your account"
  >
    {page}
  </AuthLayout>
)