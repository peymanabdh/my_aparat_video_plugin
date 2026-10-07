# AGENTS.md — Base44 Dev Environment

## Project Overview
This is a WordPress plugin (`my_aparat_video_plugin.php`) that registers the
`[aparat_video]` shortcode to embed videos from the Aparat video platform
(aparat.com). It fetches videos via Aparat's public JSON API and renders
them as iframes.

## Architecture
The dev environment runs WordPress + MariaDB via Docker Compose:
- `docker-compose.base44.yml` — the runbook
- `db` (MariaDB 11) — WordPress database
- `wordpress` (wordpress:latest) — serves the site on port 3000
- `wp-init` (wordpress:cli) — one-shot: installs WordPress, activates the
  plugin, creates a demo page with the shortcode, sets it as the front page

The plugin source is bind-mounted into the container at
`wp-content/plugins/my-aparat-video-plugin/`. Edits to the PHP file are
picked up immediately on the next page load (no restart needed).

## Key Setup Details
- WordPress admin: user `admin`, password `admin`
- The demo front page uses `[aparat_video username="aparat" limit="3"]`
- `WP_HOME`/`WP_SITEURL` are set dynamically from `BASE44_PUBLIC_HOST_SUFFIX`
  via `WORDPRESS_CONFIG_EXTRA` (eval'd at runtime, not baked into wp-config.php)
- A mu-plugin (`docker/disable-canonical-redirect.php`) disables
  `redirect_canonical` to prevent infinite redirect loops — the preview proxy
  sends a different Host header than the public URL the browser sees
- `$_SERVER['HTTPS']` is forced to 'on' so WordPress generates HTTPS asset URLs
- Compose `$$` escaping is required for `$_SERVER` in `WORDPRESS_CONFIG_EXTRA`
  (otherwise Compose interprets `$_SERVER` as a variable substitution)

## Known Limitation
The Aparat API (aparat.com) is not reachable from the sandbox environment
(returns connection error). The plugin correctly shows "Unable to retrieve
video data." — this is expected here and would work in production where
aparat.com is accessible.

## Verification
- `curl -sI http://localhost:3000/` → HTTP 200
- Page title: "Aparat Video Demo"
- Shortcode executes and renders (error message due to API being unreachable)
- WordPress admin at `/wp-admin/` (admin/admin)
