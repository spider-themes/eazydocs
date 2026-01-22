<?php
/**
 * Analytics Email Reports
 * Configure automated email reports for documentation performance.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get the next scheduled report timestamp
$next_report_timestamp = wp_next_scheduled( 'eazydocs_send_report' );
$last_sent_timestamp   = get_option( 'ezd_send_report_email', 0 );

// Format the timestamps for display
$next_report_date = $next_report_timestamp ? date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $next_report_timestamp ) : esc_html__( 'Not scheduled', 'eazydocs' );
$last_sent_date   = $last_sent_timestamp ? date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $last_sent_timestamp ) : esc_html__( 'Never sent', 'eazydocs' );

// Build the status info HTML
$report_status_html = '
<div class="ezd-email-report-status-wrapper">
	<div class="ezd-report-status-card">
		<div class="ezd-status-icon">
			<span class="dashicons dashicons-clock"></span>
		</div>
		<div class="ezd-status-content">
			<span class="ezd-status-label">' . esc_html__( 'Last Report Sent', 'eazydocs' ) . '</span>
			<span class="ezd-status-value">' . esc_html( $last_sent_date ) . '</span>
		</div>
	</div>
	<div class="ezd-report-status-card">
		<div class="ezd-status-icon ezd-status-icon-next">
			<span class="dashicons dashicons-calendar-alt"></span>
		</div>
		<div class="ezd-status-content">
			<span class="ezd-status-label">' . esc_html__( 'Next Scheduled', 'eazydocs' ) . '</span>
			<span class="ezd-status-value">' . esc_html( $next_report_date ) . '</span>
		</div>
	</div>
</div>';

// Create a section
CSF::createSection(
	$prefix,
	array(
		'id'     => 'reporting_opt',
		'parent' => 'email_settings',
		'title'  => esc_html__( 'Automatic Email Reports', 'eazydocs' ),
		'icon'   => '',
		'fields' => array(

			// Section Header with Feature Overview
			array(
				'type'    => 'content',
				'content' => '
				<div class="ezd-email-reports-header">
					<div class="ezd-header-icon">
						<span class="dashicons dashicons-chart-bar"></span>
					</div>
					<div class="ezd-header-content">
						<h3>' . esc_html__( 'Automated Periodic Reports', 'eazydocs' ) . '</h3>
						<p>' . esc_html__( 'Receive comprehensive analytics summaries directly in your inbox. Track page views, search queries, user reactions, and documentation statistics without logging in to your dashboard.', 'eazydocs' ) . '</p>
						<div class="ezd-feature-highlights">
							<span class="ezd-highlight-item"><span class="dashicons dashicons-yes-alt"></span> ' . esc_html__( 'Customizable frequency', 'eazydocs' ) . '</span>
							<span class="ezd-highlight-item"><span class="dashicons dashicons-yes-alt"></span> ' . esc_html__( 'Visual charts included', 'eazydocs' ) . '</span>
							<span class="ezd-highlight-item"><span class="dashicons dashicons-yes-alt"></span> ' . esc_html__( 'Performance comparisons', 'eazydocs' ) . '</span>
							<span class="ezd-highlight-item"><span class="dashicons dashicons-yes-alt"></span> ' . esc_html__( 'Multiple recipients', 'eazydocs' ) . '</span>
						</div>
					</div>
				</div>',
			),

			// Report Status Display
			array(
				'id'         => 'reporting_status_display',
				'type'       => 'content',
				'content'    => $report_status_html,
				'dependency' => array(
					array( 'reporting_enabled', '==', 'true' ),
				),
			),

			array(
				'id'       => 'reporting_enabled',
				'type'     => 'switcher',
				'title'    => '<span class="dashicons dashicons-admin-generic ezd-field-icon"></span> ' . esc_html__( 'Enable Email Reports', 'eazydocs' ),
				'subtitle' => esc_html__( 'Toggle this option to activate automated email reports. When enabled, you\'ll receive periodic summaries of your documentation analytics based on the schedule you configure below.', 'eazydocs' ),
				'class'    => 'eazydocs-promax-notice ezd-field-primary',
				'default'  => false,
			),

			// Schedule Settings Subheading
			array(
				'type'       => 'subheading',
				'content'    => '<span class="dashicons dashicons-calendar ezd-subheading-icon"></span> ' . esc_html__( 'Schedule Settings', 'eazydocs' ),
				'dependency' => array(
					array( 'reporting_enabled', '==', 'true' ),
				),
			),

			array(
				'id'         => 'reporting_frequency',
				'type'       => 'select',
				'title'      => '<span class="dashicons dashicons-update ezd-field-icon"></span> ' . esc_html__( 'Report Frequency', 'eazydocs' ),
				'subtitle'   => esc_html__( 'Select how often you want to receive analytics reports. Daily reports are ideal for active documentation sites, while weekly or monthly reports work better for smaller sites.', 'eazydocs' ),
				'dependency' => array(
					array( 'reporting_enabled', '==', 'true' ),
				),
				'options'    => array(
					'daily'   => esc_html__( '📅 Daily — Perfect for high-traffic documentation', 'eazydocs' ),
					'weekly'  => esc_html__( '📆 Weekly — Balanced overview of performance', 'eazydocs' ),
					'monthly' => esc_html__( '🗓️ Monthly — Comprehensive monthly summary', 'eazydocs' ),
				),
				'class'      => 'eazydocs-promax-notice',
				'default'    => 'weekly',
			),

			array(
				'id'         => 'reporting_day',
				'type'       => 'select',
				'title'      => '<span class="dashicons dashicons-calendar-alt ezd-field-icon"></span> ' . esc_html__( 'Weekly Report Day', 'eazydocs' ),
				'subtitle'   => esc_html__( 'Choose which day of the week you\'d like to receive your weekly reports. Many users prefer Monday to review the previous week\'s activity.', 'eazydocs' ),
				'multiple'   => false,
				'dependency' => array(
					array( 'reporting_enabled', '==', 'true' ),
					array( 'reporting_frequency', '==', 'weekly' ),
				),
				'options'    => array(
					'sunday'    => esc_html__( 'Sunday', 'eazydocs' ),
					'monday'    => esc_html__( 'Monday (Recommended)', 'eazydocs' ),
					'tuesday'   => esc_html__( 'Tuesday', 'eazydocs' ),
					'wednesday' => esc_html__( 'Wednesday', 'eazydocs' ),
					'thursday'  => esc_html__( 'Thursday', 'eazydocs' ),
					'friday'    => esc_html__( 'Friday', 'eazydocs' ),
					'saturday'  => esc_html__( 'Saturday', 'eazydocs' ),
				),
				'class'      => 'eazydocs-promax-notice',
				'default'    => 'monday',
			),

			array(
				'id'         => 'reporting_monthly_day',
				'type'       => 'select',
				'title'      => '<span class="dashicons dashicons-calendar-alt ezd-field-icon"></span> ' . esc_html__( 'Monthly Report Day', 'eazydocs' ),
				'subtitle'   => esc_html__( 'Select which day of the month to receive your monthly report. Choose "Last Day" for end-of-month summaries.', 'eazydocs' ),
				'dependency' => array(
					array( 'reporting_enabled', '==', 'true' ),
					array( 'reporting_frequency', '==', 'monthly' ),
				),
				'options'    => array_merge(
					array( '1' => esc_html__( '1st of the month', 'eazydocs' ) ),
					array( '15' => esc_html__( '15th of the month', 'eazydocs' ) ),
					array( 'last' => esc_html__( 'Last day of the month', 'eazydocs' ) ),
				),
				'class'      => 'eazydocs-promax-notice',
				'default'    => 'last',
			),

			array(
				'id'         => 'reporting_time',
				'type'       => 'select',
				'title'      => '<span class="dashicons dashicons-clock ezd-field-icon"></span> ' . esc_html__( 'Preferred Time', 'eazydocs' ),
				'subtitle'   => esc_html__( 'Select the time of day when you\'d like to receive your email reports. Reports will be sent in your site\'s timezone.', 'eazydocs' ),
				'dependency' => array(
					array( 'reporting_enabled', '==', 'true' ),
				),
				'options'    => array(
					'00:00' => esc_html__( '12:00 AM (Midnight)', 'eazydocs' ),
					'06:00' => esc_html__( '6:00 AM', 'eazydocs' ),
					'08:00' => esc_html__( '8:00 AM', 'eazydocs' ),
					'09:00' => esc_html__( '9:00 AM (Recommended)', 'eazydocs' ),
					'12:00' => esc_html__( '12:00 PM (Noon)', 'eazydocs' ),
					'15:00' => esc_html__( '3:00 PM', 'eazydocs' ),
					'18:00' => esc_html__( '6:00 PM', 'eazydocs' ),
				),
				'class'      => 'eazydocs-promax-notice',
				'default'    => '09:00',
				'desc'       => sprintf(
					/* translators: %s: Site timezone */
					esc_html__( 'Current site timezone: %s', 'eazydocs' ),
					'<strong>' . esc_html( wp_timezone_string() ) . '</strong>'
				),
			),

			// Report Content Subheading
			array(
				'type'       => 'subheading',
				'content'    => '<span class="dashicons dashicons-analytics ezd-subheading-icon"></span> ' . esc_html__( 'Report Content', 'eazydocs' ),
				'dependency' => array(
					array( 'reporting_enabled', '==', 'true' ),
				),
			),

			array(
				'id'          => 'reporting_data',
				'type'        => 'select',
				'title'       => '<span class="dashicons dashicons-chart-pie ezd-field-icon"></span> ' . esc_html__( 'Report Metrics', 'eazydocs' ),
				'subtitle'    => esc_html__( 'Choose which analytics metrics to include in your email reports. Select multiple metrics for a comprehensive overview. Each metric includes trend comparisons with the previous period.', 'eazydocs' ),
				'chosen'      => true,
				'multiple'    => true,
				'placeholder' => esc_html__( 'Select metrics to include...', 'eazydocs' ),
				'dependency'  => array(
					array( 'reporting_enabled', '==', 'true' ),
				),
				'options'     => array(
					'views'     => esc_html__( '👁️ Page Views — Track how many times your documentation is read', 'eazydocs' ),
					'searches'  => esc_html__( '🔍 Search Queries — Monitor what users are searching for', 'eazydocs' ),
					'reactions' => esc_html__( '👍 User Reactions — See helpful/unhelpful feedback on docs', 'eazydocs' ),
					'docs'      => esc_html__( '📄 Documentation Stats — Track new and updated documents', 'eazydocs' ),
				),
				'class'       => 'eazydocs-promax-notice',
				'default'     => array( 'views', 'searches', 'reactions', 'docs' ),
			),

			// Email Configuration Subheading
			array(
				'type'       => 'subheading',
				'content'    => '<span class="dashicons dashicons-email ezd-subheading-icon"></span> ' . esc_html__( 'Email Configuration', 'eazydocs' ),
				'dependency' => array(
					array( 'reporting_enabled', '==', 'true' ),
				),
			),

			array(
				'id'         => 'reporting_email',
				'type'       => 'text',
				'title'      => '<span class="dashicons dashicons-admin-users ezd-field-icon"></span> ' . esc_html__( 'Primary Recipient', 'eazydocs' ),
				'subtitle'   => esc_html__( 'Enter the primary email address where reports should be sent. This is required. Leave empty to use the site administrator email.', 'eazydocs' ),
				'default'    => get_option( 'admin_email' ),
				'dependency' => array(
					array( 'reporting_enabled', '==', 'true' ),
				),
				'attributes' => array(
					'type'        => 'email',
					'placeholder' => 'example@domain.com',
				),
				'class'      => 'eazydocs-promax-notice',
			),

			array(
				'id'         => 'reporting_cc_emails',
				'type'       => 'text',
				'title'      => '<span class="dashicons dashicons-groups ezd-field-icon"></span> ' . esc_html__( 'CC Recipients', 'eazydocs' ),
				'subtitle'   => esc_html__( 'Add additional email addresses to receive copies of the report. Separate multiple emails with commas.', 'eazydocs' ),
				'dependency' => array(
					array( 'reporting_enabled', '==', 'true' ),
				),
				'attributes' => array(
					'placeholder' => 'team@domain.com, manager@domain.com',
				),
				'class'      => 'eazydocs-promax-notice',
			),

			// Email Customization Subheading
			array(
				'type'       => 'subheading',
				'content'    => '<span class="dashicons dashicons-art ezd-subheading-icon"></span> ' . esc_html__( 'Email Customization', 'eazydocs' ),
				'dependency' => array(
					array( 'reporting_enabled', '==', 'true' ),
				),
			),

			array(
				'id'         => 'reporting_site_name',
				'type'       => 'text',
				'title'      => '<span class="dashicons dashicons-admin-site ezd-field-icon"></span> ' . esc_html__( 'Site Name in Email', 'eazydocs' ),
				'subtitle'   => esc_html__( 'Customize how your site name appears in the email header. Leave empty to use your WordPress site title.', 'eazydocs' ),
				'default'    => get_bloginfo( 'name' ),
				'dependency' => array(
					array( 'reporting_enabled', '==', 'true' ),
				),
				'attributes' => array(
					'placeholder' => get_bloginfo( 'name' ),
				),
				'class'      => 'eazydocs-promax-notice',
			),

			array(
				'id'         => 'reporting_heading',
				'type'       => 'text',
				'title'      => '<span class="dashicons dashicons-heading ezd-field-icon"></span> ' . esc_html__( 'Email Subject Line', 'eazydocs' ),
				'subtitle'   => esc_html__( 'Customize the subject line for report emails. Use clear, descriptive text so your reports are easy to find in your inbox.', 'eazydocs' ),
				'default'    => esc_html__( 'Your Documentation Analytics Summary', 'eazydocs' ),
				'dependency' => array(
					array( 'reporting_enabled', '==', 'true' ),
				),
				'attributes' => array(
					'placeholder' => esc_html__( 'Weekly Documentation Report', 'eazydocs' ),
				),
				'class'      => 'eazydocs-promax-notice',
				'desc'       => esc_html__( 'Tip: Include your site name in the subject for easier inbox filtering.', 'eazydocs' ),
			),

			array(
				'id'         => 'reporting_description',
				'type'       => 'textarea',
				'title'      => '<span class="dashicons dashicons-editor-paragraph ezd-field-icon"></span> ' . esc_html__( 'Email Introduction', 'eazydocs' ),
				'subtitle'   => esc_html__( 'Add a personalized introduction message that appears at the top of each report email. This helps provide context for team members receiving the reports.', 'eazydocs' ),
				'default'    => esc_html__( 'Here\'s your comprehensive analytics summary for the documentation on your website. This report includes key metrics and performance trends to help you understand how users interact with your documentation.', 'eazydocs' ),
				'dependency' => array(
					array( 'reporting_enabled', '==', 'true' ),
				),
				'attributes' => array(
					'rows'        => '4',
					'style'       => 'min-height:unset',
					'placeholder' => esc_html__( 'Write a brief introduction for your reports...', 'eazydocs' ),
				),
				'class'      => 'eazydocs-promax-notice',
			),

			// Testing Section Subheading
			array(
				'type'       => 'subheading',
				'content'    => '<span class="dashicons dashicons-visibility ezd-subheading-icon"></span> ' . esc_html__( 'Test & Preview', 'eazydocs' ),
				'dependency' => array(
					array( 'reporting_enabled', '==', 'true' ),
				),
			),

			array(
				'title'      => '<span class="dashicons dashicons-email-alt ezd-field-icon"></span> ' . esc_html__( 'Send Test Report', 'eazydocs' ),
				'subtitle'   => esc_html__( 'Send a sample report email to verify your settings are configured correctly. The test report uses sample data to show how your actual reports will look.', 'eazydocs' ),
				'id'         => 'reporting_sample',
				'type'       => 'content',
				'content'    => '
				<div class="ezd-test-email-wrapper">
					<button class="button button-primary button-hero ezd-analytics-sample-report" type="button">
						<span class="dashicons dashicons-email-alt"></span>
						<span class="ezd-btn-text">' . esc_html__( 'Send Test Email', 'eazydocs' ) . '</span>
						<span class="ezd-btn-loading" style="display:none;">
							<span class="spinner is-active" style="margin:0;float:none;"></span>
							' . esc_html__( 'Sending...', 'eazydocs' ) . '
						</span>
					</button>
					<p class="description ezd-test-email-desc">
						<span class="dashicons dashicons-info-outline"></span>
						' . esc_html__( 'A sample report will be sent to the primary recipient email address configured above.', 'eazydocs' ) . '
					</p>
				</div>',
				'class'      => 'eazydocs-promax-notice ezd-test-email-field',
				'dependency' => array(
					array( 'reporting_enabled', '==', 'true' ),
				),
			),

			// SMTP Configuration Notice
			array(
				'type'       => 'content',
				'content'    => '
				<div class="ezd-smtp-notice">
					<div class="ezd-smtp-notice-icon">
						<span class="dashicons dashicons-email-alt2"></span>
					</div>
					<div class="ezd-smtp-notice-content">
						<h4>' . esc_html__( '📧 SMTP Configuration Recommended', 'eazydocs' ) . '</h4>
						<p>' . esc_html__( 'For reliable email delivery, we strongly recommend configuring SMTP (Simple Mail Transfer Protocol) on your WordPress site. By default, WordPress uses PHP\'s mail() function which often fails or emails end up in spam folders.', 'eazydocs' ) . '</p>
						<p class="ezd-smtp-benefits-title">' . esc_html__( 'Benefits of using SMTP:', 'eazydocs' ) . '</p>
						<ul class="ezd-smtp-benefits">
							<li><span class="dashicons dashicons-yes"></span> ' . esc_html__( 'Higher email deliverability rates', 'eazydocs' ) . '</li>
							<li><span class="dashicons dashicons-yes"></span> ' . esc_html__( 'Emails won\'t end up in spam folders', 'eazydocs' ) . '</li>
							<li><span class="dashicons dashicons-yes"></span> ' . esc_html__( 'Email logging and debugging capabilities', 'eazydocs' ) . '</li>
							<li><span class="dashicons dashicons-yes"></span> ' . esc_html__( 'Works with popular services like Gmail, SendGrid, Mailgun, and Amazon SES', 'eazydocs' ) . '</li>
						</ul>
						<p class="ezd-smtp-plugins-title">' . esc_html__( 'Recommended SMTP Plugins:', 'eazydocs' ) . '</p>
						<div class="ezd-smtp-plugins">
							<a href="https://wordpress.org/plugins/wp-mail-smtp/" target="_blank" class="ezd-smtp-plugin">
								<span class="dashicons dashicons-plugins-checked"></span>
								<span class="ezd-plugin-name">WP Mail SMTP</span>
								<span class="ezd-plugin-badge">' . esc_html__( 'Popular', 'eazydocs' ) . '</span>
							</a>
							<a href="https://wordpress.org/plugins/fluent-smtp/" target="_blank" class="ezd-smtp-plugin">
								<span class="dashicons dashicons-plugins-checked"></span>
								<span class="ezd-plugin-name">FluentSMTP</span>
								<span class="ezd-plugin-badge ezd-badge-free">' . esc_html__( 'Free', 'eazydocs' ) . '</span>
							</a>
							<a href="https://wordpress.org/plugins/post-smtp/" target="_blank" class="ezd-smtp-plugin">
								<span class="dashicons dashicons-plugins-checked"></span>
								<span class="ezd-plugin-name">Post SMTP</span>
							</a>
						</div>
					</div>
				</div>',
				'dependency' => array(
					array( 'reporting_enabled', '==', 'true' ),
				),
			),

			// Help Section
			array(
				'type'       => 'content',
				'content'    => '
				<div class="ezd-email-reports-help">
					<div class="ezd-help-card">
						<span class="dashicons dashicons-editor-help ezd-help-icon"></span>
						<div class="ezd-help-content">
							<h4>' . esc_html__( 'Troubleshooting Tips', 'eazydocs' ) . '</h4>
							<p>' . esc_html__( 'If you\'re not receiving reports, check the following:', 'eazydocs' ) . '</p>
							<ul>
								<li>' . esc_html__( 'Verify the recipient email address is correct', 'eazydocs' ) . '</li>
								<li>' . esc_html__( 'Check your spam/junk folder for the reports', 'eazydocs' ) . '</li>
								<li>' . esc_html__( 'Ensure an SMTP plugin is installed and configured properly', 'eazydocs' ) . '</li>
								<li>' . esc_html__( 'Ensure your WordPress cron is running properly', 'eazydocs' ) . '</li>
								<li>' . esc_html__( 'Use the "Send Test Email" button above to verify your email setup', 'eazydocs' ) . '</li>
							</ul>
						</div>
					</div>
				</div>',
				'dependency' => array(
					array( 'reporting_enabled', '==', 'true' ),
				),
			),

		),
	)
);
