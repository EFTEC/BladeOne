# Changelog — BladeOne

## 5.0.0 (2026-03)

Full list of changes applied to this working copy (baseline: upstream 4.19.1).
All bug fixes were **experimentally verified** before and after the change, and the full test suite
(84 tests / 189 assertions) passes on every step.

### Breaking changes
- **PHP >= 8.1 is now required** (was >= 7.4). Enforced in `composer.json`.
- Removed `ext-json` from `composer.json` requirements (JSON is a core extension since PHP 8.0).
- Version constant bumped `4.19.1` → `5.0.0` (class `VERSION` + docblock `@version`).
- Typed signatures on the developer-facing API: code passing wrong types to typed methods now fails
  with `TypeError` instead of being silently coerced/misbehaving.

### Bug fixes
- **`getInstance()` no longer fatals on first call** — `$instance` was a non-nullable typed static
  property without default, so `self::$instance === null` threw
  *"Typed static property … must not be accessed before initialization"*.
  Now `public static ?BladeOne $instance = null;`.
- **`@prepend` directive fixed (was doubly broken):**
  - `startPrepend()` used `array_unshift($this->pushStack[], $section)` (passing a non-variable →
    `TypeError: Argument #1 must be of type array, null given`). Now
    `array_unshift($this->pushStack, $section)` — correct LIFO pairing with `stopPrepend()`'s
    `array_shift` (nested `@prepend` now works).
  - `compilePrepend()` generated `$this->startPush(...)` (copy-paste bug). Now generates
    `$this->startPrepend(...)`.
- **`ipClient()` regex fixed** — pattern was `/^(d{1,3}).(d{1,3}).(d{1,3}).(d{1,3})$/` (literal `d`,
  unescaped dots), so `HTTP_X_FORWARDED_FOR` was never validated/returned. Now
  `/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/`.
- **`extract()` collisions fixed** — `evaluateText()` and `evaluatePath()` called
  `extract($variables)` without `EXTR_SKIP`; a view variable named `content` silently replaced the
  compiled code and `compiledFile` broke the include. Both now use `extract($variables, EXTR_SKIP)`
  (like `runString()` already did).
- **`compileForeach()` validates its expression** — a malformed `@foreach($x)` (no `as`) emitted
  PHP 8 warnings (`Undefined array key`) and silently generated broken code
  (`$__currentLoopData = ;`). Now it throws a clear `RuntimeException` via `showError()`.
- **Dead code removed** — pointless `static::last($this->componentStack);` call in `endSlot()`.
- **Nullable properties now have explicit `= null` defaults** — `$currentUser`, `$currentRole`,
  `$baseDomain`, `$canonicalUrl`, `$currentUrl` (prevents *"uninitialized typed property"* errors
  when read before being set, e.g. `getCurrentUser()` without `setAuth()`).
- **`isAbsolutePath()`** — added length guard for one-character paths on Windows (avoids
  `Undefined offset` warning reading `$path[1]`).

### Performance optimizations (measured, `php -n`, min of 5 runs)
- **Path memoization** — `getTemplateFile()`/`getCompiledFile()` are called several times per render
  (`compile`, `isExpired`, `evaluatePath`), each doing an `is_file()` loop + hash. Results are now
  cached per view name in `$templateFileCache`/`$compiledFileCache`:
  - Only **successful** template lookups are cached (a template created later is still found).
  - Invalidation: `setPath()` and `setFileExtension()` clear both caches via `clearPathCaches()`;
    `setCompiledExtension()` and `setCompileTypeFileName()` clear the compiled cache.
  - Measured: `run()` with cached compiles ≈ **8-9 % faster** (300 renders: ~24.5 ms vs ~26.8 ms).
- **`getEchoMethods()` cached** — was rebuilt + `uksort`ed with a closure **per HTML token**; now
  cached in `$echoMethodsCache` and invalidated in `setContentTags()` (rawTags have no setter).
- **Native string functions** — `mb_strpos` shims and `function_exists('mb_strpos')` checks removed
  from `startsWith()`/`contains()` in favor of native `str_starts_with()`/`str_contains()`.
  Together with the rest of the PHP 8 modernization: **compilation ≈ 23 % faster**
  (`compileString` ×30: ~16.4 ms vs ~21.5 ms).
- **Constructor** — iterates `class_uses($this)` instead of scanning every trait in the process
  (`get_declared_traits()`), same trait-"constructor" convention.
- **`$optimize` whitespace stripping** — two `preg_replace()` passes merged into one:
  `/^(?: {2,}|\t{2,})/m`.
- **`missingTranslation()`** — `filesize`+`fopen`+`fwrite`+`fclose` replaced by a single
  `file_put_contents(..., FILE_APPEND)` (with the 100 KB rewrite rule preserved).
- **Negative result (documented, do not retry):** caching `method_exists()` with a
  `get_class_methods()`-based per-class cache made compilation ~5 % **slower** (native
  `method_exists()` is already an O(1) C-level lookup; the `strtolower` normalization needed to keep
  case-insensitivity — required for `@endpushonce` → `compileEndpushOnce` — costs more than it
  saves). Reverted; the attempt is documented in `AI_GUIDE.md` §9.

### PHP 8.1+ modernization
- Native `str_contains()` / `str_starts_with()` / `str_ends_with()` replaced boolean `strpos()`
  checks in ~18 places: `wrapPHP`, `compileString`, `postRun`, `getTemplateFile`,
  `yieldPushContent`, `wildCardComparison`, `compileStatements`, `fixNamespaceClass`, `renderEach`,
  `setBaseUrl`, `getCurrentUrl(Calculated)`, `parseArgs`, …
  (`wildCardComparison` also uses `str_ends_with()` and negative string offsets).
