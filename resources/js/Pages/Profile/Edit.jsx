import { useState } from "react"
import { Head, useForm, usePage } from "@inertiajs/react"
import { Eye, EyeOff, CheckCircle2, XCircle } from "lucide-react"
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
import { Badge } from "@/components/ui/badge"
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

  // Password visibility states
  const [showCurrentPassword, setShowCurrentPassword] = useState(false)
  const [showNewPassword, setShowNewPassword] = useState(false)
  const [showConfirmPassword, setShowConfirmPassword] = useState(false)

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

  // Real-time password criteria
  const isMinLength = passwordForm.data.password.length >= 8
  const isMatching =
    passwordForm.data.password_confirmation.length > 0 &&
    passwordForm.data.password === passwordForm.data.password_confirmation

  return (
    <>
      <Head title="Profile Information" />

      <div className="mx-auto flex max-w-4xl flex-col space-y-6">
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

        <Tabs
          value={tab}
          onValueChange={setTab}
          className="flex w-full flex-col space-y-6"
        >
          <div>
            <TabsList className="inline-flex h-10 w-full items-center justify-start rounded-md bg-muted p-1 text-muted-foreground">
              <TabsTrigger value="profile">Profile</TabsTrigger>
              <TabsTrigger value="security">Security</TabsTrigger>
              <TabsTrigger value="danger">Delete Account</TabsTrigger>
            </TabsList>
          </div>

          <TabsContent value="profile" className="mt-0 w-full">
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

          <TabsContent value="security" className="mt-0 w-full">
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
                    <div className="relative">
                      <Input
                        id="current_password"
                        type={showCurrentPassword ? "text" : "password"}
                        value={passwordForm.data.current_password}
                        onChange={(e) =>
                          passwordForm.setData("current_password", e.target.value)
                        }
                        autoComplete="current-password"
                        required
                        className="pr-10"
                      />
                      <button
                        type="button"
                        onClick={() =>
                          setShowCurrentPassword(!showCurrentPassword)
                        }
                        className="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                      >
                        {showCurrentPassword ? (
                          <EyeOff className="h-4 w-4" />
                        ) : (
                          <Eye className="h-4 w-4" />
                        )}
                      </button>
                    </div>
                    {passwordForm.errors.current_password && (
                      <p className="text-xs text-destructive">
                        {passwordForm.errors.current_password}
                      </p>
                    )}
                  </div>

                  <div className="space-y-2">
                    <div className="flex items-center justify-between">
                      <Label htmlFor="password">New password *</Label>
                      {passwordForm.data.password && (
                        <span className="flex items-center gap-1 text-xs">
                          {isMinLength ? (
                            <span className="flex items-center text-emerald-500">
                              <CheckCircle2 className="mr-1 h-3 w-3" /> Min 8 chars
                            </span>
                          ) : (
                            <span className="flex items-center text-muted-foreground">
                              <XCircle className="mr-1 h-3 w-3" /> Min 8 chars
                            </span>
                          )}
                        </span>
                      )}
                    </div>
                    <div className="relative">
                      <Input
                        id="password"
                        type={showNewPassword ? "text" : "password"}
                        value={passwordForm.data.password}
                        onChange={(e) =>
                          passwordForm.setData("password", e.target.value)
                        }
                        autoComplete="new-password"
                        required
                        className="pr-10"
                      />
                      <button
                        type="button"
                        onClick={() => setShowNewPassword(!showNewPassword)}
                        className="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                      >
                        {showNewPassword ? (
                          <EyeOff className="h-4 w-4" />
                        ) : (
                          <Eye className="h-4 w-4" />
                        )}
                      </button>
                    </div>
                    {passwordForm.errors.password && (
                      <p className="text-xs text-destructive">
                        {passwordForm.errors.password}
                      </p>
                    )}
                  </div>

                  <div className="space-y-2">
                    <div className="flex items-center justify-between">
                      <Label htmlFor="password_confirmation">
                        Confirm password *
                      </Label>
                      {passwordForm.data.password_confirmation && (
                        <Badge
                          variant="outline"
                          className={
                            isMatching
                              ? "border-emerald-500/30 bg-emerald-500/10 text-emerald-500"
                              : "border-destructive/30 bg-destructive/10 text-destructive"
                          }
                        >
                          {isMatching ? (
                            <span className="flex items-center gap-1 text-[10px]">
                              <CheckCircle2 className="h-3 w-3" /> Passwords match
                            </span>
                          ) : (
                            <span className="flex items-center gap-1 text-[10px]">
                              <XCircle className="h-3 w-3" /> Passwords do not match
                            </span>
                          )}
                        </Badge>
                      )}
                    </div>
                    <div className="relative">
                      <Input
                        id="password_confirmation"
                        type={showConfirmPassword ? "text" : "password"}
                        value={passwordForm.data.password_confirmation}
                        onChange={(e) =>
                          passwordForm.setData(
                            "password_confirmation",
                            e.target.value
                          )
                        }
                        autoComplete="new-password"
                        required
                        className={`pr-10 ${
                          passwordForm.data.password_confirmation
                            ? isMatching
                              ? "border-emerald-500 focus-visible:ring-emerald-500"
                              : "border-destructive focus-visible:ring-destructive"
                            : ""
                        }`}
                      />
                      <button
                        type="button"
                        onClick={() =>
                          setShowConfirmPassword(!showConfirmPassword)
                        }
                        className="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                      >
                        {showConfirmPassword ? (
                          <EyeOff className="h-4 w-4" />
                        ) : (
                          <Eye className="h-4 w-4" />
                        )}
                      </button>
                    </div>
                  </div>

                  <Button
                    type="submit"
                    disabled={
                      passwordForm.processing || !isMatching || !isMinLength
                    }
                  >
                    Save password
                  </Button>
                </form>
              </CardContent>
            </Card>
          </TabsContent>

          <TabsContent value="danger" className="mt-0 w-full">
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
    </>
  )
}

Edit.layoutProps = {
  active: "profile",
  title: "Profile",
  breadcrumbs: [{ label: "Profile" }],
}