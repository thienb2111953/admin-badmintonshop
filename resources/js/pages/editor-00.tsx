'use client';

import { useEffect, useState } from 'react';
import { SerializedEditorState, SerializedLexicalNode } from 'lexical';

import { Editor } from '@/components/blocks/editor-00/editor';

export const initialValue = {
  root: {
    children: [
      {
        children: [
          {
            detail: 0,
            format: 0,
            mode: 'normal',
            style: '',
            text: '',
            type: 'text',
            version: 1,
          },
        ],
        direction: 'ltr',
        format: '',
        indent: 0,
        type: 'paragraph',
        version: 1,
      },
    ],
    direction: 'ltr',
    format: '',
    indent: 0,
    type: 'root',
    version: 1,
  },
} as unknown as SerializedEditorState;

interface EditorPageProps {
  value?: string;
  onChange?: (value: string) => void;
}

// Hàm trích xuất text từ SerializedEditorState
const extractTextFromEditorState = (editorState: SerializedEditorState): string => {
  const texts: string[] = [];

  const traverse = (node: any) => {
    if (node.type === 'text' && node.text) {
      texts.push(node.text);
    }
    if (node.children && Array.isArray(node.children)) {
      node.children.forEach((child: any) => traverse(child));
    }
  };

  traverse(editorState.root);
  return texts.join(' ');
};

export default function EditorPage({ value, onChange }: EditorPageProps) {
  const parseValue = (val?: string): SerializedEditorState => {
    if (!val) return initialValue;
    try {
      return JSON.parse(val) as SerializedEditorState;
    } catch {
      return initialValue;
    }
  };

  const [editorState, setEditorState] = useState<SerializedEditorState>(parseValue(value));

  useEffect(() => {
    setEditorState(parseValue(value));
  }, [value]);

  const handleChange = (newValue: SerializedEditorState) => {
    setEditorState(newValue);
    if (onChange) {
      // Chỉ trả về text thuần túy
      const plainText = extractTextFromEditorState(newValue);
      onChange(plainText);
    }
  };

  return <Editor editorSerializedState={editorState} onSerializedChange={handleChange} />;
}
