#!/bin/bash

# Deploy New Wizard to Production
# This script uploads the new 3-step chat wizard files to production

set -e

echo "🚀 Deploying New Wizard to Production"
echo "======================================"
echo ""

# Configuration - UPDATE THESE VALUES
PROD_HOST="your-production-server.com"
PROD_USER="your-ssh-username"
PROD_PATH="/path/to/wordpress/wp-content/themes/saaslauncher"

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${YELLOW}Please update the configuration in this script first:${NC}"
echo "PROD_HOST: $PROD_HOST"
echo "PROD_USER: $PROD_USER"
echo "PROD_PATH: $PROD_PATH"
echo ""
read -p "Have you updated these values? (y/n) " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo -e "${RED}Please edit deploy-wizard-to-production.sh and update the configuration${NC}"
    exit 1
fi

echo ""
echo -e "${YELLOW}Files to be uploaded:${NC}"
echo "1. page-wizard-clean.php"
echo "2. templates/wizard/ (entire directory)"
echo ""

# Check if files exist locally
if [ ! -f "wp-content/themes/saaslauncher/page-wizard-clean.php" ]; then
    echo -e "${RED}Error: page-wizard-clean.php not found${NC}"
    exit 1
fi

if [ ! -d "wp-content/themes/saaslauncher/templates/wizard" ]; then
    echo -e "${RED}Error: templates/wizard directory not found${NC}"
    exit 1
fi

echo -e "${GREEN}✓ All files found locally${NC}"
echo ""

read -p "Continue with upload? (y/n) " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo -e "${RED}Upload cancelled${NC}"
    exit 1
fi

echo ""
echo -e "${YELLOW}Uploading files via rsync...${NC}"

# Upload page template
echo "Uploading page-wizard-clean.php..."
rsync -avz --progress \
    wp-content/themes/saaslauncher/page-wizard-clean.php \
    ${PROD_USER}@${PROD_HOST}:${PROD_PATH}/

# Upload wizard directory
echo "Uploading templates/wizard/..."
rsync -avz --progress --delete \
    wp-content/themes/saaslauncher/templates/wizard/ \
    ${PROD_USER}@${PROD_HOST}:${PROD_PATH}/templates/wizard/

echo ""
echo -e "${GREEN}=====================================${NC}"
echo -e "${GREEN}✓ Upload Complete!${NC}"
echo -e "${GREEN}=====================================${NC}"
echo ""
echo "Next steps:"
echo "1. Go to WordPress Admin → Pages"
echo "2. Edit your wizard page"
echo "3. Change Template to: 'Wizard (Clean - No WP Scripts)'"
echo "4. Click Update"
echo ""
