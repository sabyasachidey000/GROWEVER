# Designation menu permissions

Menu registration is centralized in the `menus` table. The existing sidebar remains a Blade-owned layout: it continues to define menu HTML, icons, ordering, and routes.

Each sidebar item calls `hasMenuPermission()` with its `menus.menu_key`. The same key is enforced by the `menu` middleware on protected routes. An unregistered key is denied and is logged as a development warning.

## Adding a future menu/module

1. Add the route, controller, and module as usual.
2. Add its static sidebar item in the desired location, wrapped with `@if(auth()->user()->hasMenuPermission('your_menu_key'))`.
3. Register `menu_key`, display name, section, route name, active flag, and sort order in the menu registry seeder (or create it through an approved administration flow).
4. Apply `menu:your_menu_key` middleware to the module's routes.
5. Run the migration/seeder in the target environment.

The Super Admin permission page queries active registered menus dynamically and groups them by section. New menu records therefore appear without any permission-page code change. Super Admins can access every active menu automatically; other designations receive access only after it is explicitly granted.
