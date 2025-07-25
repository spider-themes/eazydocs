<?php
wp_enqueue_style( 'sweetalert' );
wp_enqueue_script( 'sweetalert' );
?>
<div class="wrap">
    <div class="ezd-migration-wrapper">
        <div class="ezd-migration-inner">
            <img src="<?php echo esc_url( EAZYDOCS_IMG . '/bdocs-ezd.png' ); ?>" alt="<?php echo esc_attr__( 'Eazydocs icon', 'eazydocs' ); ?>"/>

            <h1>Migrate from BetterDocs to EazyDocs</h1>

            <p>
                This tool will help you migrate your existing documentation from <strong>BetterDocs</strong> into<br> <strong>EazyDocs</strong>.
                During this migration:
            </p>

            <ul>
                <li>All <strong>categories</strong> will be converted into <strong>parent Docs</strong>.</li>
                <li>All existing Docs will be organized as <strong>child Docs</strong> under those parent Docs.</li>
                <li>The URL structure will reflect the parent-child relationship (e.g. <code>/docs/parent-doc/child-doc/</code>).</li>
                <li>Your original category will be preserved as taxonomy terms.</li>
            </ul>

            <span>
                Need help? <a href="#" target="_blank">Read the full migration guide</a>
            </span>

            <button class="ezd-start-miration-btn button-primary"> 🚀 Start Migration </button>
        </div>
    </div>
</div>