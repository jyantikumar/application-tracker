import { useState } from "react"
import { Head, useForm, usePage } from "@inertiajs/react"
import AppLayout from "@/layouts/AppLayout"
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogTrigger,
} from "@/components/ui/alert-dialog"
import { Alert, AlertDescription } from "@/components/ui/alert"
import { Button } from "@/components/ui/button"
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "@/components/ui/card"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs"

export default function Edit({ mustVerifyEmail, status }) {
  const { auth } = usePage().props
  const user = auth?.user ?? {}
  const [tab, setTab] = useState("profile")

  const profileForm = useForm({
    name: user.name || "",
    email: user.email || "",
  })

  const passwordForm = useForm({
    current_password: "",
    password: "",
    password_confirmation: "",
  })

  const deleteForm = useForm({
    password: "",
  })

  return (
    <AppLayout
      active="profile"
      title="Profile"
      breadcrumbs={[{ label: "Profile" }]}
    >
      <Head title="Profile Information" />

      <div className="mx-auto max-w-4xl space-y-6">
        <div>
          <h1 className="font-heading text-2xl font-semibold tracking-tight">
            Account & profile
          </h1>
          <p className="mt-1 text-sm text-muted-foreground">
            Manage your personal details, password, and account security.
          </p>
        </div>

        {(status === "profile-updated" || status === "password-updated") && (
          <Alert className="border-primary/30 bg-primary/10 text-primary">
            <AlertDescription>Saved successfully.</AlertDescription>
          </Alert>
        )}

        <Tabs value={tab} onValueChange={setTab} className="gap-6">
          <TabsList className="grid w-full grid-cols-3 sm:w-auto sm:inline-grid">
            <TabsTrigger value="profile">Profile</TabsTrigger>
            <TabsTrigger value="security">Security</TabsTrigger>
            <TabsTrigger value="danger">Danger</TabsTrigger>
          </TabsList>

          <TabsContent value="profile">
            <Card>
              <CardHeader>
                <CardTitle>Profile information</CardTitle>
                <CardDescription>
                  Update your account name and email address.
                </CardDescription>
              </CardHeader>
              <CardContent>
                <form
                  className="space-y-4"
                  onSubmit={(e) => {
                    e.preventDefault()
                    profileForm.patch(route("profile.update"), {
                      preserveScroll: true,
                    })
                  }}
                >
                  <div className="space-y-2">
                    <Label htmlFor="name">Name *</Label>
                    <Input
                      id="name"
                      value={profileForm.data.name}
                      onChange={(e) =>
                        profileForm.setData("name", e.target.value)
                      }
                      required
                      autoComplete="name"
                    />
                    {profileForm.errors.name && (
                      <p className="text-xs text-destructive">
                        {profileForm.errors.name}
                      </p>
                    )}
                  </div>
                  <div className="space-y-2">
                    <Label htmlFor="email">Email *</Label>
                    <Input
                      id="email"
                      type="email"
                      value={profileForm.data.email}
                      onChange={(e) =>
                        profileForm.setData("email", e.target.value)
                      }
                      required
                      autoComplete="username"
                    />
                    {profileForm.errors.email && (
                      <p className="text-xs text-destructive">
                        {profileForm.errors.email}
                      </p>
                    )}
                    {mustVerifyEmail && user.email_verified_at === null && (
                      <p className="text-xs text-amber-600 dark:text-amber-400">
                        Your email address is unverified.
                      </p>
                    )}
                  </div>
                  <Button type="submit" disabled={profileForm.processing}>
                    Save profile
                  </Button>
                </form>
              </CardContent>
            </Card>
          </TabsContent>

          <TabsContent value="security">
            <Card>
              <CardHeader>
                <CardTitle>Update password</CardTitle>
                <CardDescription>
                  Use a long, random password to keep your account secure.
                </CardDescription>
              </CardHeader>
              <CardContent>
                <form
                  className="space-y-4"
                  onSubmit={(e) => {
                    e.preventDefault()
                    passwordForm.put(route("password.update"), {
                      preserveScroll: true,
                      onSuccess: () => passwordForm.reset(),
                    })
                  }}
                >
                  <div className="space-y-2">
                    <Label htmlFor="current_password">Current password *</Label>
                    <Input
                      id="current_password"
                      type="password"
                      value={passwordForm.data.current_password}
                      onChange={(e) =>
                        passwordForm.setData("current_password", e.target.value)
                      }
                      autoComplete="current-password"
                      required
                    />
                    {passwordForm.errors.current_password && (
                      <p className="text-xs text-destructive">
                        {passwordForm.errors.current_password}
                      </p>
                    )}
                  </div>
                  <div className="space-y-2">
                    <Label htmlFor="password">New password *</Label>
                    <Input
                      id="password"
                      type="password"
                      value={passwordForm.data.password}
                      onChange={(e) =>
                        passwordForm.setData("password", e.target.value)
                      }
                      autoComplete="new-password"
                      required
                    />
                    {passwordForm.errors.password && (
                      <p className="text-xs text-destructive">
                        {passwordForm.errors.password}
                      </p>
                    )}
                  </div>
                  <div className="space-y-2">
                    <Label htmlFor="password_confirmation">
                      Confirm password *
                    </Label>
                    <Input
                      id="password_confirmation"
                      type="password"
                      value={passwordForm.data.password_confirmation}
                      onChange={(e) =>
                        passwordForm.setData(
                          "password_confirmation",
                          e.target.value
                        )
                      }
                      autoComplete="new-password"
                      required
                    />
                  </div>
                  <Button type="submit" disabled={passwordForm.processing}>
                    Save password
                  </Button>
                </form>
              </CardContent>
            </Card>
          </TabsContent>

          <TabsContent value="danger">
            <Card className="border-destructive/30">
              <CardHeader>
                <CardTitle className="text-destructive">Delete account</CardTitle>
                <CardDescription>
                  Permanently delete your account and all associated data.
                </CardDescription>
              </CardHeader>
              <CardContent>
                <AlertDialog>
                  <AlertDialogTrigger asChild>
                    <Button variant="destructive">Delete account</Button>
                  </AlertDialogTrigger>
                  <AlertDialogContent>
                    <AlertDialogHeader>
                      <AlertDialogTitle>Are you sure?</AlertDialogTitle>
                      <AlertDialogDescription>
                        Enter your password to confirm permanent account deletion.
                      </AlertDialogDescription>
                    </AlertDialogHeader>
                    <div className="space-y-2 py-2">
                      <Label htmlFor="delete_password">Password</Label>
                      <Input
                        id="delete_password"
                        type="password"
                        value={deleteForm.data.password}
                        onChange={(e) =>
                          deleteForm.setData("password", e.target.value)
                        }
                        required
                      />
                      {deleteForm.errors.password && (
                        <p className="text-xs text-destructive">
                          {deleteForm.errors.password}
                        </p>
                      )}
                    </div>
                    <AlertDialogFooter>
                      <AlertDialogCancel>Cancel</AlertDialogCancel>
                      <AlertDialogAction
                        className="bg-destructive text-white hover:bg-destructive/90"
                        onClick={(e) => {
                          e.preventDefault()
                          deleteForm.delete(route("profile.destroy"))
                        }}
                      >
                        Delete account
                      </AlertDialogAction>
                    </AlertDialogFooter>
                  </AlertDialogContent>
                </AlertDialog>
              </CardContent>
            </Card>
          </TabsContent>
        </Tabs>
      </div>
    </AppLayout>
  )
}
