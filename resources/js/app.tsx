import './bootstrap';
import '../css/app.css';

import { createInertiaApp } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';
import { Component, type ReactNode, type ErrorInfo } from 'react';

class ErrorBoundary extends Component<{ children: ReactNode }, { error: Error | null }> {
    state = { error: null as Error | null };

    static getDerivedStateFromError(error: Error) {
        return { error };
    }

    componentDidCatch(error: Error, info: ErrorInfo) {
        console.error('React render error:', error, info.componentStack);
    }

    render() {
        if (this.state.error) {
            return (
                <div style={{ padding: '2rem', fontFamily: 'system-ui, sans-serif' }}>
                    <h1 style={{ color: '#dc2626', fontSize: '1.25rem', fontWeight: 600 }}>
                        Something went wrong
                    </h1>
                    <pre style={{ marginTop: '1rem', padding: '1rem', background: '#fef2f2', borderRadius: '0.5rem', overflow: 'auto', fontSize: '0.875rem', color: '#991b1b' }}>
                        {this.state.error.message}
                        {'\n\n'}
                        {this.state.error.stack}
                    </pre>
                </div>
            );
        }
        return this.props.children;
    }
}

createInertiaApp({
    title: (title) => title ? `${title} — MagShip` : 'MagShip',
    resolve: (name) => {
        const pages = import.meta.glob('./pages/**/*.tsx', { eager: true });
        const page = pages[`./pages/${name}.tsx`];
        if (!page) {
            console.error(`Page not found: ${name}. Available pages:`, Object.keys(pages));
        }
        return page;
    },
    setup({ el, App, props }) {
        createRoot(el).render(
            <ErrorBoundary>
                <App {...props} />
            </ErrorBoundary>
        );
    },
    progress: {
        color: '#3b82f6',
        showSpinner: true,
    },
});
