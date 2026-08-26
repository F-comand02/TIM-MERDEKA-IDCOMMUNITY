(function () {
    const icons = {
        '🔥': '<path d="M12 22c4.5 0 8-3.1 8-7.4 0-3.4-2.1-5.7-4.3-8.1-.4 2.1-1.4 3.4-2.6 4.2.2-3.5-1.5-6.5-4.1-8.7.1 3.2-2.9 5.6-4.3 8.5C2.8 12 2 13.8 2 15.5 2 19.3 6.1 22 12 22Z"/><path d="M9.5 16.2c0-1.1.7-2.1 1.8-3.3.5 1.1 1.2 1.8 2 2.5.3-1 .1-1.8-.2-2.5 1.2 1.1 1.9 2.2 1.9 3.4 0 1.6-1.2 2.7-2.8 2.7s-2.7-1.1-2.7-2.8Z"/>',
        '❤️': '<path d="M20.8 8.7c0 5.5-8.8 10.3-8.8 10.3S3.2 14.2 3.2 8.7A4.7 4.7 0 0 1 12 6.4a4.7 4.7 0 0 1 8.8 2.3Z"/>',
        '🎯': '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1"/>',
        '🌱': '<path d="M12 22V10"/><path d="M12 14c-4.5 0-7-2.4-7-7 4.6 0 7 2.4 7 7Z"/><path d="M12 10c0-4.1 2.3-6 6-6 0 4.1-2.1 6-6 6Z"/>',
        '🏆': '<path d="M8 21h8"/><path d="M12 17v4"/><path d="M7 4h10v5a5 5 0 0 1-10 0V4Z"/><path d="M7 6H4v2a4 4 0 0 0 4 4M17 6h3v2a4 4 0 0 1-4 4"/>',
        '👑': '<path d="m3 7 3 3 6-6 6 6 3-3-2 11H5L3 7Z"/><path d="M5 21h14"/>',
        '🌈': '<path d="M3 19a9 9 0 0 1 18 0"/><path d="M6 19a6 6 0 0 1 12 0"/><path d="M9 19a3 3 0 0 1 6 0"/>',
        '📍': '<path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/>',
        '💼': '<rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M3 12h18M10 12v2h4v-2"/>',
        '📚': '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"/>',
        '🌍': '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.3 2.5 3.4 5.5 3.4 9S14.3 18.5 12 21M12 3C9.7 5.5 8.6 8.5 8.6 12s1.1 6.5 3.4 9"/>',
        '🌎': '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.3 2.5 3.4 5.5 3.4 9S14.3 18.5 12 21M12 3C9.7 5.5 8.6 8.5 8.6 12s1.1 6.5 3.4 9"/><path d="M7 6c1 1 2 1.3 3 1M15 17c1.2-1 2.5-1.2 4-1"/>',
        '🌏': '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.3 2.5 3.4 5.5 3.4 9S14.3 18.5 12 21M12 3C9.7 5.5 8.6 8.5 8.6 12s1.1 6.5 3.4 9"/><path d="M5 8c2 0 3 1 4 2M15 5c1 1 2 2 4 2M6 17c2-1 3-1 5 0"/>',
        '🗺️': '<path d="m3 6 6-3 6 3 6-3v15l-6 3-6-3-6 3V6Z"/><path d="M9 3v15M15 6v15"/>',
        '🌴': '<path d="M12 21V9"/><path d="M12 10C9 7 6 7 3 8c2-3 5-4 9-2 1-3 3-4 6-4-1 2-2 4-5 6 4-1 7 0 9 3-4-1-7-1-10-1Z"/>',
        '🏙️': '<path d="M4 21V8h5v13M9 21V4h6v17M15 21v-9h5v9M2 21h20"/><path d="M6 11h1M6 14h1M11 7h1M11 10h1M11 13h1M17 15h1M17 18h1"/>',
        '🏝️': '<path d="M4 21c4-3 12-3 16 0"/><path d="M12 19V9"/><path d="M12 11c-3-3-5-3-7-2 2-3 5-3 7-1 1-3 3-4 5-4-1 3-3 5-5 6 3-1 5 0 7 2-3-1-5-1-7-1Z"/><path d="M3 18c3-2 5-2 8 0M13 18c3-2 5-2 8 0"/>',
        '🏔️': '<path d="m3 20 7-12 3 5 2-3 6 10H3Z"/><path d="m10 8 2 3 1-2M16 10l2 3"/>',
        '🌳': '<path d="M12 22V12"/><path d="m12 12-3-3M12 16l4-4"/><path d="M5 12a4 4 0 0 1 1-7.9A6 6 0 0 1 18 6a4 4 0 0 1-1 7.9H5Z"/>',
        '🛡️': '<path d="M12 3 20 6v5c0 5-3.4 8.7-8 10-4.6-1.3-8-5-8-10V6l8-3Z"/><path d="m9 12 2 2 4-4"/>',
        '📈': '<path d="M4 19V5M4 19h16"/><path d="m7 15 3-3 3 2 5-6"/><path d="M15 8h3v3"/>',
        '🏛️': '<path d="M3 21h18M5 18V9M9 18V9M15 18V9M19 18V9M3 9h18L12 4 3 9Z"/><path d="M2 21h20"/>',
        '💡': '<path d="M9 18h6M10 21h4"/><path d="M8 14a6 6 0 1 1 8 0c-.9.7-1 1.7-1 2H9c0-.3-.1-1.3-1-2Z"/>',
        '⚡': '<path d="m13 2-9 12h7l-1 8 9-12h-7l1-8Z"/>',
        '💧': '<path d="M12 3.5S5 11 5 15a7 7 0 0 0 14 0c0-4-7-11.5-7-11.5Z"/>',
        '🤝': '<path d="m7 11 3-3 4 4 3-3 4 4-4 4-4-4-3 3-4-4 3-3Z"/><path d="m2 7 3-3 4 4M22 7l-3-3-4 4"/>',
        '🤲': '<path d="M4 12v5a3 3 0 0 0 3 3h7a4 4 0 0 0 3.6-2.2L21 12"/><path d="m4 12 3-3 3 3 3-3 3 3 3-3"/><path d="M8 7V4M12 8V3M16 8V4"/>',
        '⚖️': '<path d="M12 3v18M5 21h14M4 7h16M7 7l-3 7h6L7 7ZM17 7l-3 7h6l-3-7Z"/>',
        '♻️': '<path d="m7 7-3 5 3 5"/><path d="M4 12h10a4 4 0 0 1 4 4v1"/><path d="m17 17 3-5-3-5"/><path d="M20 12H10a4 4 0 0 1-4-4V7"/>',
        '🏅': '<circle cx="12" cy="14" r="6"/><path d="m8 9-2-6 4 2 2-3 2 3 4-2-2 6"/><path d="M10 14h4M12 12v4"/>',
        '🍚': '<path d="M4 13h16a8 8 0 0 1-16 0Z"/><path d="M6 10c1-2 3-3 6-3s5 1 6 3M3 13h18"/>',
        '💻': '<rect x="4" y="4" width="16" height="12" rx="1"/><path d="M2 20h20M8 20l1-4h6l1 4"/>',
        '🏘️': '<path d="m3 11 5-4 5 4v8H3v-8ZM13 13l4-3 4 3v6h-8v-6Z"/><path d="M6 15h2M16 16h2"/>',
        '📂': '<path d="M3 6a2 2 0 0 1 2-2h5l2 2h7a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6Z"/><path d="M3 9h18"/>',
        '🌊': '<path d="M3 8c3 0 3 3 6 3s3-3 6-3 3 3 6 3"/><path d="M3 14c3 0 3 3 6 3s3-3 6-3 3 3 6 3"/>',
        '🕊️': '<path d="M3 17c4-1 6-4 8-8 1-2 3-4 6-4l-2 3h5c-1 5-5 9-11 9H3Z"/><path d="m9 14 3-2"/>',
        '📅': '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/>',
        '📸': '<path d="M4 7h3l1.5-2h7L17 7h3a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2Z"/><circle cx="12" cy="13" r="3.5"/>',
        '🎉': '<path d="m4 20 6-10M4 20l10-2M4 20l2-10"/><path d="M14 4v3M20 7h-3M18 2l-2 2M21 13h-3M19 11l-2 2"/>',
        '📜': '<path d="M6 3h12v18H6a3 3 0 0 1 0-6h12"/><path d="M6 15h12M9 7h6M9 10h6"/>',
        '✦': '<path d="m12 2 1.7 6.3L20 10l-6.3 1.7L12 18l-1.7-6.3L4 10l6.3-1.7L12 2Z"/>',
        '⏳': '<path d="M6 2h12M6 22h12M7 2v4c0 2 5 4 5 6s-5 4-5 6v4M17 2v4c0 2-5 4-5 6s5 4 5 6v4"/>',
        '�🇩': '<path d="M3 5h18v7H3z" fill="currentColor" stroke="none"/><path d="M3 12h18v7H3z" fill="#fff" stroke="none"/><path d="M3 5h18v14H3z"/>',
        '→': '<path d="M5 12h14M13 6l6 6-6 6"/>',
        '←': '<path d="M19 12H5M11 18l-6-6 6-6"/>'
    };

    const iconColors = {
        '🔥': '#e0522d',
        '❤️': '#dc2626',
        '🎯': '#2563eb',
        '🌱': '#16a34a',
        '🏆': '#d49a16',
        '👑': '#c58a13',
        '🌈': '#7c3aed',
        '📍': '#dc2626',
        '💼': '#2563eb',
        '📚': '#7c3aed',
        '🌍': '#16856a',
        '🌎': '#16856a',
        '🌏': '#16856a',
        '🛡️': '#2563eb',
        '📈': '#8b5cf6',
        '🏛️': '#64748b',
        '💡': '#f59e0b',
        '🌳': '#15803d',
        '⚡': '#d97706',
        '💧': '#0284c7',
        '🤝': '#db2777',
        '🤲': '#d97706',
        '⚖️': '#475569',
        '♻️': '#16a34a',
        '✦': '#f59e0b',
        '⏳': '#64748b',
        '🏅': '#d49a16',
        '🍚': '#b7791f',
        '💻': '#2563eb',
        '🏘️': '#0f766e',
        '📂': '#d49a16',
        '🌊': '#0284c7',
        '🕊️': '#64748b',
        '📅': '#d71920',
        '📸': '#d71920',
        '🎉': '#d71920',
        '📜': '#d71920',
        '🇮🇩': '#d71920',
        '→': '#d71920',
        '←': '#737373'
    };

    const iconList = Object.keys(icons);
    const iconPattern = new RegExp(iconList.map(escapeRegExp).join('|'), 'gu');

    function escapeRegExp(value) {
        return value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    function normalizeIconKey(value) {
        return value.replace(/\uFE0F/g, '');
    }

    function buildSvg(match) {
        const key = normalizeIconKey(match);
        const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        svg.setAttribute('class', 'ui-icon');
        svg.setAttribute('viewBox', '0 0 24 24');
        svg.setAttribute('aria-hidden', 'true');
        svg.style.color = iconColors[match] || iconColors[key] || 'currentColor';
        svg.innerHTML = icons[match] || icons[key] || '';
        return svg;
    }

    function replaceText(node) {
        if (!node || !node.nodeValue || !node.parentElement) return;
        if (node.parentElement.closest('.ui-icon') || node.parentElement.tagName === 'SCRIPT' || node.parentElement.tagName === 'STYLE') return;

        const text = node.nodeValue;
        if (!iconPattern.test(text)) return;

        iconPattern.lastIndex = 0;
        const fragment = document.createDocumentFragment();
        let lastIndex = 0;
        let match;

        while ((match = iconPattern.exec(text)) !== null) {
            fragment.append(document.createTextNode(text.slice(lastIndex, match.index)));
            fragment.append(buildSvg(match[0]));
            lastIndex = match.index + match[0].length;
        }

        fragment.append(document.createTextNode(text.slice(lastIndex)));
        node.parentNode.replaceChild(fragment, node);
    }

    function scanTextNodes() {
        if (!document.body) return;
        const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT);
        const nodes = [];
        while (walker.nextNode()) nodes.push(walker.currentNode);
        nodes.forEach(replaceText);
    }

    scanTextNodes();

    if (window.MutationObserver) {
        const observer = new MutationObserver(function () {
            scanTextNodes();
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true,
            characterData: true
        });
    }
})();
