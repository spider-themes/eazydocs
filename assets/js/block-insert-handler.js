wp.data.subscribe(() => {
    const blocks = wp.data.select('core/block-editor').getBlocks();

    // Check if EazyDocs block exists in the current editor content
    const hasEazyDocsBlock = blocks.some(block => block.name === 'eazydocs-pro/eazy-docs');

    if (hasEazyDocsBlock && !document.body.classList.contains('ezd-assets-loaded')) {
        document.body.classList.add('ezd-assets-loaded');

        // Dynamically load styles
        ezdAssets.styles.forEach(styleUrl => {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = styleUrl;
            document.head.appendChild(link);
        });

        // Dynamically load scripts
        ezdAssets.scripts.forEach(scriptUrl => {
            const script = document.createElement('script');
            script.src = scriptUrl;
            document.body.appendChild(script);
        });
    }
});
