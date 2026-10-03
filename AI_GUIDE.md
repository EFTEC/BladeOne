# BladeOne — AI Engineering Guide

> This document is written for AI coding agents (and humans) that need to **understand, extend, debug or
> optimize** the BladeOne codebase quickly and safely. It reflects the architecture of `lib/BladeOne.php`
> version **5.0.0** (PHP 8.1+ only).

## 1. What is BladeOne?

BladeOne is a **standalone, single-file implementation of Laravel's Blade template engine** (no Laravel
required). It compiles `.blade.php` templates into plain PHP files (`.bladec`) and executes them.

- **Namespace:** `eftec\bladeone`
- **Class:** `BladeOne` (single god-class by design; keep it that way — it's the library's selling point)
- **Requires:** PHP >= 8.1 (composer enforces it). No other extensions required (json is core since PHP 8.0).
- **License:** MIT (author attribution comment must be preserved)

### PHP 8.1+ modernization notes (v5.0.0)

- Native `str_contains()` / `str_starts_with()` / `str_ends_with()` replaced every `strpos()` boolean
  check **and** the old mb-string shims in `startsWith()`/`contains()` (ext-mbstring is no longer used
  by the engine itself).
- `match` expressions in `colorLog()`, `compileComments()`, `compileViewName()`.
- First-class callable syntax (`self::convertArgCallBack(...)`) in `convertArg()`.
- Arrow functions in the echo compilers and verbatim restore callbacks.
- Typed signatures on the developer-facing API (constructor, `getInstance`, `run`, `compile`,
  `share`, `composer`, `directive`, `setAuth`, `showError`, path getters/setters, …).
  **Deliberately left untyped:** methods invoked from *compiled templates* with user expressions
  (`runChild`, `addLoop`, `startSection`, `_e`, `format`, …) so bad template input keeps producing
  friendly errors instead of `TypeError`.
- Nullable properties have explicit `= null` defaults (`$currentUser`, `$currentRole`, `$baseDomain`,
  `$canonicalUrl`, `$currentUrl`) to avoid "uninitialized typed property" errors.
- The `array_key_last()` polyfill was removed (native since PHP 7.3).

### Repository map

| Path | Purpose |
|---|---|
| `lib/BladeOne.php` | The entire engine (compile + runtime). **Main file.** |
| `lib/BladeOneCustom.php` | Example trait showing the extension pattern (compile + runtime methods) |
| `lib/BladeOneCache.php` | Optional caching trait (file/redis cache of rendered output) |
| `lib/bladeonecli` | CLI entry point (`php bladeonecli -createfolder -check -clearcompile`) |
| `tests/` | PHPUnit 8.5 test suite + fixture templates in `tests/resources/templates/` |
| `docs/` | Images only |
| `examples/` | Runnable usage examples |

## 2. Core mental model: two phases

Everything in BladeOne belongs to one of two phases. **Never mix them up when editing code.**

### Phase A — Compile time (`compile*` methods)
Runs only when a template changed. Transforms Blade syntax into PHP code **as strings**.
All methods named `compile<Directive>()` belong here (e.g. `compileIf`, `compileForeach`). They receive
the directive *expression* as text and return a string of PHP/HTML. They must be **pure string
manipulation** — no I/O of the rendered result, no runtime state.

### Phase B — Runtime (`run*` / helpers without `compile` prefix)
Runs on every request. The compiled `.bladec` file is `include`d inside `evaluatePath()` with the view
variables `extract`ed into local scope. Compiled templates call runtime helpers via `$this->...`
(e.g. `$this->runChild(...)`, `$this->incrementLoopIndices()`).

### The pipeline

```
run($view, $vars)
 └─ runInternal($view, $vars, $forced, $runFast)
     ├─ evalComposer($view)                  // composer() callbacks, wildcard match
     ├─ merge variablesGlobal (share())      // into $this->variables
     ├─ compile($view, $forced)              // only if !$runFast
     │   ├─ getCompiledFile()  getTemplateFile()   // path resolution (is_file loop + sha1/md5)
     │   ├─ isExpired()        // filemtime compare (MODE_AUTO)
     │   ├─ compileString(getFile($template))      // ← the tokenizer pipeline
     │   │    ├─ storeVerbatimBlocks()             // @verbatim…@endverbatim → placeholder
     │   │    ├─ token_get_all() → parseToken()    // PHP tokenizer splits HTML vs PHP
     │   │    │    per T_INLINE_HTML runs, in order:
     │   │    │      compileExtensions → compileComponents → compileStatements
     │   │    │      → compileComments → compileEchos
     │   │    └─ restoreVerbatimBlocks() + footer (@extends appended)
     │   ├─ compileCallBacks()               // user compileCallbacks (by ref)
     │   └─ file_put_contents($compiled)
     └─ evaluatePath($compiledFile, $vars)   // ob_start + extract + include
         └─ postRun()                        // resolves deferred @stack markers
```

