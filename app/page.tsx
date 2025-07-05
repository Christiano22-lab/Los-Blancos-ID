import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from "@/components/ui/card"
import Link from "next/link"
import { Home, Dumbbell, ClubIcon as Football } from "lucide-react"

export default function HomePage() {
  return (
    <div className="min-h-screen bg-gradient-to-b from-blue-50 to-white">
      <div className="container max-w-4xl mx-auto px-4 py-8">
        <header className="text-center mb-12">
          <h1 className="text-4xl font-bold text-blue-600">PRO FOOTBALL TRAINING</h1>
          <p className="text-blue-400">Your personal training companion</p>
        </header>

        <div className="grid gap-8 md:grid-cols-3">
          <Card className="border-blue-200 shadow-md hover:shadow-lg transition-shadow">
            <CardHeader className="bg-blue-100 rounded-t-lg">
              <CardTitle className="flex items-center gap-2 text-blue-700">
                <Football className="h-5 w-5" />
                Football Training
              </CardTitle>
              <CardDescription>Focus on football-specific workouts</CardDescription>
            </CardHeader>
            <CardContent className="pt-4">
              <p className="text-sm text-gray-600">
                Improve your skills, agility, and football performance with specialized drills.
              </p>
            </CardContent>
            <CardFooter>
              <Link href="/program/football" className="w-full">
                <Button className="w-full bg-blue-500 hover:bg-blue-600">View Program</Button>
              </Link>
            </CardFooter>
          </Card>

          <Card className="border-blue-200 shadow-md hover:shadow-lg transition-shadow">
            <CardHeader className="bg-blue-100 rounded-t-lg">
              <CardTitle className="flex items-center gap-2 text-blue-700">
                <Football className="h-5 w-5" />
                <span className="mx-1">+</span>
                <Dumbbell className="h-5 w-5" />
                Combined Training
              </CardTitle>
              <CardDescription>Balance gym and football workouts</CardDescription>
            </CardHeader>
            <CardContent className="pt-4">
              <p className="text-sm text-gray-600">
                The complete package for athletes who want to excel both in strength and on the field.
              </p>
            </CardContent>
            <CardFooter>
              <Link href="/program/combined" className="w-full">
                <Button className="w-full bg-blue-500 hover:bg-blue-600">View Program</Button>
              </Link>
            </CardFooter>
          </Card>

          <Card className="border-blue-200 shadow-md hover:shadow-lg transition-shadow">
            <CardHeader className="bg-blue-100 rounded-t-lg">
              <CardTitle className="flex items-center gap-2 text-blue-700">
                <Dumbbell className="h-5 w-5" />
                Gym Training
              </CardTitle>
              <CardDescription>Focus on strength and conditioning</CardDescription>
            </CardHeader>
            <CardContent className="pt-4">
              <p className="text-sm text-gray-600">Build strength, endurance, and muscle with targeted gym workouts.</p>
            </CardContent>
            <CardFooter>
              <Link href="/program/gym" className="w-full">
                <Button className="w-full bg-blue-500 hover:bg-blue-600">View Program</Button>
              </Link>
            </CardFooter>
          </Card>
        </div>

        <nav className="fixed bottom-0 left-0 right-0 bg-white border-t border-blue-100 p-2">
          <div className="flex justify-center">
            <Button variant="ghost" className="text-blue-500">
              <Home className="h-6 w-6" />
            </Button>
          </div>
        </nav>
      </div>
    </div>
  )
}
