import './bootstrap';
import '../css/app.css';

import { createRoot } from 'react-dom/client';
import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ThemeProvider } from '@/components/theme-provider';
import { TooltipProvider } from '@/components/ui/tooltip';
import { Toaster } from '@/components/ui/sonner';
import AppLayout from '@/layouts/AppLayout';

createInertiaApp({
    resolve: (name) => {
        const page = resolvePageComponent(
            `./Pages/${name}.jsx`,
            import.meta.glob('./Pages/**/*.jsx')
        );

        page.then((module) => {
            module.default.layout =
                module.default.layout ??
                ((page) => (
                    <AppLayout {...(module.default.layoutProps ?? {})}>
                        {page}
                    </AppLayout>
                ));
        });

        return page;
    },
    setup({ el, App, props }) {
        const root = createRoot(el);
        root.render(
            <ThemeProvider
                attribute="class"
                defaultTheme="system"
                enableSystem
                disableTransitionOnChange
            >
                <TooltipProvider>
                    <App {...props} />
                    <Toaster />
                </TooltipProvider>
            </ThemeProvider>
        );
    },
});