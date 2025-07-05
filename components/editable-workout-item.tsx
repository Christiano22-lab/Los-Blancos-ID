"use client"

import { useState } from "react"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Clock, ChevronDown, ChevronUp, Plus, Trash2 } from "lucide-react"

export interface Exercise {
  id: string
  name: string
}

export interface WorkoutItemProps {
  id: string
  title: string
  duration: string
  exercises: Exercise[]
  onUpdate: (id: string, field: string, value: string) => void
  onExerciseAdd: (workoutId: string) => void
  onExerciseUpdate: (workoutId: string, exerciseId: string, name: string) => void
  onExerciseDelete: (workoutId: string, exerciseId: string) => void
}

export function EditableWorkoutItem({
  id,
  title,
  duration,
  exercises,
  onUpdate,
  onExerciseAdd,
  onExerciseUpdate,
  onExerciseDelete,
}: WorkoutItemProps) {
  const [expanded, setExpanded] = useState(false)

  return (
    <div className="space-y-2 p-3 bg-gray-700 rounded-md">
      <div className="flex justify-between items-center">
        <Input
          value={title}
          onChange={(e) => onUpdate(id, "title", e.target.value)}
          className="font-medium text-white bg-gray-600 border-gray-500 focus:border-blue-500"
          placeholder="Workout title"
        />
        <div className="flex items-center gap-2 ml-2">
          <Clock className="h-4 w-4 text-blue-300 flex-shrink-0" />
          <Input
            value={duration}
            onChange={(e) => onUpdate(id, "duration", e.target.value)}
            className="w-20 h-8 text-sm bg-gray-600 border-gray-500 text-white focus:border-blue-500"
            placeholder="Duration"
          />
        </div>
      </div>

      <Button
        variant="ghost"
        size="sm"
        className="w-full justify-between text-blue-300 hover:bg-gray-600"
        onClick={() => setExpanded(!expanded)}
      >
        <span>Exercise Details</span>
        {expanded ? <ChevronUp className="h-4 w-4" /> : <ChevronDown className="h-4 w-4" />}
      </Button>

      {expanded && (
        <div className="pl-2 space-y-2 border-l-2 border-gray-600 mt-2">
          {exercises.map((exercise) => (
            <div key={exercise.id} className="flex items-center gap-2">
              <Input
                value={exercise.name}
                onChange={(e) => onExerciseUpdate(id, exercise.id, e.target.value)}
                className="text-sm bg-gray-600 border-gray-500 text-white focus:border-blue-500"
                placeholder="Exercise name"
              />
              <Button
                variant="ghost"
                size="icon"
                className="h-8 w-8 text-red-400 hover:text-red-300 hover:bg-gray-600"
                onClick={() => onExerciseDelete(id, exercise.id)}
              >
                <Trash2 className="h-4 w-4" />
              </Button>
            </div>
          ))}

          <Button
            variant="ghost"
            size="sm"
            className="flex items-center gap-1 text-green-400 hover:text-green-300 hover:bg-gray-600"
            onClick={() => onExerciseAdd(id)}
          >
            <Plus className="h-4 w-4" />
            <span>Add Exercise</span>
          </Button>
        </div>
      )}
    </div>
  )
}
