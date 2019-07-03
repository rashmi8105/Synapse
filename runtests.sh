#!/bin/bash

# Run by Bamboo continuous builds.
# Generates report.xml, coverage.xml, and coverage directory in tests/_output.

# ensure we bail on any command failure
set -e

# Environment where the schema changes are ran.  For unit testing this should be unit.
environment=test

# Drop/Create the DB Schema

# Run the tests
bin/codecept run unit \
    --coverage \
    --coverage-xml \
    --coverage-html \
    --xml
