# Contributing Guidelines

## Branch Naming Convention
- `feature/` - New features
- `bugfix/` - Bug fixes
- `hotfix/` - Urgent production fixes
- `chore/` - Maintenance tasks

## Workflow for Junior Developers

1. **Never work directly on main, staging, or develop branches**
2. Create feature branch from develop:
   ```bash
   git checkout develop
   git pull origin develop
   git checkout -b feature/your-feature-name


# Push your branch and create PR:
git push origin feature/your-feature-name

# PRs to develop branch only - Tech Lead handles staging/production


main - Production (Tech Lead only)
staging - Staging environment (Tech Lead only)
develop - Development (PR + Tech Lead approval required)


## Test as Junior Developer
# Have a junior developer try:
git checkout main
git push origin main
# Should see: error: failed to push some refs
# remote: error: GH006: Protected branch update failed

# Create PR (should work)
git checkout -b feature/test
echo "test" >> test.txt
git add test.txt
git commit -m "Test PR"
git push origin feature/test
# Create PR via GitHub UI - should work




