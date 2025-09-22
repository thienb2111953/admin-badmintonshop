/**
 * v0 by Vercel.
 * @see https://v0.app/t/H91G9RHpfwB
 * Documentation: https://v0.app/docs#integrating-generated-code-into-your-nextjs-app
 */
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from "@/components/ui/card"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { ProgressBar } from "@/components/ui/progress"

export default function UploadFiles() {
  return (
    <div>
        <div className="flex items-center gap-4">
          <Button as="label" htmlFor="files" variant="outline">
            <UploadIcon className="w-4 h-4 mr-1.5" />
            Select files
            <Input id="files" type="file" multiple />
          </Button>
          <Button variant="outline" size="sm">
            <TrashIcon className="w-3 h-3.5 mr-1.5" />
            Remove
          </Button>
        </div>
        <div className="grid gap-2">
          <div className="flex items-center gap-2">
            <FileIcon className="w-5 h-5" />
            <div className="flex-1">
              <div className="font-medium">document.pdf</div>
              <div className="text-sm leading-none">2.3 MB</div>
            </div>
            <Button variant="outline" size="xs">
              <TrashIcon className="w-3 h-3 -translate-y-0.5" />
              <span className="sr-only">Remove</span>
            </Button>
          </div>
          <div className="flex items-center gap-2">
            <FileIcon className="w-5 h-5" />
            <div className="flex-1">
              <div className="font-medium">spreadsheet.xlsx</div>
              <div className="text-sm leading-none">1.1 MB</div>
            </div>
            <Button variant="outline" size="xs">
              <TrashIcon className="w-3 h-3 -translate-y-0.5" />
              <span className="sr-only">Remove</span>
            </Button>
          </div>
        </div>
        <div className="grid gap-4">
          <div className="flex items-center gap-2">
            <FileIcon className="w-5 h-5" />
            <div className="flex-1">
              <div className="font-medium">photo.jpg</div>
              <div className="text-sm leading-none">4.2 MB</div>
            </div>
            <div className="w-1/3" />
          </div>
          <div className="flex items-center gap-2">
            <FileIcon className="w-5 h-5" />
            <div className="flex-1">
              <div className="font-medium">presentation.ppt</div>
              <div className="text-sm leading-none">7.8 MB</div>
            </div>
            <div className="w-1/3" />
          </div>
          <div className="flex items-center gap-2">
            <FileIcon className="w-5 h-5" />
            <div className="flex-1">
              <div className="font-medium">video.mp4</div>
              <div className="text-sm leading-none">12.5 MB</div>
            </div>
            <div className="w-1/3" />
          </div>
        </div>
    </div>
  )
}

function FileIcon(props) {
  return (
    <svg
      {...props}
      xmlns="http://www.w3.org/2000/svg"
      width="24"
      height="24"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      strokeWidth="2"
      strokeLinecap="round"
      strokeLinejoin="round"
    >
      <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z" />
      <path d="M14 2v4a2 2 0 0 0 2 2h4" />
    </svg>
  )
}


function TrashIcon(props) {
  return (
    <svg
      {...props}
      xmlns="http://www.w3.org/2000/svg"
      width="24"
      height="24"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      strokeWidth="2"
      strokeLinecap="round"
      strokeLinejoin="round"
    >
      <path d="M3 6h18" />
      <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
      <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
    </svg>
  )
}


function UploadIcon(props) {
  return (
    <svg
      {...props}
      xmlns="http://www.w3.org/2000/svg"
      width="24"
      height="24"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      strokeWidth="2"
      strokeLinecap="round"
      strokeLinejoin="round"
    >
      <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
      <polyline points="17 8 12 3 7 8" />
      <line x1="12" x2="12" y1="3" y2="15" />
    </svg>
  )
}