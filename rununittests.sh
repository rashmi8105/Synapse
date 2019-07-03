#!/bin/bash

set -e

# Run by Bamboo continuous builds.
# Only runs unit tests, no environment stand-up needed.
# Generates report.xml, coverage.xml, and coverage directory in tests/_output.

# Environment where the schema changes are ran.  For unit testing this should be unit.
environment=test

# Run the tests
bin/codecept run unit \
    --coverage \
    --coverage-xml \
    --coverage-html \
    --xml

cp tests/_output/coverage.xml coverage-clover.xml
