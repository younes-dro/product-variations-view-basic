import React from 'react';
import ReactDOM from 'react-dom/client';
import App from './App';


document.addEventListener("DOMContentLoaded", () => {
    const rootElement = document.getElementById("pvv-app");

    if (rootElement) {
        const root = ReactDOM.createRoot(rootElement);

        root.render(
            <App /> 
            
        
        );
    }
});