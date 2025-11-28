#!/bin/bash

# Production Deployment Script for MGRNZ AI Workflow Wizard
# This script deploys the blueprint PDF fix to production

set -e  # Exit on error

echo "🚀 MGRNZ Production Deployment"
echo "================================"
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if we're in the right directory
if [ ! -f "wp-config.php" ]; then
    echo -e "${RED}Error: Not in WordPress root directory${NC}"
    exit 1
fi

echo -e "${YELLOW}Step 1: Checking git status...${NC}"
if [ -n "$(git status --porcelain)" ]; then
    echo -e "${YELLOW}You have uncommitted changes. Commit them first.${NC}"
    git status --short
    echo ""
    read -p "Do you want to commit these changes? (y/n) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        read -p "Enter commit message: " commit_msg
        git add .
        git commit -m "$commit_msg"
        echo -e "${GREEN}✓ Changes committed${NC}"
    else
        echo -e "${RED}Deployment cancelled${NC}"
        exit 1
    fi
else
    echo -e "${GREEN}✓ Working directory clean${NC}"
fi

echo ""
echo -e "${YELLOW}Step 2: Files to be deployed:${NC}"
echo "  Blueprint PDF Fix:"
echo "    - wp-content/mu-plugins/includes/class-pdf-generator.php"
echo "    - wp-content/mu-plugins/blueprint-auth-bypass.php"
echo "    - wp-content/mu-plugins/blueprint-viewer.php"
echo "    - quote-my-workflow.html"
echo ""
echo "  New 3-Step Chat Wizard:"
echo "    - wp-content/themes/saaslauncher/page-wizard-clean.php"
echo "    - wp-content/themes/saaslauncher/templates/wizard/ (entire directory)"
echo "    - wp-content/themes/saaslauncher/templates/wizard-subscribe-page.php"
echo ""

read -p "Continue with deployment? (y/n) " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo -e "${RED}Deployment cancelled${NC}"
    exit 1
fi

echo ""
echo -e "${YELLOW}Step 3: Pushing to production...${NC}"

# Check if production remote exists
if git remote | grep -q "production"; then
    echo "Pushing to production remote..."
    git push production main
    echo -e "${GREEN}✓ Pushed to production${NC}"
else
    echo -e "${YELLOW}No 'production' remote found.${NC}"
    echo "Available remotes:"
    git remote -v
    echo ""
    read -p "Enter remote name to push to (or 'skip' to skip): " remote_name
    
    if [ "$remote_name" != "skip" ]; then
        git push $remote_name main
        echo -e "${GREEN}✓ Pushed to $remote_name${NC}"
    else
        echo -e "${YELLOW}Skipped git push. Deploy files manually.${NC}"
    fi
fi

echo ""
echo -e "${GREEN}================================${NC}"
echo -e "${GREEN}✓ Deployment Complete!${NC}"
echo -e "${GREEN}================================${NC}"
echo ""
echo "Next steps:"
echo "1. Go to WordPress Admin → Pages → Edit your wizard page"
echo "2. Change Template to: 'Wizard (Clean - No WP Scripts)'"
echo "3. Click Update"
echo "4. Test the wizard at: https://your-domain.com/start-using-ai/"
echo "5. Generate a test blueprint"
echo "6. Verify HTML displays correctly"
echo "7. Test print to PDF functionality"
echo ""
echo "If issues occur, run: git revert HEAD && git push production main"
echo ""
