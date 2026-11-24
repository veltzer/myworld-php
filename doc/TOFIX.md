# TOFIX

Findings from a code scan on 2026-10-04.

## High

- `mytheme/search.php:5` - reflected XSS: the search term `$_GET['s']` is echoed into the page unescaped (also `mytheme/search.php:27` and `mytheme/archive.php:51`, where it is additionally passed through `_e()` as a translation string). Use `get_search_query()` / `esc_html()`.
- `myworld/sa/GetRsBlob.php:9` - SQL injection: `$_GET['slug']` is interpolated straight into the query with `sprintf('... where slug="%s"', ...)` (line 14-16). Escape it with `my_mysql_real_escape_string` or use a prepared statement.
- `myworld/sa/GetBlob.php:98-102` - the "security" whitelist for the table and column names that are spliced unescaped into the SQL (lines 105-113) is implemented with `assert()`. PHP 8 defaults to `zend.assertions=-1` in production (also the value here), so the asserts are compiled out and any table/column can be read. Replace with explicit `if (...) { error(...); }` checks.
- `myworld/src/utils.php:24` - `assert_options(ASSERT_QUIET_EVAL, 0)` uses a constant removed in PHP 8.0 (`defined("ASSERT_QUIET_EVAL")` is false on the installed PHP 8.5), so `assert_setup()` - called from `utils_init()` - dies with "Undefined constant". `assert_options()` itself is deprecated since 8.3. Drop the assert_options block and use explicit error handling.
- `myworld/src/utils.php:89` - `assert($line!=NULL)` tests an undefined `$line` instead of `$link`, so the connection result is never checked (and the assertion always fails when assertions are on). Check `$link` and report `mysqli_connect_error()`.
- `myworld/sa/GetEvents.php:71` - calls `mysql_query`, and `myworld/sa/GetRsBlob.php:24` calls `mysql_num_rows`; the `mysql_*` extension was removed in PHP 7, so both endpoints fatal. Use the `my_mysql_query` wrapper / `$result->num_rows` like the rest of the code.

## Medium

- `myworld/src/utils.php:97` - side effects inside `assert()`: `$link->set_charset(...)` (line 97), `$link->select_db('wordpress')` (line 120) and `$handle=fopen(...)` (line 269) are never executed when assertions are disabled (`zend.assertions=-1`), so the connection silently stays latin1, the WordPress DB is not reselected and the logger writes to a null handle. Move the calls out of `assert()`.
- `private/.htpasswd:1` - a public repo carries an htpasswd entry for user `mark` with a 13-char traditional DES crypt hash, which is trivially crackable. Remove the file from the repo (and history) and rotate that password if it is still used anywhere.
- `src/myworld_download.py:15-16` - imports `download.generic` and `download.ted`, which exist neither in this repo nor in `pyproject.toml` dependencies; the mypy override `download.*` in `pyproject.toml:33` hides the dead import. Restore/declare the module or delete the script. Likewise `MediaInfoDLL3` (`src/myworld_update_length.py:14`) is undeclared and silenced by the same override list.
- `rsconstruct.toml:27-32` - ruff and mypy run on `src`, `scripts` and `config` but not on `python/`, where the shared `myworld` package (`python/myworld/db.py`, `menu_maker.py`, `utils.py`) lives, even though `pyproject.toml:26` puts `python` on the mypy path. Add `python` to both `src_dirs`.
- `.oxlintrc.json:61` - `no-undef` is turned off for the whole tree to accommodate library globals. Per the lint policy, declare the globals each file really uses (`/* global jQuery, Ext */`) and turn the rule back on.

## Low

- `myworld/sa/GetRsBlob.php:3-4` - the usage comment is copied from GetBlob.php and documents GetBlob.php parameters (`table`, `id`, `field`, ...) on a veltzer.org URL, while this script only takes `slug`. Update the comment.
- `rsconstruct.toml:37` - shellcheck `src_dirs` includes `misc`, which only holds `rss.png`; drop it.