`runChild()` = `runInternal()` + backup/restore of `variables`, `controlStack`, `sectionStack`,
`loopsStack` (used by `@include`, `@each`, components).

## 3. Directive resolution order (critical!)

When `compileStatements()` meets `@something`, it resolves in **this exact order**:

1. `@@escaped` → literal `@` output.
2. `@Class::method` (contains `::`) → `compileStatementClass()` → echoed PHP call
   (class alias resolved through `$aliasClasses` / `fixNamespaceClass()`).
3. `$this->customDirectivesRT[$name]` exists:
   - `true` (registered via `directiveRT()`) → `compileStatementCustom()` → `call_user_func` at runtime.
   - `false` (registered via `directive()`) → handler executed **at compile time**, returns string.
4. `$this->methods['compile'.Name]` (added via `addMethod('compile', …)`) → callable invoked.
5. `\method_exists($this, 'compile'.Name)` → native compiler method (the big family of `compile*`).
6. `$this->methods['runtime'.Name]` → `autoruntime()` wraps it as `$this->methods['name']([...])`.
7. `\method_exists($this, 'runtime'.Name)` → `autoruntime()` wraps `$this->name([...])`.
8. Otherwise → left untouched (plain text).

`autoruntime()` parses the expression with `parseArgs()` (space-separated `key=value` pairs) and builds
an array literal `'k'=>$v` to pass to the runtime method.

## 4. Echo tags and escaping

Three tag families, compiled longest-first (see `getEchoMethods()`):

| Tags | Method | Output |
|---|---|---|
| `{!! ... !!}` | `compileRawEchos` | raw echo, no escaping |
| `{{{ ... }}}` | `compileEscapedEchos` | `echoFormat` applied |
| `{{ ... }}` | `compileRegularEchos` | `echoFormat` applied (default: `\htmlentities(...)`) |

- `$this->echoFormat` (default `'\htmlentities(%s??\'\', ENT_QUOTES, \'UTF-8\', false)'`) is the single
  point to change escaping strategy. `static::e()` / `static::enq()` are the helper escape functions.
- `$x or default` syntax → rewritten to `isset($x) ? $x : default` (`compileEchoDefaults()`).
- Pipes (`{{ $v|strtolower }}`) only when `$pipeEnable === true` (`pipeDream()`).
- `@{{` prefix escapes a tag (the `(@)?` group in echo regexes).

## 5. State machine fields (what holds what)

| Property | Used by | Notes |
|---|---|---|
| `$variables` / `$variablesGlobal` | runtime | `share()`/`with()` write to globals; merged in `runInternal` |
| `$sections` + `$sectionStack` | `@section/@yield` | `extendSection()` handles `@parent` via `$PARENTKEY` marker |
| `$pushes` + `$pushStack` | `@push/@stack` | keyed by section + `renderCount`; `@stack` resolved **after** render via `postRun()` and `escapeStack0/1` markers |
| `$loopsStack` | `$loop` var | `addLoop/incrementLoopIndices/popLoop`; `splitForeach()` reads it |
| `$componentStack/$componentData/$slots/$slotStack` | `@component` + `<x-…>` | index = `count(stack)-1`; **note** `renderComponent()` pops the name *before* `componentData()` reads index — indexes still match |
| `$controlStack` (+ `$controlStackParent`) | custom control tags (BladeOneHtml) | tree encoded as parent indexes |
| `$footer` | `@extends` | compiled `runChild` appended at end of template |
| `$customDirectives` (+ `$customDirectivesRT` flags) | `directive()/directiveRT()` | |
| `$methods` | `addMethod()` | keys: `compileName` / `runtimeName` |
| `$conditions` | `BladeOne::if()` (`__call('if',…)`) | used by `check()` |

