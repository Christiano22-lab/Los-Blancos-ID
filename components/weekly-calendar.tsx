"use client"

import { useState } from "react"
import { Button } from "@/components/ui/button"
import { ChevronLeft, ChevronRight } from "lucide-react"
import { format, addDays, startOfWeek, isSameDay } from "date-fns"

interface WeeklyCalendarProps {
  selectedDate: Date
  onSelectDate: (date: Date) => void
  completedDays: string[]
}

export function WeeklyCalendar({ selectedDate, onSelectDate, completedDays }: WeeklyCalendarProps) {
  const [weekStart, setWeekStart] = useState(startOfWeek(selectedDate, { weekStartsOn: 1 }))

  // Generate the week days
  const weekDays = Array.from({ length: 7 }).map((_, i) => {
    const date = addDays(weekStart, i)
    const dateKey = format(date, "yyyy-MM-dd")
    const isSelected = isSameDay(date, selectedDate)
    const isCompleted = completedDays.includes(dateKey)

    return { date, dateKey, isSelected, isCompleted }
  })

  // Navigate to previous week
  const previousWeek = () => {
    setWeekStart(addDays(weekStart, -7))
  }

  // Navigate to next week
  const nextWeek = () => {
    setWeekStart(addDays(weekStart, 7))
  }

  return (
    <div className="w-full">
      <div className="flex justify-between items-center mb-4">
        <Button
          variant="outline"
          size="icon"
          onClick={previousWeek}
          className="text-white border-blue-700 bg-gray-700 hover:bg-gray-600"
        >
          <ChevronLeft className="h-4 w-4" />
        </Button>
        <h2 className="text-lg font-medium text-white">
          {format(weekStart, "MMMM d")} - {format(addDays(weekStart, 6), "MMMM d, yyyy")}
        </h2>
        <Button
          variant="outline"
          size="icon"
          onClick={nextWeek}
          className="text-white border-blue-700 bg-gray-700 hover:bg-gray-600"
        >
          <ChevronRight className="h-4 w-4" />
        </Button>
      </div>

      <div className="grid grid-cols-7 gap-2">
        {/* Day names */}
        {["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"].map((day) => (
          <div key={day} className="text-center text-sm font-medium text-gray-400 mb-2">
            {day}
          </div>
        ))}

        {/* Calendar days */}
        {weekDays.map(({ date, dateKey, isSelected, isCompleted }) => (
          <Button
            key={dateKey}
            variant={isSelected ? "default" : "outline"}
            className={`
              h-14 w-full rounded-md flex flex-col items-center justify-center
              ${isSelected ? "bg-blue-600 text-white" : "bg-gray-700 text-white border-blue-700"}
              ${isCompleted ? "ring-2 ring-green-500" : ""}
              hover:bg-blue-700 transition-colors
            `}
            onClick={() => onSelectDate(date)}
          >
            <span className="text-lg">{format(date, "d")}</span>
            {isCompleted && <span className="text-xs text-green-400">✓</span>}
          </Button>
        ))}
      </div>
    </div>
  )
}
