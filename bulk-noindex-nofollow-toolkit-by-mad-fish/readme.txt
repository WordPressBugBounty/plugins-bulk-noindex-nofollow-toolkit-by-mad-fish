=== Bulk NoIndex & NoFollow Toolkit ===
Contributors: MadFishDigital
Donate link: https://www.madfishdigital.com/plugins-donate/
Tags: bulk noindex nofollow, seo penalty recovery, yoast, All in One SEO (AIOSEO), Rank Math
Requires PHP: 5.6
Requires at least: 4.1
Tested up to: 6.9.1
Stable tag: 2.30
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html

Bulk set the noindex / nofollow robots tag for posts, pages, categories, and author URLs. Easily identify thin content and noindex it fast.

== Description ==

Developed by Mad Fish Digital, this plugin saves webmasters time when finding and removing thin pages from search engine indexes.

The plugin provides an interface to sort posts by word count and character count, then bulk noindex or nofollow them. Categories and terms can be sorted by post count and managed the same way. Author archive URLs can now also be managed from a dedicated tab — useful for controlling whether author pages appear in search results.

= Please Keep in Mind =

After a page or category is noindexed, it can take search engines up to a few weeks before the page stops appearing in the search index. The amount of time will depend on how frequently a search engine crawls your website. We recommend using Google Search Console to further analyze how your pages appear in the search index.

= Advantages =

1) Reduce the time it takes to NoIndex/NoFollow each page, post, category, or author URL manually

2) Sort posts and pages by word count and character count to quickly identify thin content

3) Sort categories and tags by their number of associated posts

4) Manage NoIndex and NoFollow directives for author archive URLs from a dedicated Authors tab

5) Speed up search engine penalty recovery by bulk noindexing large numbers of posts and pages

6) Quickly noindex content identified by web crawlers such as Screaming Frog or DeepCrawl

7) Visualize all posts', pages', categories', and authors' noindex and nofollow statuses at a glance

8) Syncs with Rank Math, Yoast SEO, and AIOSEO to maintain and manage your existing robots directives

= Support =

For support related inquiries, visit the <a rel="follow" href="https://www.madfishdigital.com/wp-plugins/">Mad Fish Digital plugin support page</a> to drop us a line or ask a question. Please note that responses to specific inquiries may take up to 24 hours.

#### Why would you want to remove a bulk amount of pages from search indexes?

At Mad Fish Digital, we use tools like Screaming Frog, LinkResearch Tools, Ahrefs, and SEM Rush to crawl and analyze web pages. Sometimes, you want to remove multiple web pages from a search engine's index that contain no longer current content, old products and services, or outdated guidelines/regulations. In many of these cases, you need the pages to be temporarily dropped from Google's index today, but may want to update the content at a later date. By noindexing a post or page, you can avoid having to set the status code of those pages to 404 (or 410).

This is where having a tool to bulk noindex/nofollow these pages can become handy. You can easily remove pages from the search index, then remove the noindex directive once the content has been updated.

This plugin allows you to bulk edit the meta robots index and follow directives for your site's posts, pages, categories, and author archive URLs. This tool is compatible with your existing Rank Math, Yoast SEO, and AIOSEO settings. Syncing of category noindex/nofollow settings with Yoast and AIOSEO is not yet supported.

By keeping your pages in sync with the noindex/nofollow settings for Rank Math, Yoast, and AIOSEO, you never have to worry about duplicating efforts or which plugin is managing your robots directives.

#### Fallback Protection

If you disable your Rank Math, Yoast, or AIOSEO plugins, this plugin will continue to serve the appropriate meta robots tag based on the noindex/nofollow settings configured through the interface.

If you do disable any of these plugins, be sure to check the Bulk NoIndex/NoFollow interface (Tools menu) to confirm that your posts, pages, and author URLs are still noindexed and nofollowed accordingly. Robots directives set directly through the WP post editing interface may not always be tracked by this plugin if those SEO plugins were previously enabled but are later disabled.

== Screenshots ==

1. This is a screenshot of the interface for bulk noindexing and bulk nofollowing posts and pages

2. This is a walk through of the interface and how easy it is to bulk noindex and bulk nofollow posts and pages

== Installation & Usage ==
1) Login as an administrator to your WordPress Admin account. Using the "Add New" menu option under the "Plugins" section of the navigation, you can either search for: "Bulk NoIndex & NoFollow Tool" or if you've downloaded the plugin already, click the "Upload" link, find the .zip file you downloaded and then click "Install Now". Or you can unzip and FTP upload the plugin to your plugins directory.

2) Navigate to Tools -> Bulk NoIndex/NoFollow

3) Use the Posts, Categories, or Authors tabs to manage robots directives

== Frequently Asked Questions ==

= Will this plugin play nice if I already use Yoast SEO for noindexing and nofollowing pages? =

Yes, this plugin will sync with Yoast's native noindexing settings for posts and pages. Note that Yoast's global noindex settings for categories and terms will override this plugin's individual settings.

= Will this plugin work if I am using the Rank Math plugin for noindexing and nofollowing pages? =

Yes, this plugin fully syncs with Rank Math's native noindexing and nofollowing settings for posts, pages, categories, and author archive URLs.

