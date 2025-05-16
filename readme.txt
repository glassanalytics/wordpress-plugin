=== Glass Analytics ===
Contributors: glassanalytics
Tags: analytics, statistics, tracking
Requires at least: 5.0
Tested up to: 6.8
Requires PHP: 7.2
Stable tag: 1.8
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt

Add Glass Analytics tracking to your WordPress site.

== Description ==
Glass Analytics is a lightweight WordPress plugin that integrates with your website to provide detailed analytics about user behavior, page views, and engagement metrics.

== Installation ==
1. Upload the plugin files to the `/wp-content/plugins/glass-analytics` directory, or install the plugin through the WordPress plugins screen.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Configure the plugin settings through the 'Glass Analytics' menu in WordPress admin.

== Features ==
* Simple setup with minimal configuration required
* User-friendly dashboard for viewing analytics data
* Track page views, user sessions, and engagement metrics
* Export analytics data in various formats

== External Services ==
This plugin connects to the Glass Analytics service to collect and display website usage data:

= Glass Analytics Service =
* Purpose: This service collects anonymous visitor data from your website to provide you with analytics about page views, visitor behavior, and site engagement.
* Data Collected: The service collects data including page views, referring URLs, browser information, device information, and general geographic location of visitors (based on IP address, which is anonymized).
* When Data is Sent: Data is sent when visitors access pages on your website where the Glass Analytics tracking script is active.
* Service Domains: The plugin connects to the following domains:
  * https://staging-cdn.glassanalytics.com - Hosts the tracking script
  * https://staging.app.glassanalytics.com - Hosts the analytics dashboard

The Glass Analytics service is provided by Glass Analytics, Inc.:
* Terms of Service: https://glassanalytics.com/terms
* Privacy Policy: https://glassanalytics.com/privacy

== Changelog ==
= 1.7 =
* Updated plugin to comply with WordPress.org guidelines
* Improved script and style enqueuing
* Added documentation for external services

= 1.2 =
* Initial public release

== Upgrade Notice ==
= 1.7 =
This update improves compatibility with WordPress standards and adds proper documentation.