import './bootstrap';

import Alpine from 'alpinejs';
import 'flyonui/flyonui';

window.Alpine = Alpine;

Alpine.start();

// Auto re-initialize FlyonUI elements after dynamic updates
document.addEventListener('livewire:navigated', () => {
    if (window.HSStaticMethods) {
        window.HSStaticMethods.autoInit();
    }
});