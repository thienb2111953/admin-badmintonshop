import '../css/app.css';

import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createRoot } from 'react-dom/client';
import { initializeTheme } from './hooks/use-appearance';

import { route as routeFn, Config } from 'ziggy-js';
import { Ziggy } from '@/ziggyGenerated';
import Providers from './providers';

declare global {
  var route: typeof routeFn;
}

globalThis.route = (name: string, params?: any, absolute?: boolean, config: Config = Ziggy) =>
  routeFn(name, params, absolute, config);

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
  title: (title) => (title ? `${title} - ${appName}` : appName),
  resolve: (name) => resolvePageComponent(`./pages/${name}.tsx`, import.meta.glob('./pages/**/*.tsx')),
  setup({ el, App, props }) {
    const root = createRoot(el);

    root.render(
      <Providers>
        <App {...props} />
      </Providers>,
    );
  },
  progress: {
    color: '#4B5563',
  },
});

// This will set light / dark mode on load...
initializeTheme();
