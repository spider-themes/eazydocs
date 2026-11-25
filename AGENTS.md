# EazyDocs Free

- Start at [the development guide](docs/development.md) for the feature map, setup, checks, and release asset layout.
- Free supports PHP 8.1+. Use WordPress hooks and the existing Composer namespace for plugin-owned PHP classes. Check the sibling `../eazydocs-pro` when touching a shared feature or interface.
- Treat hook names and priorities, REST and AJAX request/response shapes, option and post meta keys, and template override paths as compatibility contracts. Check permissions and visibility in the normal WordPress entry point.
- Edit SCSS under `assets/scss/` and block or Builder code under `src/`. See the asset table for tracked CSS. Rebuild generated `build/` files with `npm run build`.
- Run `bash scripts/check.sh` after installing dependencies. Use a disposable WordPress site for database or browser checks.
