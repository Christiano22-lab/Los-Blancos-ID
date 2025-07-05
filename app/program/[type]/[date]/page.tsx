"use client"

import { useState, useEffect } from "react"
import { useParams, useRouter } from "next/navigation"
import Link from "next/link"
import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from "@/components/ui/card"
import { Textarea } from "@/components/ui/textarea"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Home, ArrowLeft, Clock, Save, CheckCircle } from "lucide-react"
import { format, parse } from "date-fns"

type Exercise = {
  id: string
  name: string
  duration: string
  notes: string
  completed: boolean
}

type WorkoutDay = {
  notes: string
  exercises: Exercise[]
  completed: boolean
}

export default function WorkoutDetailPage() {
  const { type, date } = useParams()
  const router = useRouter()
  const [workout, setWorkout] = useState<WorkoutDay>({
    notes: "",
    exercises: [],
    completed: false,
  })

  const formattedDate = format(parse(date as string, "yyyy-MM-dd", new Date()), "MMMM d, yyyy")

  // Load workout data from localStorage
  useEffect(() => {
    const savedWorkout = localStorage.getItem(`workout-${type}-${date}`)
    if (savedWorkout) {
      setWorkout(JSON.parse(savedWorkout))
    } else {
      // Initialize with exercises from workouts
      const savedWorkouts = localStorage.getItem(`workouts-${type}`)
      if (savedWorkouts) {
        const workouts = JSON.parse(savedWorkouts)
        if (workouts[date as string]) {
          // Convert workout format to exercise format
          const exercises = workouts[date as string].flatMap((workout) =>
            workout.exercises.map((exercise) => ({
              id: exercise.id,
              name: exercise.name,
              duration: "15 mins",
              notes: "",
              completed: false,
            })),
          )

          setWorkout({
            notes: "",
            exercises,
            completed: false,
          })
        } else {
          // Initialize with default exercises
          const defaultExercises = getDefaultExercises(type as string, date as string)
          setWorkout({
            notes: "",
            exercises: defaultExercises,
            completed: false,
          })
        }
      } else {
        // Initialize with default exercises
        const defaultExercises = getDefaultExercises(type as string, date as string)
        setWorkout({
          notes: "",
          exercises: defaultExercises,
          completed: false,
        })
      }
    }

    // Check if day is marked as completed
    const savedCompleted = localStorage.getItem(`completed-${type}`)
    if (savedCompleted) {
      const completedDays = JSON.parse(savedCompleted)
      if (completedDays.includes(date)) {
        setWorkout((prev) => ({ ...prev, completed: true }))
      }
    }
  }, [type, date])

  // Save workout data
  const saveWorkout = () => {
    localStorage.setItem(`workout-${type}-${date}`, JSON.stringify(workout))

    // Update completed days if workout is marked as completed
    if (workout.completed) {
      const savedCompleted = localStorage.getItem(`completed-${type}`)
      const completedDays = savedCompleted ? JSON.parse(savedCompleted) : []
      if (!completedDays.includes(date)) {
        completedDays.push(date)
        localStorage.setItem(`completed-${type}`, JSON.stringify(completedDays))
      }
    }
  }

  // Toggle workout completion status
  const toggleCompleted = () => {
    setWorkout((prev) => {
      const updated = { ...prev, completed: !prev.completed }
      localStorage.setItem(`workout-${type}-${date}`, JSON.stringify(updated))

      // Update completed days list
      const savedCompleted = localStorage.getItem(`completed-${type}`)
      let completedDays = savedCompleted ? JSON.parse(savedCompleted) : []

      if (updated.completed) {
        if (!completedDays.includes(date)) {
          completedDays.push(date)
        }
      } else {
        completedDays = completedDays.filter((d: string) => d !== date)
      }

      localStorage.setItem(`completed-${type}`, JSON.stringify(completedDays))
      return updated
    })
  }

  // Update exercise details
  const updateExercise = (id: string, field: keyof Exercise, value: string | boolean) => {
    setWorkout((prev) => {
      const updatedExercises = prev.exercises.map((ex) => (ex.id === id ? { ...ex, [field]: value } : ex))
      return { ...prev, exercises: updatedExercises }
    })
  }

  // Update general notes
  const updateNotes = (notes: string) => {
    setWorkout((prev) => ({ ...prev, notes }))
  }

  const programTitle =
    {
      football: "Football Training",
      gym: "Gym Training",
      combined: "Combined Training",
    }[type as string] || "Training Program"

  return (
    <div className="min-h-screen bg-gray-900 text-white">
      <div className="container max-w-4xl mx-auto px-4 py-8 pb-20">
        <header className="flex items-center mb-6">
          <Link href={`/program/${type}`}>
            <Button variant="ghost" size="icon" className="mr-2 text-white hover:bg-gray-800 bg-gray-700">
              <ArrowLeft className="h-5 w-5" />
            </Button>
          </Link>
          <div>
            <h1 className="text-2xl font-bold text-blue-400">{programTitle}</h1>
            <p className="text-sm text-gray-400">{formattedDate}</p>
          </div>
        </header>

        <Card className="border-gray-700 bg-gray-800 shadow-md mb-6">
          <CardHeader className="bg-gray-800 rounded-t-lg border-b border-gray-700">
            <CardTitle className="text-white">Workout Notes</CardTitle>
            <CardDescription className="text-gray-400">
              {workout.completed ? "Completed ✓" : "Track your progress"}
            </CardDescription>
          </CardHeader>
          <CardContent className="pt-4 space-y-4">
            {workout.exercises.map((exercise) => (
              <div key={exercise.id} className="space-y-2 p-3 bg-gray-700 rounded-md">
                <div className="flex justify-between items-center">
                  <Input
                    value={exercise.name}
                    onChange={(e) => updateExercise(exercise.id, "name", e.target.value)}
                    className="font-medium text-white bg-gray-600 border-gray-500 focus:border-blue-500"
                    placeholder="Exercise name"
                  />
                  <div className="flex items-center gap-2 ml-2">
                    <Clock className="h-4 w-4 text-blue-300 flex-shrink-0" />
                    <Input
                      value={exercise.duration}
                      onChange={(e) => updateExercise(exercise.id, "duration", e.target.value)}
                      className="w-20 h-8 text-sm bg-gray-600 border-gray-500 text-white focus:border-blue-500"
                      placeholder="Duration"
                    />
                  </div>
                </div>
                <Textarea
                  placeholder="Add notes about this exercise..."
                  value={exercise.notes}
                  onChange={(e) => updateExercise(exercise.id, "notes", e.target.value)}
                  className="min-h-[80px] text-sm bg-gray-600 border-gray-500 text-white focus:border-blue-500"
                />
                <div className="flex items-center">
                  <Button
                    variant="ghost"
                    size="sm"
                    className={
                      exercise.completed
                        ? "text-green-400 hover:text-green-300 hover:bg-gray-600"
                        : "text-gray-400 hover:bg-gray-600"
                    }
                    onClick={() => updateExercise(exercise.id, "completed", !exercise.completed)}
                  >
                    <CheckCircle className="h-5 w-5 mr-1" />
                    {exercise.completed ? "Completed" : "Mark complete"}
                  </Button>
                </div>
              </div>
            ))}

            <div className="space-y-2 mt-4">
              <Label htmlFor="workout-notes" className="font-medium text-white">
                General Notes
              </Label>
              <Textarea
                id="workout-notes"
                placeholder="Add general notes about today's workout..."
                value={workout.notes}
                onChange={(e) => updateNotes(e.target.value)}
                className="min-h-[100px] bg-gray-600 border-gray-500 text-white focus:border-blue-500"
              />
            </div>
          </CardContent>
          <CardFooter className="flex justify-between">
            <Button
              variant="outline"
              className="border-blue-700 bg-gray-700 text-white hover:bg-gray-600 flex items-center gap-2"
              onClick={saveWorkout}
            >
              <Save className="h-4 w-4" />
              Save
            </Button>
            <Button
              onClick={toggleCompleted}
              className={workout.completed ? "bg-green-600 hover:bg-green-700" : "bg-blue-600 hover:bg-blue-700"}
            >
              {workout.completed ? "Completed ✓" : "Mark as Complete"}
            </Button>
          </CardFooter>
        </Card>

        <nav className="fixed bottom-0 left-0 right-0 bg-gray-800 border-t border-gray-700 p-2">
          <div className="flex justify-center">
            <Link href="/">
              <Button variant="ghost" className="text-blue-400 hover:bg-gray-700 bg-gray-800">
                <Home className="h-6 w-6" />
              </Button>
            </Link>
          </div>
        </nav>
      </div>
    </div>
  )
}

