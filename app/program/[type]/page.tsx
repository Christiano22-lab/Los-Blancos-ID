"use client"

import { useState, useEffect } from "react"
import { useParams } from "next/navigation"
import Link from "next/link"
import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from "@/components/ui/card"
import { WeeklyCalendar } from "@/components/weekly-calendar"
import { EditableWorkoutItem, type Exercise } from "@/components/editable-workout-item"
import { Home, ArrowLeft, Plus } from "lucide-react"
import { format } from "date-fns"
import { v4 as uuidv4 } from "uuid"

interface Workout {
  id: string
  title: string
  duration: string
  exercises: Exercise[]
}

interface DailyWorkouts {
  [date: string]: Workout[]
}

export default function ProgramPage() {
  const { type } = useParams()
  const [date, setDate] = useState<Date>(new Date())
  const [completedDays, setCompletedDays] = useState<string[]>([])
  const [workouts, setWorkouts] = useState<DailyWorkouts>({})

  const dateKey = format(date, "yyyy-MM-dd")
  const formattedDate = format(date, "MMMM d, yyyy")
  const isCompleted = completedDays.includes(dateKey)

  // Load data from localStorage
  useEffect(() => {
    // Load completed days
    const saved = localStorage.getItem(`completed-${type}`)
    if (saved) {
      setCompletedDays(JSON.parse(saved))
    }

    // Load workouts
    const savedWorkouts = localStorage.getItem(`workouts-${type}`)
    if (savedWorkouts) {
      setWorkouts(JSON.parse(savedWorkouts))
    } else {
      // Initialize with default workouts
      const defaultWorkouts = getDefaultWorkouts(type as string)
      setWorkouts(defaultWorkouts)
      localStorage.setItem(`workouts-${type}`, JSON.stringify(defaultWorkouts))
    }
  }, [type])

  // Save completed days to localStorage
  const saveCompletedDays = (days: string[]) => {
    setCompletedDays(days)
    localStorage.setItem(`completed-${type}`, JSON.stringify(days))
  }

  // Save workouts to localStorage
  const saveWorkouts = (updatedWorkouts: DailyWorkouts) => {
    setWorkouts(updatedWorkouts)
    localStorage.setItem(`workouts-${type}`, JSON.stringify(updatedWorkouts))
  }

  // Toggle day completion status
  const toggleCompleted = () => {
    if (isCompleted) {
      saveCompletedDays(completedDays.filter((d) => d !== dateKey))
    } else {
      saveCompletedDays([...completedDays, dateKey])
    }
  }

  // Update workout field
  const updateWorkout = (workoutId: string, field: string, value: string) => {
    const updatedWorkouts = { ...workouts }

    if (updatedWorkouts[dateKey]) {
      updatedWorkouts[dateKey] = updatedWorkouts[dateKey].map((workout) =>
        workout.id === workoutId ? { ...workout, [field]: value } : workout,
      )
    }

    saveWorkouts(updatedWorkouts)
  }

  // Add a new workout
  const addWorkout = () => {
    const updatedWorkouts = { ...workouts }

    if (!updatedWorkouts[dateKey]) {
      updatedWorkouts[dateKey] = []
    }

    updatedWorkouts[dateKey].push({
      id: uuidv4(),
      title: "New Workout",
      duration: "30 mins",
      exercises: [],
    })

    saveWorkouts(updatedWorkouts)
  }

  // Add exercise to workout
  const addExercise = (workoutId: string) => {
    const updatedWorkouts = { ...workouts }

    if (updatedWorkouts[dateKey]) {
      updatedWorkouts[dateKey] = updatedWorkouts[dateKey].map((workout) => {
        if (workout.id === workoutId) {
          return {
            ...workout,
            exercises: [...workout.exercises, { id: uuidv4(), name: "New Exercise" }],
          }
        }
        return workout
      })
    }

    saveWorkouts(updatedWorkouts)
  }

  // Update exercise
  const updateExercise = (workoutId: string, exerciseId: string, name: string) => {
    const updatedWorkouts = { ...workouts }

    if (updatedWorkouts[dateKey]) {
      updatedWorkouts[dateKey] = updatedWorkouts[dateKey].map((workout) => {
        if (workout.id === workoutId) {
          return {
            ...workout,
            exercises: workout.exercises.map((exercise) =>
              exercise.id === exerciseId ? { ...exercise, name } : exercise,
            ),
          }
        }
        return workout
      })
    }

    saveWorkouts(updatedWorkouts)
  }

  // Delete exercise
  const deleteExercise = (workoutId: string, exerciseId: string) => {
    const updatedWorkouts = { ...workouts }

    if (updatedWorkouts[dateKey]) {
      updatedWorkouts[dateKey] = updatedWorkouts[dateKey].map((workout) => {
        if (workout.id === workoutId) {
          return {
            ...workout,
            exercises: workout.exercises.filter((exercise) => exercise.id !== exerciseId),
          }
        }
        return workout
      })
    }

    saveWorkouts(updatedWorkouts)
  }

  const programTitle =
    {
      football: "Football Training",
      gym: "Gym Training",
      combined: "Combined Training",
    }[type as string] || "Training Program"

  // Get current day's workouts
  const currentWorkouts = workouts[dateKey] || []

  return (
    <div className="min-h-screen bg-gray-900 text-white">
      <div className="container max-w-4xl mx-auto px-4 py-8">
        <header className="flex items-center mb-6">
          <Link href="/">
            <Button variant="ghost" size="icon" className="mr-2 text-white hover:bg-gray-800 bg-gray-700">
              <ArrowLeft className="h-5 w-5" />
            </Button>
          </Link>
          <h1 className="text-2xl font-bold text-blue-400">{programTitle}</h1>
        </header>

        <div className="mb-6">
          <WeeklyCalendar selectedDate={date} onSelectDate={(date) => setDate(date)} completedDays={completedDays} />
        </div>

        <Card className="border-gray-700 bg-gray-800 shadow-md mb-6">
          <CardHeader className="bg-gray-800 rounded-t-lg border-b border-gray-700">
            <CardTitle className="text-white">{formattedDate}</CardTitle>
            <CardDescription className="text-gray-400">
              {isCompleted ? "Completed" : "Workout plan for today"}
            </CardDescription>
          </CardHeader>
          <CardContent className="pt-4 space-y-4">
            {currentWorkouts.length > 0 ? (
              currentWorkouts.map((workout) => (
                <EditableWorkoutItem
                  key={workout.id}
                  id={workout.id}
                  title={workout.title}
                  duration={workout.duration}
                  exercises={workout.exercises}
                  onUpdate={updateWorkout}
                  onExerciseAdd={addExercise}
                  onExerciseUpdate={updateExercise}
                  onExerciseDelete={deleteExercise}
                />
              ))
            ) : (
              <p className="text-gray-400 text-center py-4">No workouts scheduled. Add one below.</p>
            )}

            <Button
              variant="outline"
              className="w-full border-blue-700 text-blue-300 hover:bg-gray-700 mt-2"
              onClick={addWorkout}
            >
              <Plus className="h-4 w-4 mr-2" />
              Add Workout
            </Button>
          </CardContent>
          <CardFooter className="flex justify-between">
            <Link href={`/program/${type}/${dateKey}`}>
              <Button variant="outline" className="border-blue-700 bg-gray-700 text-white hover:bg-gray-600">
                View Notes
              </Button>
            </Link>
            <Button
              onClick={toggleCompleted}
              className={isCompleted ? "bg-green-600 hover:bg-green-700" : "bg-blue-600 hover:bg-blue-700"}
            >
              {isCompleted ? "Completed ✓" : "Mark as Complete"}
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

// Helper function to generate default workouts based on program type
function getDefaultWorkouts(type: string): DailyWorkouts {
  const today = new Date()
  const dateKey = format(today, "yyyy-MM-dd")

  if (type === "football") {
    return {
      [dateKey]: [
        {
          id: uuidv4(),
          title: "Agility Drills",
          duration: "30 mins",
          exercises: [
            { id: uuidv4(), name: "Ladder drills" },
            { id: uuidv4(), name: "Cone weaving" },
            { id: uuidv4(), name: "Quick direction changes" },
          ],
        },
        {
          id: uuidv4(),
          title: "Passing Practice",
          duration: "45 mins",
          exercises: [
            { id: uuidv4(), name: "Short passing" },
            { id: uuidv4(), name: "Long passing" },
            { id: uuidv4(), name: "Through balls" },
          ],
        },
      ],
    }
  } else if (type === "gym") {
    return {
      [dateKey]: [
        {
          id: uuidv4(),
          title: "Chest & Triceps",
          duration: "60 mins",
          exercises: [
            { id: uuidv4(), name: "Bench press" },
            { id: uuidv4(), name: "Incline dumbbell press" },
            { id: uuidv4(), name: "Tricep pushdowns" },
          ],
        },
        {
          id: uuidv4(),
          title: "Core Workout",
          duration: "15 mins",
          exercises: [
            { id: uuidv4(), name: "Planks" },
            { id: uuidv4(), name: "Russian twists" },
            { id: uuidv4(), name: "Leg raises" },
          ],
        },
      ],
    }
  } else if (type === "combined") {
    return {
      [dateKey]: [
        {
          id: uuidv4(),
          title: "Upper Body Strength",
          duration: "45 mins",
          exercises: [
            { id: uuidv4(), name: "Pull-ups" },
            { id: uuidv4(), name: "Push-ups" },
            { id: uuidv4(), name: "Shoulder press" },
          ],
        },
        {
          id: uuidv4(),
          title: "Football Skills",
          duration: "30 mins",
          exercises: [
            { id: uuidv4(), name: "Dribbling drills" },
            { id: uuidv4(), name: "Shooting practice" },
            { id: uuidv4(), name: "1v1 skills" },
          ],
        },
      ],
    }
  }

  return {
    [dateKey]: [
      {
        id: uuidv4(),
        title: "Custom Workout",
        duration: "45 mins",
        exercises: [
          { id: uuidv4(), name: "Exercise 1" },
          { id: uuidv4(), name: "Exercise 2" },
          { id: uuidv4(), name: "Exercise 3" },
        ],
      },
    ],
  }
}