= Will this plugin work if I am using the All in One SEO plugin for noindexing and nofollowing pages? =

Yes, this plugin syncs with the All in One SEO native noindexing and nofollowing settings for posts and pages.

= Can I control the noindex/nofollow settings for author archive pages? =

Yes. Version 2.30 adds a dedicated Authors tab where you can toggle the NoIndex and NoFollow directives for each author's archive URL. This is useful for sites where author archive pages contain thin or duplicate content.

== Upgrade Notice ==

= 2.30 =
This release adds author archive URL support (new Authors tab), a comprehensive security hardening pass addressing 11 vulnerabilities, and updated SEO plugin compatibility documentation.

= 2.20 =
We recommend upgrading to the latest version as it adds support for the Rank Math SEO plugin, includes a patch for some minor javascript bugs, and includes a patch to address a rare but potential Cross-Site scripting (XSS) vulnerability found by Patchstack.

= 2.16 =
We recommend upgrading to the latest version as it includes a patch to address a rare but potential Cross-Site scripting (XSS) vulnerability.

= 2.15 =
We recommend upgrading to the latest version to access categories with no posts.

= 2.10 =
We recommend upgrading to the latest version as it includes a patch to address the potential for a Cross-Site scripting (XSS) vulnerability reported by users.

= 2.01 =
This latest version provides additional support for managing custom post types, categories and terms. This update also changes the way the robots meta tag is implemented to avoid potential duplication.

= 1.51 =
We recommend upgrading to the latest version as it includes additional security measures.

= 1.5 =
We recommend upgrading to the latest version as it includes a patch to address the potential for a Cross-Site scripting (XSS) vulnerability.

= 1.42 =
We recommend upgrading to the latest version as it includes a fix for a minor warning occurring when not using either All in One SEO or Yoast.

= 1.41 =
We recommend upgrading to the latest version as it includes a fix for a minor warning occurring in PHP 7.4.

= 1.4 =
We recommend upgrading to the latest version as it includes some performance enhancements for the interface.

= 1.3 =
We recommend upgrading to the latest version as it now includes support for AIOSEO.

= 1.2 =
We recommend upgrading to the latest version as it corrects some PHP warnings that were appearing in php.log files.

= 1.1 =
We recommend upgrading to the latest version to avoid potential issues in the case where another plugin or theme prevents the WP is_plugin_active() function from properly loading.

== Changelog ==

= 2.30 =
Release Date: March 8th, 2025

* Added dedicated Authors tab to manage NoIndex and NoFollow directives for author archive URLs
* Author archive support includes full Rank Math sync via rank_math_robots user meta; Yoast and AIOSEO directives are applied via their existing output filters
* Front-end robots meta tag output now correctly handles author archive pages via is_author() check
* Security: fixed incomplete authorization check on bulk post updates (capability now verified per post)
* Security: fixed reflected XSS — added esc_attr() and esc_url() to all unescaped output in the admin UI
* Security: fixed DOM-based XSS in JavaScript — replaced .html() with safe DOM construction and fixed jQuery selector injection
* Security: replaced deprecated FILTER_SANITIZE_STRING (removed in PHP 8.4) with sanitize_text_field()
* Security: added absint() sanitization on integer GET parameters (items_per_page, paged)
* Security: bulk post ID arrays now cast with absint() instead of sanitize_text_field()
* Security: added whitelist validation before dynamic usort() callback to prevent arbitrary function execution
* Security: changed relative include() path to absolute path using plugin_dir_path()
* Security: removed debug list_hooks() function left in production code
* Simplified xss_clean() helper to use WordPress-native sanitize_text_field()
* Changed is_page() to is_singular() for broader post type coverage on front-end robots checks

= 2.20 =
Release Date: April 18, 2025

Added support for Rank Math, minor javascript bug fix when bulk selecting pages and categories, patched a potential vulnerability.

= 2.16 =
Release Date: September 24, 2024

Patched a potential vulnerability.

= 2.15 =
Release Date: August 24th, 2024

Minor bug fixes for filtering on searched categories, and displaying categories that have no posts.

= 2.10 =
Release Date: March 9th, 2024

Patched a potential vulnerability.

= 2.01 =
Release Date: February 14th, 2024

* Added support for custom post types and categories

= 1.51 =
Release Date: September 11th, 2023

* Patch that implements additional security measures

= 1.5 =
Release Date: September 11th, 2023

* Patch that addresses a potential XSS vulnerability

= 1.42 =
Release Date: June 13th, 2023

* Small patch that addresses a minor bug for non-Yoast and non-AIOSEO users

= 1.41 =
Release Date: July 28th, 2022

* Small patch that addresses a minor warning in PHP 7.4

= 1.4 =
Release Date: July 27th, 2022

* Updated the queries for the interface to use less resources

= 1.3 =
Release Date: July 26th, 2022

* This release adds support for the All in One SEO Pack plugin

= 1.2 =
Release Date: January 26th, 2021

* This release fixes some small PHP warnings that were happening in some instances of PHP 7.4.

= 1.1 =
Release Date: January 11th, 2021

* This release improves error catching if the WordPress is_plugin_active() function had not loaded prior to this plugin loading. In some instances, a PHP error was thrown due to is_plugin_active() not being available.