### Modes (`$mode`, or `BLADEONE_MODE` constant)

| Constant | Behaviour |
|---|---|
| `MODE_AUTO` (0) | compile only if template newer than compiled file (filemtime) |
| `MODE_SLOW` (1) | always recompile (`$forced`) |
| `MODE_FAST` (2) | never compile/check, run existing `.bladec` directly (`$runFast`) |
| `MODE_DEBUG` (5) | recompile + verbose errors |

`run()` derives: `$forced = ($mode & 1)`, `$runFast = ($mode & 2)`.
`setIsCompiled(false)` switches to in-memory execution (`evaluateText` via `eval`) instead of files.

### File resolution

- Templates: `folder.template` → `<templatePath>/folder/template.blade.php`
  (`$fileExtension`). `$templatePath` is an **array** — `locateTemplate()` scans in order.
- Compiled: `<compiledPath>/<basename>_<sha1|md5(fullPath)><.bladec>` (`$compileTypeFileName`).
- `setOptimize(true)` strips leading multi-spaces/tabs of compiled output (default on).

## 6. Extension points (how you're supposed to customize)

1. **Compile-time directive:** `$blade->directive('name', fn($expr) => string)` — output injected into
   compiled file.
2. **Runtime directive:** `$blade->directiveRT('name', fn($args) => ...)` — executed when the compiled
   template runs.
3. **Custom compiler/runtime methods:** `$blade->addMethod('compile'|'runtime', 'name', $callable)`
   → resolved as `@name`/`@endname`-style tags (see order in §3).
4. **Hook compiled output:** `$blade->compileCallbacks[] = function (&$content, $templateName) {...}`
   and `$blade->extend($compiler)` (runs first, in `compileExtensions`).
5. **View composers:** `$blade->composer('folder.*', $fnOrClassOrInstance)` — runs before render,
   wildcard `*` supported at edges (`wildCardComparison`).
6. **Auth callbacks:** `setCanFunction/setAnyFunction/setErrorFunction`, `setAuth()`.
7. **Trait "constructors":** any trait `use`d by a subclass whose **short name matches a method name**
   gets that method auto-called in the constructor (convention: `trait BladeOneHtml` → method
   `BladeOneHtml()`). See constructor loop over `class_uses()`.
8. **Includes aliasing:** `addInclude('folder.view', 'alias')` → creates `@alias([...])`.
9. **Conditional directives:** `BladeOne::if('admin', fn() => ...)` → `@admin/@elseadmin/@endadmin`.

## 7. Error handling conventions

- `showError($id, $text, $critic, $alwaysThrow)`: prints red HTML box and returns; **throws
  RuntimeException** if `$throwOnError || $alwaysThrow || $critic`; `die(1)` if `$critic` after echo.
