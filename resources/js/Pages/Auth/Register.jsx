import { useEffect } from "react"
import { Head, Link, useForm } from "@inertiajs/react"
import AuthLayout from "@/layouts/AuthLayout"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"

export default function Register() {
  const { data, setData, post, processing, errors, reset } = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
  })

  useEffect(() => {
    return () => {
      reset("password", "password_confirmation")
    }
  }, [])

  const submit = (e) => {
    e.preventDefault()
    post(route("register"))
  }

  return (
    <>
      <Head title="Register" />

      <form onSubmit={submit} className="space-y-5">
        <div className="space-y-2">
          <Label
            htmlFor="name"
            className="text-[11px] font-semibold tracking-wider text-muted-foreground uppercase"
          >
            Full name *
          </Label>
          <Input
            id="name"
            value={data.name}
            autoComplete="name"
            autoFocus
            required
            placeholder="e.g. Juan Dela Cruz"
            onChange={(e) => setData("name", e.target.value)}
          />
          {errors.name && (
            <p className="text-xs text-destructive">{errors.name}</p>
          )}
        </div>

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
            value={data.password}
            autoComplete="new-password"
            required
            placeholder="••••••••"
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
            placeholder="••••••••"
            onChange={(e) => setData("password_confirmation", e.target.value)}
          />
          {errors.password_confirmation && (
            <p className="text-xs text-destructive">
              {errors.password_confirmation}
            </p>
          )}
        </div>

        <div className="flex items-center justify-between pt-2">
          <Link
            href={route("login")}
            className="text-xs text-muted-foreground transition-colors hover:text-primary"
          >
            Already registered? Log in
          </Link>

          <Button type="submit" disabled={processing}>
            Register
          </Button>
        </div>
      </form>
    </>
  )
}

Register.layout = (page) => (
  <AuthLayout
    title="Create an account"
    subtitle="Start tracking your job applications with user-level security"
  >
    {page}
  </AuthLayout>
)