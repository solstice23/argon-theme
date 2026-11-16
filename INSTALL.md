# Argon Theme Installation Guide

## For End Users (Installing from Release)

### Option 1: Install via WordPress Admin (Recommended)

1. Download the latest release `.zip` file from [GitHub Releases](https://github.com/solstice23/argon-theme/releases)
2. In WordPress admin panel, go to **Appearance → Themes → Add New**
3. Click **Upload Theme** and select the downloaded `.zip` file
4. Click **Install Now** and then **Activate**

The release package already includes all necessary dependencies pre-installed.

### Option 2: Manual Installation via FTP

1. Download the latest release `.zip` file from [GitHub Releases](https://github.com/solstice23/argon-theme/releases)
2. Extract the zip file
3. Upload the extracted `argon-theme` folder to your WordPress installation at:
   ```
   /wp-content/themes/
   ```
4. In WordPress admin panel, go to **Appearance → Themes**
5. Find Argon theme and click **Activate**

## For Developers (Installing from Source)

If you're installing directly from the GitHub repository (not from a release), you need to install PHP dependencies using Composer:

### Prerequisites

- PHP 7.4 or higher
- Composer (PHP dependency manager)

### Installation Steps

1. **Clone the repository:**
   ```bash
   cd /wp-content/themes/
   git clone https://github.com/solstice23/argon-theme.git argon
   cd argon
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install --no-dev --optimize-autoloader
   ```
   
   This will download and install all required PHP packages into the `vendor/` directory.

3. **Activate the theme:**
   - In WordPress admin panel, go to **Appearance → Themes**
   - Find Argon theme and click **Activate**

### Important Notes

- The `vendor/` directory is **NOT** included in the Git repository (it's in `.gitignore`)
- You **MUST** run `composer install` after cloning from source
- Release packages already have the `vendor/` directory pre-installed

## Troubleshooting

### Error: "Failed to open stream: No such file or directory" (vendor/composer/...)

This error occurs when the `vendor/` directory is missing or incomplete. To fix:

1. Make sure you have Composer installed:
   ```bash
   composer --version
   ```

2. Navigate to the theme directory:
   ```bash
   cd /path/to/wordpress/wp-content/themes/argon
   ```

3. Install dependencies:
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

4. Verify the installation:
   ```bash
   php -r "require 'vendor/autoload.php'; echo 'Success!\n';"
   ```

### Error: "Could not authenticate against github.com"

If you encounter authentication errors during `composer install`:

1. Composer will try to download from cache first
2. If cache is available, the installation should complete successfully
3. If not, you may need to create a GitHub personal access token:
   - Visit: https://github.com/settings/tokens
   - Create a token with `public_repo` access
   - Run: `composer config -g github-oauth.github.com YOUR_TOKEN`

## For Theme Packagers

When creating a release package:

1. Clone the repository
2. Run `composer install --no-dev --optimize-autoloader`
3. Remove development files (`.git`, `.github`, etc.)
4. Create a zip file of the entire theme directory **including the `vendor/` folder**

The `vendor/` directory **must** be included in release packages so end users don't need to run Composer.

## System Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- MySQL 5.6 or higher / MariaDB 10.1 or higher

## Support

- Documentation: https://argon-docs.solstice23.top/
- Issues: https://github.com/solstice23/argon-theme/issues
- Discussions: https://github.com/solstice23/argon-theme/discussions
