#!/bin/sh
set -e

URL="https://3000-${BASE44_PUBLIC_HOST_SUFFIX}"

# Install WordPress if not already installed
if ! wp core is-installed --allow-root 2>/dev/null; then
    wp core install --allow-root \
        --url="${URL}" \
        --title="Aparat Video Demo" \
        --admin_user=admin \
        --admin_password=admin \
        --admin_email=admin@example.com \
        --skip-email
fi

# Activate the plugin
wp plugin activate my-aparat-video-plugin --allow-root || true

# Create demo page if it doesn't exist, then set it as the front page
if ! wp post list --post_type=page --post_status=publish --field=post_title --allow-root 2>/dev/null | grep -q "Aparat Video Demo"; then
    PAGE_ID=$(wp post create --allow-root --porcelain \
        --post_type=page \
        --post_title="Aparat Video Demo" \
        --post_status=publish \
        --post_content='[aparat_video username="aparat" limit="3"]')
    wp option update show_on_front page --allow-root
    wp option update page_on_front "${PAGE_ID}" --allow-root
fi

echo "WordPress setup complete."
