'use client';

import { $generateHtmlFromNodes } from '@lexical/html';
import { InitialConfigType, LexicalComposer } from '@lexical/react/LexicalComposer';
import { useLexicalComposerContext } from '@lexical/react/LexicalComposerContext';
import { OnChangePlugin } from '@lexical/react/LexicalOnChangePlugin';
import { EditorState, LexicalEditor, SerializedEditorState } from 'lexical';

import { editorTheme } from '@/components/editor/themes/editor-theme';
import { TooltipProvider } from '@/components/ui/tooltip';

import { nodes } from './nodes';
import { Plugins } from './plugins';
import { useEffect } from 'react';
import { $generateNodesFromDOM } from '@lexical/html';
import { $getRoot } from 'lexical';

const editorConfig: InitialConfigType = {
  namespace: 'Editor',
  theme: editorTheme,
  nodes,
  onError: (error: Error) => {
    console.error(error);
  },
};

function HtmlPlugin({ onHtmlChange }: { onHtmlChange?: (html: string) => void }) {
  const [editor] = useLexicalComposerContext();

  const handleOnChange = (editorState: EditorState, editor: LexicalEditor) => {
    editorState.read(() => {
      if (onHtmlChange) {
        const htmlString = $generateHtmlFromNodes(editor, null);
        onHtmlChange(htmlString);
      }
    });
  };

  return <OnChangePlugin ignoreSelectionChange onChange={handleOnChange} />;
}

function InitialHtmlLoader({ initialHtml }: { initialHtml?: string }) {
  const [editor] = useLexicalComposerContext();

  useEffect(() => {
    if (!initialHtml) return;
    editor.update(() => {
      const parser = new DOMParser();
      const dom = parser.parseFromString(initialHtml, 'text/html');
      const nodes = $generateNodesFromDOM(editor, dom);
      const root = $getRoot();
      root.clear();
      root.append(...nodes);
    });
  }, [editor, initialHtml]);

  return null;
}

export function Editor({
  editorState,
  editorSerializedState,
  onChange,
  onSerializedChange,
  onHtmlChange,
  initialHtml,
}: {
  editorState?: EditorState;
  editorSerializedState?: SerializedEditorState;
  onChange?: (editorState: EditorState) => void;
  onSerializedChange?: (editorSerializedState: SerializedEditorState) => void;
  onHtmlChange?: (htmlString: string) => void;
  initialHtml?: string;
}) {
  return (
    <div className="overflow-hidden rounded-lg border bg-background shadow">
      <LexicalComposer
        initialConfig={{
          ...editorConfig,
          ...(editorState ? { editorState } : {}),
          ...(editorSerializedState ? { editorState: JSON.stringify(editorSerializedState) } : {}),
        }}
      >
        <TooltipProvider>
          <Plugins />

          <InitialHtmlLoader initialHtml={initialHtml} />

          <OnChangePlugin
            ignoreSelectionChange={true}
            onChange={(editorState) => {
              onChange?.(editorState);
              onSerializedChange?.(editorState.toJSON());
            }}
          />

          <HtmlPlugin onHtmlChange={onHtmlChange} />
        </TooltipProvider>
      </LexicalComposer>
    </div>
  );
}
