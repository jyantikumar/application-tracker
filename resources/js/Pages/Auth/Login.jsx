import { useEffect } from "react"
import { Head, Link, useForm } from "@inertiajs/react"
import AuthLayout from "@/layouts/AuthLayout"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Checkbox } from "@/components/ui/checkbox"

export default function Login({ status, canResetPassword }) {
  const { data, setData, post, processing, errors, reset } = useForm({
    email: "",
    password: "",
    remember: false,
  })

  useEffect(() => {
    return () => {
      reset("password")
    }
  }, [])

  const submit = (e) => {
    e.preventDefault()
    post(route("login"))
  }

  return (
    <>
      <Head title="Log in" />

      <form onSubmit={submit} className="space-y-5">
        {status && (
          <div className="rounded-md border border-emerald-500/30 bg-emerald-500/10 px-3 py-2 text-xs text-emerald-600 dark:text-emerald-400">
            {status}
          </div>
        )}

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
            name="email"
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
            name="password"
            value={data.password}
            autoComplete="current-password"
            required
            placeholder="••••••••"
            onChange={(e) => setData("password", e.target.value)}
          />
          {errors.password && (
            <p className="text-xs text-destructive">{errors.password}</p>
          )}
        </div>

        <div className="flex items-center justify-between text-xs">
          <label className="inline-flex cursor-pointer items-center gap-2">
            <Checkbox
              checked={data.remember}
              onCheckedChange={(v) => setData("remember", !!v)}
            />
            <span className="text-muted-foreground">Remember me</span>
          </label>

          {canResetPassword && (
            <Link
              href={route("password.request")}
              className="text-muted-foreground transition-colors hover:text-primary"
            >
              Forgot password?
            </Link>
          )}
        </div>

        <div className="flex items-center justify-between pt-2">
          <Link
            href={route("register")}
            className="text-xs text-muted-foreground transition-colors hover:text-primary"
          >
            Need an account? Register
          </Link>

          <Button type="submit" disabled={processing}>
            Log in
          </Button>
        </div>
      </form>
    </>
  )
}

Login.layout = (page) => (
  <AuthLayout
    title="Welcome back"
    subtitle="Sign in to manage your job application records"
  >
    {page}
  </AuthLayout>
)