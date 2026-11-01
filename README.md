# CGExtensions (maintained fork)

Calguy's Module Extensions: the shared PHP library and Smarty plugins (`{cge_*}`, `{cgjs_*}`, form, image, query, email and report helpers) that the other CG modules (CGSmartImage, CGSimpleSmarty, CGBlog, CGCalendar, CGFeedback, …) are built on.

This repository is a **community-maintained fork** of the CGExtensions module for
[CMS Made Simple](https://www.cmsmadesimple.org) (CMSMS), originally written by
Robert Campbell (*calguy1000*).

## Why this fork exists

The original author left the CMSMS project and the CG ("Calguy") modules will
not receive any further official releases. Many sites still depend on them, and
the last releases predate PHP 8: on current PHP they log large numbers of
deprecations, and some features stop with fatal errors.

Rather than patching the copy inside every site separately, the fixes live
here, in one place, so every site can use the same tested code and new fixes
only have to be made once.

## Compatibility

| | |
|---|---|
| Based on | CGExtensions **1.65.2** (final upstream release) |
| Tested with | CMS Made Simple 2.2.24, Smarty 4.5.5, **PHP 8.5.12**, AlmaLinux 9 / Apache 2.4 |
| Minimum CMSMS | 2.2.9 (unchanged from upstream) |
| Requires | None. |

## Version number

The module still reports version **1.65.2**, deliberately. If the version
in `moduleinfo.ini` / `GetVersion()` is raised, CMSMS treats the module as
needing an upgrade and **will not load it** until someone runs the upgrade in
Module Manager. Keeping the upstream version means the fork can be dropped
into an existing site with no database step. Fork changes are tracked by this
repository's commits (and tags, when made) instead.

## Installing / updating a site

The folder name must be exactly `CGExtensions`.

```sh
# new install of the fork into a site
cd /path/to/site/modules
mv CGExtensions CGExtensions.orig          # keep the old copy until verified, then delete it
git clone <this-repository-url> CGExtensions

# later updates
cd /path/to/site/modules/CGExtensions && git pull
```

After replacing or updating the files, clear the CMSMS cache
(Admin → Site Admin → System Maintenance → Clear Cache). Make sure the web
server does not serve the `.git` folder (e.g. block dot-directories in
`.htaccess`).

## Changes in this fork

The first commit is the unmodified upstream release; every later commit is one
fix:

- fix(CGExtensions): cast optional cgjs_require params to string before trim()
- fix(CGExtensions): declare cge_utils::$settings property
- fix(CGExtensions): explicit nullable types and canonical casts for PHP 8.4/8.5
- fix(CGExtensions): replace removed $str{n} string offsets that fatal on PHP 8
- fix(CGExtensions): cast remaining optional plugin params to string before trim()
- fix(CGExtensions): accept GdImage objects in watermarker (fatal on PHP 8)
- fix(CGExtensions): make {cge_cached_url} callable (typo and namespace)
- fix(CGExtensions): avoid ord('')/ord(null) at end of input in jsmin
- fix(CGExtensions): qualify global class names used inside namespaces
- fix(CGExtensions): match parent signature in data_report_generator::do_group_header
- fix(CGExtensions): mark ArrayAccess/Iterator/JsonSerializable methods #[\ReturnTypeWillChange]

See each commit message for the error it fixes and how it was verified.

## Known issues

- `lib/class.secure_settings.php` is unfinished upstream code that has never parsed on any PHP version. Nothing loads it at runtime (only `tests/`), so it is left as-is here.
- `_c1.dat` / `_d1.dat` are the upstream integrity checksums. CGExtensions' **Verify Modules** tool will therefore report the files this fork changed as modified; that is expected.

## Contributing

- One fix per commit, using [Conventional Commits](https://www.conventionalcommits.org)
  (`fix:`, `feat:`, `perf:` …), with the error message and how you tested it.
- Keep behaviour and rendered output unchanged unless the change is the fix.
- Test against the PHP versions in the table above (`php -l` is not enough:
  load the classes and exercise the feature, since many PHP 8 problems only
  appear at runtime).
- Don't change the version number (see above).

## Licence

GNU General Public License, version 2 or (at your option) any later version,
with the upstream author's additional condition, repeated from the source file
headers:

> However, as a special exception to the GPL, this software is distributed as
> an addon module to CMS Made Simple. You may not use this software in any Non
> GPL version of CMS Made simple, or in any version of CMS Made simple that does
> not indicate clearly and obviously in its admin section that the site was
> built with CMS Made simple.

Copyright © Robert Campbell (calguy1000) and contributors. The full GPL text is
in [`COPYING`](COPYING).
