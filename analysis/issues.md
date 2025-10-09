# Repository Review Tasks

## Typo Fix

- **Location:** `resources/README.md`
- **Issue:** The word "necessáriamente" is misspelled twice; the correct spelling is "necessariamente" (without the accent on the second "a").
- **Suggested Task:** Update both instances to use the correct spelling to avoid confusion in the developer documentation.

## Bug Fix

- **Location:** `Makefile`
- **Issue:** The `test-pint` target ends with `@unset XDEBUG_MODE=off`, which attempts to unset a variable named `XDEBUG_MODE=off` instead of removing the `XDEBUG_MODE` environment variable. This causes a shell error when the target runs.
- **Suggested Task:** Change the line to `@unset XDEBUG_MODE` so the temporary environment variable is correctly cleaned up after running Pint in test mode.

## Comment/Documentation Discrepancy

- **Location:** `Makefile`
- **Issue:** The `env-down` target is documented with the description "Start the development environment", but the command actually stops the Docker environment.
- **Suggested Task:** Update the description to state that the target stops (or tears down) the development environment, keeping the help output accurate.

## Test Improvement

- **Location:** `tests/Feature/ExampleTest.php`
- **Issue:** The feature test only asserts the response status code for the home page, missing checks that the expected view content is rendered. This leaves regressions in the Blade layout undetected.
- **Suggested Task:** Extend the test to assert the presence of key UI elements (e.g., the sidebar component or specific headings) in the response body to better guard against accidental template changes.
