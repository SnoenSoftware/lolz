import { StrictMode } from 'react';
import ReactDOM from 'react-dom';
import { CurrentThemeProvider } from './contexts/CurrentThemeContext';
import { App } from './components/App';

window.scrollTo(0, 0);

ReactDOM.render(
    <StrictMode>
        <CurrentThemeProvider>
            <App />
        </CurrentThemeProvider>
    </StrictMode>,
    document.getElementById('app')
);
