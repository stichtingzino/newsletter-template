# Newsletter Template

A custom, robust WordPress plugin designed specifically for the **YourConn Child Theme** powered by the **Enfold Avia Advanced Layout Builder Framework**. 

Instead of relying on the Gutenberg block editor, this plugin hooks directly into Enfold's internal builder pipeline to instantly generate pre-formatted multi-column layouts for newsletters using native Avia shortcodes.

## Compatibility Notice

> [!IMPORTANT]
> **Requirements:** This plugin is uniquely architecturalized for environments running the **Avia Advanced Layout Builder (Enfold Framework)**. It will not execute layout injections on themes using the default WordPress block editor (Gutenberg) or other page builders.

## Features

- **Direct Layout Generation:** Instantly provisions a new post draft pre-populated with a 1/3 and 2/3 asymmetric Avia grid structure upon clicking the sidebar menu shortcut.
- **Automated Mode Enforcement:** Utilizes the custom Enfold template engine filters (`avf_builder_active`) to bypass the default classic editor view and load webmasters directly into the visual builder interface.
- **Configurable Settings UI:** Features a native settings dashboard page under **Settings > Newsletter Template** allowing administrators to dynamically specify which category is assigned to the drafts.
- **Pixel-Perfect Spacing:** Automatically registers exactly 5 asymmetric layout rows featuring a left-aligned portfolio image skeleton and a right-aligned heading configured with a strict `0px` top-margin override.
- **Native Localization Support:** Includes complete internationalization integration via translation domain schemas for seamless multilingual menu string rendering.

## Codebase Architecture

The plugin isolates administrative layouts and translation structures cleanly into specific modules:

```text
newsletter-template/
├── languages/
│   ├── newletter-automation-nl_NL.po  # Human-readable translation strings (Dutch)
│   └── newletter-automation-nl_NL.mo  # Compiled binary translation asset (Required for WP)
├── newsletter-template.php            # Main plugin file, handles boot routines and textdomain initialization
└── admin-settings.php                 # AJAX routes, Avia shortcode definitions, and backend administration settings
```

## Compilation of Translation Files

WordPress requires a binary `.mo` file to process translations efficiently. If you make modifications to the `newletter-automation-nl_NL.po` source file on a Mac, compile it via your terminal using the Homebrew `gettext` binary:

```bash
# 1. Install gettext utilities via brew
brew install gettext

# 2. Compile the updated .po file into the production-ready binary .mo format
msgfmt -o languages/newletter-automation-nl_NL.mo languages/newletter-automation-nl_NL.po
```

## Installation

1. Create a folder named `newsletter-template` under your site's directory path `/wp-content/plugins/`.
2. Move all core script assets and the compiled `languages/` sub-directory into it.
3. Access your WordPress Admin dashboard, navigate to **Plugins**, and click **Activate** next to **Newsletter Template**.

## Configuration & Deployment Workflow

1. Navigate to the newly activated panel under **Settings > Newsletter Template** (or *Nieuwsbrief Template Instellingen* in Dutch).
2. Choose your designated newsletter category from the **Target Category** dropdown menu and click **Save Settings**.
3. Instruct your webmasters to stop clicking *Posts > Add New*. Instead, they can simply choose the new localized sidebar menu item: **Berichten > Nieuwe nieuwsbrief**.
4. The system will handle the database draft preparation and automatically redirect them to an active visual Avia environment with the entire column framework ready for data insertion.

## License

This project is licensed under the GPL-2.0+ License.
