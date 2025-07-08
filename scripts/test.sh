#!/bin/bash

set -e

echo "🧪 Running test suite..."

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Test results
FAILED=0

# Function to run tests
run_test() {
    local test_name=$1
    local test_command=$2
    
    echo -e "\n${YELLOW}Running ${test_name}...${NC}"
    
    if eval $test_command; then
        echo -e "${GREEN}✓ ${test_name} passed${NC}"
    else
        echo -e "${RED}✗ ${test_name} failed${NC}"
        FAILED=1
    fi
}

# Change to src directory
cd src

# Run different test suites
run_test "PHPUnit Tests" "php artisan test"
run_test "PHPStan Analysis" "./vendor/bin/phpstan analyse"
run_test "Laravel Pint" "./vendor/bin/pint --test"
run_test "Security Check" "composer audit"

# Check if any tests failed
if [ $FAILED -eq 1 ]; then
    echo -e "\n${RED}❌ Some tests failed!${NC}"
    exit 1
else
    echo -e "\n${GREEN}✅ All tests passed!${NC}"
    exit 0
fi