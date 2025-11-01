import { Button } from "@/components/ui/button"
import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover"
import { Check, ChevronDown } from "lucide-react"
import { useState } from "react"

interface DataTableFilterDropdownProps {
    column: any
    title: string
    options: string[]
}

export function DataTableFilterDropdown({
                                            column,
                                            title,
                                            options,
                                        }: DataTableFilterDropdownProps) {
    const [open, setOpen] = useState(false)
    const selected = (column?.getFilterValue() as string[]) || []

    const toggleOption = (option: string) => {
        if (!column) return
        const newValue = selected.includes(option)
            ? selected.filter((v) => v !== option)
            : [...selected, option]
        column.setFilterValue(newValue.length ? newValue : undefined)
    }

    return (
        <Popover open={open} onOpenChange={setOpen}>
            <PopoverTrigger asChild>
                <Button variant="outline" size="sm" className="flex items-center gap-2">
                    {title}
                    <ChevronDown className="h-4 w-4" />
                </Button>
            </PopoverTrigger>

            <PopoverContent className="w-44 p-2">
                {options.map((option) => (
                    <div
                        key={option}
                        className="flex items-center justify-between px-2 py-1 cursor-pointer hover:bg-muted rounded"
                        onClick={() => toggleOption(option)}
                    >
                        <span>{option}</span>
                        {selected.includes(option) && <Check className="h-4 w-4" />}
                    </div>
                ))}
            </PopoverContent>
        </Popover>
    )
}