- `match` expressions: `colorLog()`, `compileComments()`, `compileViewName()`.
- **First-class callable syntax (8.1):** `self::convertArgCallBack(...)` in `convertArg()`.
- **Arrow functions:** the three echo compilers (`compileRawEchos`, `compileRegularEchos`,
  `compileEscapedEchos`) and `restoreVerbatimBlocks()` callbacks.
- Negative string offsets: `$text[-1]` in `isQuoted()`, `$textWithWildcard[-1]`.
- Removed unnecessary `@` suppressions: `@explode('?', $link)[0]` → plain `explode()[0]` (always
  defined); `@parse_url($this->baseUrl)['host']` → safe `(\parse_url(...) ?: [])['host'] ?? null`.
- **Removed the `array_key_last()` polyfill** at the bottom of `BladeOne.php` (native since 7.3).
- ext-mbstring is **no longer used by the engine itself** (kept in `suggest` for consumers).

### Typed signatures (developer-facing API, ~40 methods)
- `__construct()`, `getInstance()`: `string|array|null $templatePath, ?string $compiledPath, int $mode, int $commentMode`.
- `run(?string $view, array $variables)`, `runString(string $string, array $data)`,
  `compileString(string $value)`, `compile(?string $templateName, bool $forced)`.
- `getTemplateFile(?string)`, `getCompiledFile(?string)`, `getFile(string)`, `isExpired(?string)`,
  `evaluatePath(string, array)`, `evaluateText(string, array)`,
  `runInternal(string, array, bool, bool)`, `evalComposer(string)`.
- `setPath()`, `setMode(int)`, `setIsCompiled(bool)`, `setOptimize(bool)`,
  `setFileExtension(string)`, `setCompiledExtension(string)`, `setPhpTag(string)`.
- `directive(string, callable)`, `directiveRT(string, callable)`,
  `registerIfStatement(string, callable)`, `check(string, ...)`.
- `showError(string, string, bool, bool)`, `setAuth(?string, ?string, ?array)` (nullable: tests
  legitimately pass `null` for "no user"), `share()/with(string|array, mixed)`,
  `composer(string|array|null, callable|string|object|null)`.
- `getCsrfToken(bool, string)`, `csrfIsValid(bool, string)`, `regenerateToken(string)`.
- `addInclude(string, ?string)`, `addAssetDict()/addAssetDictCDN(string|array, string)`,
  `relative(string)`, `dump(mixed, bool)`, `convertArg(array|string)`.
- `wrapPHP(?string, string, bool)`, `stripParentheses(?string)`, `isQuoted(?string)`,
  `isVariablePHP(?string)`, `addInsideQuote(string, string)`,
  `parseArgs(?string, string, string, bool)`.
- `e(mixed)`, `enq(mixed)`; `getCurrentUser()/getCurrentRole(): ?string` + nullable setters;
  `setCurrentPermission(?array)`; `setAliasClasses(array)`, `addAliasClasses(string, string)`.
- `wildCardComparison(string, ?string)`, `injectClass(string, ?string)`, `getArgs(?string)`.
- **Deliberately left untyped:** methods invoked from *compiled templates* with user expressions
  (`runChild`, `addLoop`, `startSection`, `startPush`, `_e`, `_ef`, `_n`, `format`,
  `includeWhen`, `includeFirst`, `renderEach`, `splitForeach`, …) so bad template input keeps
  producing the library's friendly errors instead of fatal `TypeError`s
  (e.g. `@foreach($undefinedVar as $x)`).

### Tooling / tests
- **PHPUnit upgraded `^8.5` → `^10.5`** (installed 10.5.65) via
  `composer update phpunit/phpunit --with-all-dependencies`.
- `phpunit.xml` **migrated to the PHPUnit 10 schema** (`--migrate-configuration`):
  `<filter><whitelist>` → `<source><include>`, `cacheDirectory=".phpunit.cache"`, removed
  attributes dropped in 10 (`verbose`, `convert*ToExceptions`, …).
- **No test code changes were required** for PHPUnit 10 — the suite passes as-is.
- `.gitignore`: added `.phpunit.cache/`.
- Removed stale artifacts: `phpunit.xml.bak`, `.phpunit.result.cache`.
- `README.md`: PHP badges updated to `php->=8.1` and `php-8.x`.

### Documentation
- **`AI_GUIDE.md` (new)** — AI-oriented engineering guide: repository map, the two-phase
  compile/runtime mental model, full render pipeline, directive resolution order, state-machine
  fields, modes, file resolution, extension points, error-handling conventions, fixed issues,
  performance notes (incl. the negative result) and testing conventions.
- `AI_GUIDE.md` updated for 5.0.0: PHP >= 8.1 requirements, PHPUnit 10.5, and a
  "PHP 8.1+ modernization notes" section (incl. the typing strategy).
- **This changelog** (`CHANGELOG.md`).

### Validation summary
- `php -l` clean on `lib/BladeOne.php`.
- PHPUnit: **84 tests / 189 assertions OK** (run on every change batch).
- 13-point smoke script: `getInstance()` first call, nested `@prepend`, `ipClient()` valid/invalid,
  `startsWith`/`contains`/`wildCardComparison`, friendly `run()` error, nullable defaults,
  `setAuth(null)`, `VERSION`, `commentMode` behavior — all OK.
- Benchmarks (`php -n`, min of 5): `compileString` ×30 ≈ 16.4 ms (was ~21.5 ms),
  cached `run()` ×300 ≈ 24-27 ms (was ~26.8 ms).


