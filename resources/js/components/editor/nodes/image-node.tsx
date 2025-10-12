import {
  DecoratorNode,
  DOMConversionMap,
  DOMConversionOutput,
  DOMExportOutput,
  LexicalNode,
  NodeKey,
  SerializedLexicalNode,
  Spread,
} from 'lexical';

export interface ImagePayload {
  altText: string;
  src: string;
  key?: NodeKey;
  width?: number;
  height?: number;
  maxWidth?: number;
  showCaption?: boolean;
  caption?: string;
}

export interface SerializedImageNode extends SerializedLexicalNode {
  altText: string;
  src: string;
  width?: number;
  height?: number;
  maxWidth?: number;
  showCaption?: boolean;
  caption?: string;
}

function convertImageElement(domNode: Node): null | DOMConversionOutput {
  const img = domNode as HTMLImageElement;
  if (img.src.startsWith('file:///')) {
    return null;
  }
  const { alt: altText, src, width, height } = img;
  const node = $createImageNode({ altText, src, width, height });
  return { node };
}

export class ImageNode extends DecoratorNode<JSX.Element> {
  __src: string;
  __altText: string;
  __width?: number;
  __height?: number;
  __maxWidth: number;
  __showCaption: boolean;
  __caption: string;

  static getType(): string {
    return 'image';
  }

  static clone(node: ImageNode): ImageNode {
    return new ImageNode(
      node.__src,
      node.__altText,
      node.__maxWidth,
      node.__width,
      node.__height,
      node.__showCaption,
      node.__caption,
      node.__key,
    );
  }

  constructor(
    src: string,
    altText: string,
    maxWidth = 500,
    width?: number,
    height?: number,
    showCaption = false,
    caption = '',
    key?: NodeKey,
  ) {
    super(key);
    this.__src = src;
    this.__altText = altText;
    this.__maxWidth = maxWidth;
    this.__width = width;
    this.__height = height;
    this.__showCaption = showCaption;
    this.__caption = caption;
  }

  // ===== THÊM METHOD NÀY =====
  static importDOM(): DOMConversionMap | null {
    return {
      img: (node: Node) => ({
        conversion: convertImageElement,
        priority: 0,
      }),
    };
  }
  // ===========================

  exportDOM(): DOMExportOutput {
    const element = document.createElement('img');
    element.setAttribute('src', this.__src);
    element.setAttribute('alt', this.__altText);
    if (this.__maxWidth) {
      element.style.maxWidth = `${this.__maxWidth}px`;
    }
    if (this.__width) {
      element.style.width = `${this.__width}px`;
    }
    if (this.__height) {
      element.style.height = `${this.__height}px`;
    }
    return { element };
  }

  static importJSON(serializedNode: SerializedImageNode): ImageNode {
    const { altText, src, width, height, maxWidth, showCaption, caption } = serializedNode;
    const node = $createImageNode({
      altText,
      src,
      width,
      height,
      maxWidth,
      showCaption,
      caption,
    });
    return node;
  }

  exportJSON(): SerializedImageNode {
    return {
      altText: this.__altText,
      src: this.__src,
      width: this.__width,
      height: this.__height,
      maxWidth: this.__maxWidth,
      showCaption: this.__showCaption,
      caption: this.__caption,
      type: 'image',
      version: 1,
    };
  }

  createDOM(): HTMLElement {
    const span = document.createElement('span');
    span.className = 'editor-image';
    return span;
  }

  updateDOM(): false {
    return false;
  }

  decorate(): JSX.Element {
    const styles = {
      maxWidth: this.__maxWidth ? `${this.__maxWidth}px` : '100%',
      width: this.__width ? `${this.__width}px` : undefined,
      height: this.__height ? `${this.__height}px` : undefined,
      display: 'block',
    };

    return <img src={this.__src} alt={this.__altText} style={styles} />;
  }
}

export function $createImageNode(payload: ImagePayload): ImageNode {
  return new ImageNode(
    payload.src,
    payload.altText,
    payload.maxWidth,
    payload.width,
    payload.height,
    payload.showCaption,
    payload.caption,
    payload.key,
  );
}

export function $isImageNode(node: LexicalNode | null | undefined): node is ImageNode {
  return node instanceof ImageNode;
}
