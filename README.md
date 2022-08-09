# Content Security Policy Manager

Contributors: 16patsle
Tags: csp, content security policy, security, security headers, xss
Requires at least: 4.6
Requires PHP: 7.2
Tested up to: 6.1
Stable tag: 1.2.1
License: GPLv3 or later
License URI: <http://www.gnu.org/licenses/gpl-3.0.html>

Plugin for configuring Content Security Policy headers for your site. Allows different CSP headers for admin, logged inn frontend and regular visitors

## Description

**Content Security Policy Manager** is a WordPress plugin that allows you to easily configure [Content Security Policy headers](https://developer.mozilla.org/en-US/docs/Web/HTTP/CSP) for your site. You can have different CSP headers for the admin interface, the frontend for logged in users, and the frontend for regular visitors. The CSP directives can be individually enabled, and each policy can be set to enforce, report or be disabled.

Please note that this plugin offers limited help in figuring out what the contents of the policy should be. It only lets you configure the CSP in a easy to use interface.

## Frequently Asked Questions

### What is a Content Security Policy?

To quote MDN:

> Content Security Policy (CSP) is an added layer of security that helps to detect and mitigate certain types of attacks, including Cross Site Scripting (XSS) and data injection attacks. These attacks are used for everything from data theft to site defacement to distribution of malware.
>
> To enable CSP, you need to configure your web server to return the Content-Security-Policy HTTP header.

### How do I enable reporting?

Reporting can be enabled by setting the report-uri and/or report-to directives. You will need the URL to a server that can handle these kinds of reports, which there are several of. [Report URI](https://report-uri.com/) is one example of such a service, they have a free tier that allows up to 10 000 reports per month (any more than that is just ignored, no extra cost). They also have a CSP wizard that can help you construct your policy.

Reporting can be enabled both in report only mode and in enforce mode. You can use report-only mode to evaluate the contents of the policy by looking at which resources are reported as blocked.

## Changelog

This plugin's development happens in [its GitHub repo](https://github.com/16patsle/wordpress-csp-manager). Feel free to send bug reports there.

### 1.2.1

- Fix error caused by improperly checking the chosen CSP mode when outputting headers (thanks @reatlat).

### 1.2.0

- Improved UI, with CSP directives divided into collapsible categories.
- Add all remaining non-deprecated CSP directives.
- Warn if enabling upgrade-insecure-requests on a site that does not support HTTPS.
- Sanitize directives on save and disallow newlines in header content.
- Various internal improvements.

### 1.1.0

This is a relatively small update, that only contains a few more CSP directives. The next update will contain even more, along with an updated user interface.

- Add some commonly used CSP headers that were missing (thanks Master Dan).
- Add some other user requested directives.
- Fix some translator comments.

### 1.0.0

First version.

- Support for different policies for admin, logged-in frontend and regular visitors.
- Different policies can have different reporting/enforcing mode.
- Directives can be configured separately, to easier see what is allowed in which cases.
- Support for configuring the Report-To header.
