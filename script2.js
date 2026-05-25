document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('sparkles');
    if (!container) return;

    const sparkleCount = 18;

    for (let i = 0; i < sparkleCount; i++) {
        const s = document.createElement('div');
        s.className = 'sparkle';

        // Random positioning
        s.style.left = Math.random() * 100 + '%';
        s.style.top  = Math.random() * 100 + '%';

        // Randomize animation timing for an organic feel
        s.style.animationDelay    = (Math.random() * 4) + 's';
        s.style.animationDuration = (2.5 + Math.random() * 2) + 's';

        container.appendChild(s);
    }
});