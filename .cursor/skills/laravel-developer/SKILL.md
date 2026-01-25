---
name: laravel-developer
description: Applies Laravel (Blade-first + API-first) conventions for implementing features, refactoring safely, improving performance, and hardening security. Use when working on Laravel controllers, routes, Blade views/forms, Eloquent models/queries, migrations, validation/authorization, API endpoints/resources, caching/queues, or test automation (including Dusk).
---

# Laravel Developer

## Quick start

When responding to Laravel tasks in this repo:

1. **Detect project reality first**
   - Check `composer.json` for Laravel/PHP versions and tooling.
   - Check `routes/` and existing patterns before introducing new structure.
   - Prefer consistency with existing conventions over “ideal architecture”.

2. **Prefer Laravel-native patterns**
   - Validation: `FormRequest` over manual validation where appropriate.
   - Authorization: Policies/Gates (`authorize()`, `can()`, `@can`) over ad-hoc checks.
   - Eloquent: scopes, relationships, eager loading, pagination.
   - Views: Blade components/partials, `old()`, `@error`, `@csrf`.
   - API: Resources, consistent status codes, predictable error payloads.

3. **Be careful with security and data integrity**
   - Never trust request input; validate + authorize.
   - Watch mass assignment, file uploads, and raw queries.
   - Keep DB changes reversible (migrations), and avoid data loss.

## Default assumptions (auto-adjust if repo differs)

- **Framework**: Laravel (detect actual version from `composer.json` before giving version-specific advice)
- **UI**: Blade-first (server-rendered forms, partials, components)
- **API**: API-first where applicable (Resources, consistent HTTP semantics)
- **Tooling**: Pint if present, PHPUnit if present, Dusk awareness when requested

## Implementation workflow (do this in order)

- **Clarify the surface area**
  - What routes/endpoints are impacted? (`routes/web.php`, `routes/api.php`, or other route files)
  - What views? (`resources/views/**`)
  - What models/tables? (`app/Models/**`, `database/migrations/**`)

- **Make changes in the “Laravel order”**
  - Routes → Controller → FormRequest/Policy → Model/Query → View/Resource → Tests

- **Add safety rails**
  - Use transactions for multi-step writes.
  - Add DB constraints/indexes where appropriate.
  - Prefer idempotent jobs/commands.

## Blade-first guidelines (forms + UX)

When editing Blade forms:

- **Always include CSRF**: `@csrf`
- **Preserve user input**: `value="{{ old('field', $model->field ?? '') }}"`
- **Show validation errors**: `@error('field') ... @enderror`
- **Use method spoofing when needed**: `@method('PUT')`, `@method('PATCH')`, `@method('DELETE')`
- **Use named routes**: `route('name', [...])` instead of hard-coded URLs
- **Accessibility**: label controls, use `aria-describedby` for help/error text, ensure focus styles

## API-first guidelines (controllers + responses)

For API endpoints:

- **Validate + authorize early**
  - Prefer `FormRequest` and `authorize()` in the request/policy.
- **Return consistent responses**
  - Use JSON Resources for success payloads.
  - Use meaningful status codes:
    - 200/204 read/update
    - 201 create
    - 422 validation
    - 403 forbidden
    - 404 not found
- **Pagination**
  - Prefer `paginate()` over `get()` for unbounded lists.
- **Avoid leaking internals**
  - Don’t return raw exception messages; rely on Laravel’s error handling.

## Eloquent + performance guidelines

Optimize for correctness first, then performance:

- **Prevent N+1 queries**
  - Use `with()` / `load()` and verify relationship usage in views/resources.
- **Query efficiency**
  - Select only needed columns when payload is large (`select([...])`).
  - Prefer `exists()` over `count()` for boolean checks.
  - Use `chunkById()` for large backfills/exports.
- **Indexes**
  - Add indexes for common filters/sorts/joins; prefer composite indexes for multi-column filters.
- **Caching**
  - Cache stable reads with clear keys and invalidation rules.
  - Avoid caching user-specific data without scoping keys.

## Security checklist (apply to every change)

- **Validation**: enforce types, bounds, formats; whitelist allowed values.
- **Authorization**: use policies/gates; enforce at controller/service boundaries.
- **Mass assignment**: ensure `$fillable`/`$guarded` is intentional.
- **XSS**: default to escaped output (`{{ }}`); use `{!! !!}` only for trusted, sanitized HTML.
- **File uploads**: validate mime/size, store outside public when sensitive, randomize names.
- **Secrets**: never commit `.env` or keys; don’t log sensitive payloads.

## Testing guidance

- **Prefer Feature tests** for request/response behavior, validation, authorization, and DB writes.
- **Use factories** and explicit assertions (DB + response + side effects like emails/jobs).
- **Dusk** (when UI flows matter):
  - Use for critical Blade form flows and regressions that unit/feature tests can’t cover.
  - Keep selectors stable (data attributes) to reduce flaky tests.

## Commands (suggest when useful)

- Format: `./vendor/bin/pint`
- Tests: `php artisan test`
- Routes: `php artisan route:list`
- Tinker: `php artisan tinker`

