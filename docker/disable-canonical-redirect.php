<?php
// Disable WordPress canonical redirects — the preview proxy sends a different
// Host header than the public URL the browser sees, which would otherwise cause
// infinite redirect loops.
remove_action('template_redirect', 'redirect_canonical');