// Helper function to generate default exercises based on program type and date
function getDefaultExercises(type: string, dateStr: string): Exercise[] {
  const date = parse(dateStr, "yyyy-MM-dd", new Date())
  const day = date.getDay() // 0 = Sunday, 1 = Monday, etc.

  if (type === "football") {
    switch (day) {
      case 1: // Monday
        return [
          { id: "f1", name: "Agility Drills", duration: "30 mins", notes: "", completed: false },
          { id: "f2", name: "Passing Practice", duration: "45 mins", notes: "", completed: false },
          { id: "f3", name: "Sprint Training", duration: "20 mins", notes: "", completed: false },
        ]
      case 3: // Wednesday
        return [
          { id: "f4", name: "Ball Control", duration: "40 mins", notes: "", completed: false },
          { id: "f5", name: "Tactical Training", duration: "45 mins", notes: "", completed: false },
          { id: "f6", name: "Recovery Exercises", duration: "15 mins", notes: "", completed: false },
        ]
      case 5: // Friday
        return [
          { id: "f7", name: "Shooting Practice", duration: "30 mins", notes: "", completed: false },
          { id: "f8", name: "Small-sided Games", duration: "45 mins", notes: "", completed: false },
          { id: "f9", name: "Cooldown", duration: "15 mins", notes: "", completed: false },
        ]
      default:
        return [{ id: "f10", name: "Rest day or light recovery", duration: "30 mins", notes: "", completed: false }]
    }
  } else if (type === "gym") {
    switch (day) {
      case 1: // Monday
        return [
          { id: "g1", name: "Chest & Triceps", duration: "60 mins", notes: "", completed: false },
          { id: "g2", name: "Core Workout", duration: "15 mins", notes: "", completed: false },
        ]
      case 2: // Tuesday
        return [
          { id: "g3", name: "Back & Biceps", duration: "60 mins", notes: "", completed: false },
          { id: "g4", name: "HIIT Cardio", duration: "20 mins", notes: "", completed: false },
        ]
      case 4: // Thursday
        return [
          { id: "g5", name: "Legs & Shoulders", duration: "60 mins", notes: "", completed: false },
          { id: "g6", name: "Core Workout", duration: "15 mins", notes: "", completed: false },
        ]
      case 6: // Saturday
        return [
          { id: "g7", name: "Full Body Workout", duration: "45 mins", notes: "", completed: false },
          { id: "g8", name: "Stretching", duration: "15 mins", notes: "", completed: false },
        ]
      default:
        return [{ id: "g9", name: "Rest day or light recovery", duration: "30 mins", notes: "", completed: false }]
    }
  } else if (type === "combined") {
    switch (day) {
      case 1: // Monday
        return [
          { id: "c1", name: "Upper Body Strength", duration: "45 mins", notes: "", completed: false },
          { id: "c2", name: "Football Skills", duration: "30 mins", notes: "", completed: false },
        ]
      case 2: // Tuesday
        return [
          { id: "c3", name: "Sprint Training", duration: "30 mins", notes: "", completed: false },
          { id: "c4", name: "Agility Drills", duration: "30 mins", notes: "", completed: false },
        ]
      case 3: // Wednesday
        return [
          { id: "c5", name: "Active Recovery", duration: "30 mins", notes: "", completed: false },
          { id: "c6", name: "Mobility Work", duration: "20 mins", notes: "", completed: false },
        ]
      case 4: // Thursday
        return [
          { id: "c7", name: "Lower Body Strength", duration: "45 mins", notes: "", completed: false },
          { id: "c8", name: "Ball Control", duration: "30 mins", notes: "", completed: false },
        ]
      case 5: // Friday
        return [
          { id: "c9", name: "HIIT Training", duration: "30 mins", notes: "", completed: false },
          { id: "c10", name: "Tactical Drills", duration: "30 mins", notes: "", completed: false },
        ]
      case 6: // Saturday
        return [
          { id: "c11", name: "Match Play", duration: "90 mins", notes: "", completed: false },
          { id: "c12", name: "Cooldown", duration: "15 mins", notes: "", completed: false },
        ]
      default:
        return [{ id: "c13", name: "Complete rest day", duration: "0 mins", notes: "", completed: false }]
    }
  }

  return [{ id: "default", name: "Custom workout", duration: "45 mins", notes: "", completed: false }]
}