- `showError` starts with `\ob_get_clean()` — it discards one output buffer. Be careful where you call it.
- Compile methods return error *strings* from `showError()` (they can't throw non-critically).
- `$blade->throwOnError = true` converts everything to exceptions (used by `ThrowTest`).

## 8. Recently fixed issues (this working copy, 2026-03)

These bugs were **experimentally confirmed and fixed** in this working copy. If you are working
against an upstream release ≤ 4.19.1, they may still be present:

1. **`getInstance()` fataled on first call** — `public static BladeOne $instance;` was non-nullable
   typed with no default → *"Typed static property must not be accessed before initialization"*.
   Fixed as `public static ?BladeOne $instance = null;`.
2. **`@prepend` was doubly broken** — (a) `startPrepend()` used
   `array_unshift($this->pushStack[], $section)` (passing a non-variable → `TypeError`);
   now `array_unshift($this->pushStack, $section)` (pairs with `stopPrepend()`'s `array_shift`,
   correct nesting). (b) `compilePrepend()` generated `$this->startPush(...)` (copy-paste);
   now generates `$this->startPrepend(...)`.
3. **`ipClient()` regex missed escapes** — pattern used `d{1,3}` and `.` instead of `\d{1,3}` and
   `\.`, so `HTTP_X_FORWARDED_FOR` was never validated/returned. Fixed.
4. **`extract()` without `EXTR_SKIP`** in `evaluateText()`/`evaluatePath()` — a view variable named
   `content` silently replaced the compiled code, and `compiledFile` broke the include.
   Both now use `EXTR_SKIP` (like `runString()` already did).
5. **`compileForeach()` with malformed expression** (no `as`) emitted PHP 8 warnings and silently
   generated broken code. Now it throws a clear `RuntimeException` via `showError()`.
6. Dead call `static::last($this->componentStack);` removed from `endSlot()`.
7. Constructor now iterates `class_uses($this)` instead of `get_declared_traits()`
   (same trait-"constructor" convention, no global scan).
8. `missingTranslation()` uses `file_put_contents(..., FILE_APPEND)` instead of
   fopen/fwrite/fclose.

## 9. Performance notes (hot paths)

Implemented in this working copy (measured with `php -n`, min of 5 runs, Windows):

- **Path memoization** — `getTemplateFile()`/`getCompiledFile()` results are cached per view name
  (`$templateFileCache`/`$compiledFileCache`), because they are called several times per render
  (`compile`, `isExpired`, `evaluatePath`). Only *successful* template lookups are cached, so a
  template created later is still found. Invalidation: `setPath()`/`setFileExtension()` (both
  caches via `clearPathCaches()`), `setCompiledExtension()`/`setCompileTypeFileName()` (compiled
  cache). Measured: `run()` with cached compiles ≈ **8-9 % faster** (300 renders: ~24.5 ms vs
  ~26.8 ms).
- **`getEchoMethods()` cache** — the array was rebuilt + `uksort`ed per HTML token; now cached in
  `$echoMethodsCache` and invalidated in `setContentTags()` (rawTags have no setter).
- **`startsWith()`/`contains()`** cache `function_exists('mb_strpos')` in a static var.
- **`optimize`** strips leading spaces/tabs with a single `preg_replace('/^(?: {2,}|\t{2,})/m')`
  instead of two passes.
- **Negative result (do not retry):** replacing `method_exists()` with a
  `get_class_methods()`-based per-class cache made compilation ~5 % *slower* (PHP's native
  `method_exists` is already an O(1) C-level lookup; the `strtolower` normalization needed to keep
  it case-insensitive costs more than it saves). Reverted.
- `MODE_FAST` + `@includeFast` (`compileIncludeFast` inlines compiled children) remain the intended
  production speedups; `BladeOneCache` trait caches the final rendered output.


## 10. Working on this repo

### Run tests

```bash
php vendor/phpunit/phpunit/phpunit --no-coverage     # from repo root
```

Composer platform requirement is `php >= 8.1`.

- PHPUnit ^10.5 (PHP 8.1+), bootstrap `tests/bootstrap.php` (composer autoloader or fallback PSR-4).
  The configuration is the PHPUnit 10 schema (`phpunit.xml`, cache in `.phpunit.cache/`).
- Fixture templates: `tests/resources/templates/**` (naming `folder/name.blade.php` → view
  `folder.name`). Compiled output goes to `tests/resources/compiled/`.
- Tests instantiate `new BladeOne(TEMPLATE_PATH, COMPILED_PATH, BladeOne::MODE_DEBUG)` in
  `AbstractBladeTestCase` (constructor, not setUp).
- `assertEqualsIgnoringWhitespace()` helper is the norm for output comparisons.
- CLI side effects: some tests print and delete `.bladec` files — that's expected.

### Conventions checklist for edits

- [ ] New directive compiler → `protected function compile<Name>($expression): string`, name must match
      `@name` / `@endname` (`ucfirst` applied at lookup). Keep it string-in/string-out.
- [ ] Runtime helper called from compiled templates must be **public** (compiled files are included in
      a method scope but call `$this->`).
- [ ] Use `$this->phpTag` / `$this->phpTagEcho` to open PHP tags — never raw `<?php` (short-tag support).
- [ ] Registering a tag pair? Follow `compilePushOnce`/`compileEndpushOnce` examples.
- [ ] Anything appended to template end (like `@extends`) goes to `$this->footer[]`, not inline.
- [ ] Don't break the public API — this is a widely consumed library (packagist `eftec/bladeone`).
      Additive changes only in minor versions.
- [ ] Run the full test suite; add a test in `tests/` for any new directive.
- [ ] Keep `@noinspection` headers and license comment block at the top of `lib/BladeOne.php`.

### Related docs in this repo

- `README.md` — full user documentation (directives reference).
- `BladeOneCache.md`, `BladeOneHtml.md`, `BladeOneLang.md`, `BladeOneLogic.md` — companion packages.



