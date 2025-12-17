<?php
/**
 * Integrated Themes Showcase Page Template
 * Display all compatible themes with EazyDocs plugin
 *
 * @package EazyDocs\Admin
 */

// Cannot access directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Theme data array with demo URLs, marketplace URLs, and descriptions
$integrated_themes = [
	[
		'id'              => 'docy',
		'name'            => __( 'Docy', 'eazydocs' ),
		'description'     => __( 'Professional documentation and forum theme with advanced features for creating knowledge bases, community forums, and comprehensive support resources.', 'eazydocs' ),
		'preview_url'     => EAZYDOCS_ASSETS . '/images/admin/themes/docy.png',
		'demo_url'        => 'https://wordpress-theme.spider-themes.net/docy/',
		'marketplace_url' => 'https://themeforest.net/item/docy-documentation-and-forum-wordpress-theme/31370838',
	],
	[
		'id'              => 'docly',
		'name'            => __( 'Docly', 'eazydocs' ),
		'description'     => __( 'Comprehensive documentation and knowledge base theme with powerful built-in features for creating extensive documentation, forums, and support systems.', 'eazydocs' ),
		'preview_url'     => EAZYDOCS_ASSETS . '/images/admin/themes/docly.png',
		'demo_url'        => 'https://wordpress-theme.spider-themes.net/docly/',
		'marketplace_url' => 'https://themeforest.net/item/docly-documentation-and-knowledge-base-wordpress-theme/26885280',
	],
	[
		'id'              => 'saasland',
		'name'            => __( 'Saasland', 'eazydocs' ),
		'description'     => __( 'Specialized SaaS and startup theme for software companies with modern design, pricing tables, feature showcases, and marketing-focused layouts perfect for tech products.', 'eazydocs' ),
		'preview_url'     => EAZYDOCS_ASSETS . '/images/admin/themes/saasland.png',
		'demo_url'        => 'https://saaslandwp.net/',
		'marketplace_url' => 'https://themeforest.net/item/saasland-creative-wordpress-theme-for-saas-business/23362980',
	],
	[
		'id'              => 'banca',
		'name'            => __( 'Banca', 'eazydocs' ),
		'description'     => __( 'Banking and business finance theme for financial institutions, loan services, and corporate documentation with professional layouts and trust-building features.', 'eazydocs' ),
		'preview_url'     => EAZYDOCS_ASSETS . '/images/admin/themes/banca.png',
		'demo_url'        => 'https://wordpress-theme.spider-themes.net/banca/',
		'marketplace_url' => 'https://themeforest.net/item/banca-banking-business-loanwordpress-theme/33736009',
	],
	[
		'id'              => 'ama',
		'name'            => __( 'Ama', 'eazydocs' ),
		'description'     => __( 'Social Q&A and forum WordPress theme for building community-driven question and answer platforms with discussion forums and user engagement features.', 'eazydocs' ),
		'preview_url'     => EAZYDOCS_ASSETS . '/images/admin/themes/ama.png',
		'demo_url'        => 'https://wordpress-theme.spider-themes.net/ama/',
		'marketplace_url' => 'https://themeforest.net/item/ama-social-questions-and-answers-wordpress-theme/36587700',
	],
	[
		'id'              => 'landpagy',
		'name'            => __( 'Landpagy', 'eazydocs' ),
		'description'     => __( 'Multipurpose landing page theme ideal for creating high-converting product pages, promotional campaigns, and corporate websites with modern design elements.', 'eazydocs' ),
		'preview_url'     => EAZYDOCS_ASSETS . '/images/admin/themes/landpagy.png',
		'demo_url'        => 'https://wordpress-theme.spider-themes.net/landpagy/',
		'marketplace_url' => 'https://themeforest.net/item/landpagy-multipurpose-landing-page-wordpress-theme/36332915',
	],
	[
		'id'              => 'deski',
		'name'            => __( 'Deski', 'eazydocs' ),
		'description'     => __( 'Modern event ticketing and landing page WordPress theme designed for creating professional event pages, ticket sales, and promotional campaigns.', 'eazydocs' ),
		'preview_url'     => EAZYDOCS_ASSETS . '/images/admin/themes/deski.png',
		'demo_url'        => 'https://creativegigs.spider-themes.net/deski/',
		'marketplace_url' => 'https://themeforest.net/item/deski-multipurpose-landing-page-wordpress-theme/34094683',
	],
	[
		'id'              => 'rogan',
		'name'            => __( 'Rogan', 'eazydocs' ),
		'description'     => __( 'Creative multipurpose WordPress theme perfect for businesses, portfolios, and customer support platforms with elegant design and powerful customization options.', 'eazydocs' ),
		'preview_url'     => EAZYDOCS_ASSETS . '/images/admin/themes/rogan.png',
		'demo_url'        => 'https://creativegigs.spider-themes.net/rogan/',
		'marketplace_url' => 'https://themeforest.net/item/rogan-creative-multipurpose-wordpress-theme/24061213',
	],
	[
		'id'              => 'zoomy',
		'name'            => __( 'Zoomy', 'eazydocs' ),
		'description'     => __( 'Learning management system theme for online courses and educational platforms featuring course management, instructor profiles, and student engagement tools.', 'eazydocs' ),
		'preview_url'     => EAZYDOCS_ASSETS . '/images/admin/themes/zoomy.png',
		'demo_url'        => 'https://wordpress-theme.spider-themes.net/zoomy/',
		'marketplace_url' => 'https://themeforest.net/item/zoomy-lms-education-wordpress-theme/36597783',
	],
];
?>

<div class="wrap ezd-themes-showcase-wrapper">
    <!-- Header -->
    <div class="ezd-themes-header">
        <div class="ezd-header-content">
            <h1><?php esc_html_e( 'Compatible Themes for EazyDocs', 'eazydocs' ); ?></h1>
        </div>
        <div class="ezd-header-content">
            <p class="ezd-header-subtitle"><?php esc_html_e( 'Discover professionally designed WordPress themes optimized for documentation. Each theme is tested and verified to work seamlessly with EazyDocs for the best user experience.',
                        'eazydocs' ); ?></p>
        </div>
    </div>

    <!-- Themes Grid -->
    <div class="ezd-themes-grid">
        <?php foreach ( $integrated_themes as $theme ) : ?>
            <div class="ezd-theme-card">
                <!-- Theme Preview Image -->
                <div class="ezd-theme-preview">
                    <img src="<?php echo esc_url( $theme['preview_url'] ); ?>" alt="<?php echo esc_attr( $theme['name'] ); ?>" loading="lazy">
                </div>

                <!-- Theme Info -->
                <div class="ezd-theme-info">
                    <h3><?php echo esc_html( $theme['name'] ); ?></h3>
                    <p><?php echo esc_html( $theme['description'] ); ?></p>

                    <!-- Action Buttons -->
                    <div class="ezd-theme-buttons">
                        <a href="<?php echo esc_url( $theme['demo_url'] ); ?>" target="_blank" rel="noopener noreferrer" class="ezd-btn ezd-btn-demo">
                            <?php esc_html_e( 'View Demo', 'eazydocs' ); ?>
                        </a>
                        <a href="<?php echo esc_url( $theme['marketplace_url'] ); ?>" target="_blank" rel="noopener noreferrer"
                           class="ezd-btn ezd-btn-purchase">
                            <?php esc_html_e( 'Get Theme', 'eazydocs' ); ?>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>