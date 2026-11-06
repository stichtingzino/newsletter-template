# Newsletter Template

A lightweight WordPress plugin that automatically injects a predefined 1/3 and 2/3 column layout template into the Gutenberg block editor when a specific post category is selected.

## Features

- **Automated Content Injection:** Detects when a configured category (e.g., "Newsletter") is selected in a new post and instantly populates the editor with a complete newsletter skeleton structure.
- **Configurable Settings UI:** Easily choose which WordPress category triggers the template injection directly from the WordPress Admin dashboard (**Settings > Newsletter Template**).
- **Optimized Layout Block Structure:** Injects a top introduction paragraph followed by 5 rows of perfectly formatted asymmetric columns (33.33% / 66.66% flex-basis distribution).
- **Asymmetric Core Block Setup:** Pre-configures a dedicated Image placeholder on the left and a Heading block (with `0px` top-margin override) alongside a clean paragraph structure on the right.
- **Clean Architecture:** Separates backend configurations from editor assets using proper `is_admin()` conditional checks to keep your website fast and production-efficient.

## Codebase Architecture

The plugin is structured into two main decoupled PHP files:

```text
newsletter-template/
├── newsletter-template.php  # Main entry point, registers block patterns and enqueues editor assets
└── admin-settings.php       # Admin dashboard page logic, handles configuration options (Loaded in admin only)
```

## Installation

### Via FTP
1. Download or clone this repository into a directory named `newsletter-template`.
2. Upload the `newsletter-template` folder to your WordPress installation directory under `/wp-content/plugins/`.
3. Navigate to the **Plugins** page in your WordPress dashboard and click **Activate** under **Newsletter Template**.

### Via WordPress Dashboard Zip Upload
1. Compress the `newsletter-template` directory into a `.zip` archive.
2. Go to **Plugins > Add New Plugin** in your WordPress Admin interface.
3. Click **Upload Plugin**, choose your `.zip` archive, and hit **Install Now**.
4. Click **Activate Plugin** once the extraction completes.

## Configuration

1. In your WordPress sidebar, navigate to **Settings > Newsletter Template**.
2. Select your desired newsletter category from the **Target Category** dropdown menu.
3. Click **Save Changes**.

## Usage for Webmasters

1. Go to **Posts > Add New**.
2. In the right-hand sidebar panel under the **Post** tab, look for the **Categories** meta box.
3. Check the box of your designated newsletter category.
4. The Gutenberg editor will automatically load the full multi-column newsletter template layout with helpful input placeholders instantly.

## Developer & System Notes

- **WordPress Compatibility:** Tested and safe for WordPress 6.x through the upcoming WordPress 7.14 core editor React datastores.
- **Dependencies:** Relies entirely on native core WordPress editor assets (`wp-edit-post`, `core/editor`, `core/notices`) and standard block markup. Does not require external libraries or CSS frameworks.
- **Data Safety:** Uses `wp_localize_script()` to safely pass system configuration metadata into inline editor execution loops without risk of string or URI corruption.

## License

This project is licensed under the GPL-2.0+ License.
