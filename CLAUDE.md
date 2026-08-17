# create-wp-plugin

Interactive Node.js CLI (`index.js`) that scaffolds production-ready WordPress plugins from templates in `templates/`. See `README.md` for user-facing usage/features.

## Key facts

- **GitHub repo name**: `create-wp-plugin` (unchanged).
- **npm package name**: `create-wp-plugin-cli` — the plain name `create-wp-plugin` was already taken on the npm registry by an unrelated package, so it was published under this name instead. `package.json`'s `name`/`bin` both reflect this.
- Default/production branch is `main`. Always branch off `main` before committing — never commit directly to `main`.
- Tests: `node --test` (runs `tests/generator.test.js`, 20+ regression tests). Deeper end-to-end check: `bash scripts/verify.sh` (scaffolds 3 fixtures, runs `php -l`, `composer install/lint/test`, checks for unreplaced `{{TOKENS}}`).
- On Windows, `composer install` frequently fails the first 1-3 times with "Could not delete ... This can be due to an antivirus or the Windows Search Indexer locking the file" — this is a transient AV/indexer file lock, not a real bug. Just retry `composer install` again (often needs 2-3 attempts).

## Release workflow (bug fix or new feature)

1. **Branch off `main`**, make the code change, run `node --test` (and ideally `scripts/verify.sh` for anything touching generated-plugin templates — real `composer lint`/`test`/`npm run build` catch things unit tests can't).
2. **Merge the branch into `main`** and push. (A PR can be opened via `gh pr create`, but the user has also directly fast-forward-merged into local `main` and pushed when GitHub's PR-merge UI was flaky — either is fine; ask if unsure.)
3. **Bump the version** in `package.json` following semver:
   - Bug fix → patch (`1.0.0` → `1.0.1`)
   - New backward-compatible feature → minor (`1.0.0` → `1.1.0`)
   - Breaking change → major (`1.0.0` → `2.0.0`)
4. **Publish to npm.**

### Division of labor — important

Claude's Bash tool runs in a sandbox **isolated from the user's real machine/npm login**. `npm whoami` inside this sandbox always reports "not logged in" even after the user runs `npm login` in their own terminal — there is no shared `.npmrc`/auth token between the two. This means:

- Claude **can**: edit code, run tests, `git commit`/`push`, edit `package.json`'s version field, run `npm pack --dry-run` to sanity-check the tarball contents.
- Claude **cannot**: run `npm login` or `npm publish` — these must be run by the user in their own terminal. Give them the exact commands:
  ```bash
  cd d:\create-wp-plugin
  npm version patch   # or minor/major — bumps package.json + creates a git tag
  npm publish
  ```
  If 2FA is enabled on their npm account, `npm publish` will prompt for an OTP from their authenticator app.
- After they report success, verify with `npm view create-wp-plugin-cli` (readable without login) to confirm the new version is live.

## Related memory

See `[[create-wp-plugin-generator-status]]` in Claude's persistent memory for a running summary of what's shipped in this generator (React admin scoping, Interactivity API module, full WooCommerce integration, etc.) and open follow-ups.
