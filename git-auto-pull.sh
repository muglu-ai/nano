#!/bin/bash

# Git Auto Pull Script
# This script automatically pulls the latest changes from the git repository
# Designed to be run via cron job

# Configuration
REPO_DIR="${1:-$(pwd)}"  # Use first argument or current directory
LOG_FILE="${REPO_DIR}/storage/logs/git-auto-pull.log"
BRANCH="${2:-main}"  # Use second argument or default to 'main'

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to log messages
log_message() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

# Check if directory exists
if [ ! -d "$REPO_DIR" ]; then
    log_message "${RED}Error: Directory does not exist: $REPO_DIR${NC}"
    exit 1
fi

# Check if it's a git repository
if [ ! -d "$REPO_DIR/.git" ]; then
    log_message "${RED}Error: Not a git repository: $REPO_DIR${NC}"
    exit 1
fi

# Change to repository directory
cd "$REPO_DIR" || exit 1

log_message "${GREEN}Starting git pull for: $REPO_DIR${NC}"
log_message "Branch: $BRANCH"

# Check current branch
CURRENT_BRANCH=$(git rev-parse --abbrev-ref HEAD)
log_message "Current branch: $CURRENT_BRANCH"

# Fetch latest changes
log_message "Fetching latest changes..."
if git fetch origin 2>&1 | tee -a "$LOG_FILE"; then
    log_message "${GREEN}Fetch completed successfully${NC}"
else
    log_message "${RED}Fetch failed${NC}"
    exit 1
fi

# Check if there are updates
LOCAL=$(git rev-parse @)
REMOTE=$(git rev-parse @{u})
BASE=$(git merge-base @ @{u})

if [ "$LOCAL" = "$REMOTE" ]; then
    log_message "${GREEN}Already up to date. No changes to pull.${NC}"
    exit 0
elif [ "$LOCAL" = "$BASE" ]; then
    log_message "${YELLOW}Local branch is behind. Pulling changes...${NC}"
    
    # Pull changes
    if git pull origin "$BRANCH" 2>&1 | tee -a "$LOG_FILE"; then
        log_message "${GREEN}Pull completed successfully${NC}"
        
        # Run composer install if composer.json exists
        if [ -f "composer.json" ]; then
            log_message "Running composer install..."
            if composer install --no-interaction --prefer-dist --optimize-autoloader 2>&1 | tee -a "$LOG_FILE"; then
                log_message "${GREEN}Composer install completed${NC}"
            else
                log_message "${YELLOW}Composer install had warnings (continuing...)${NC}"
            fi
        fi
        
        # Run npm install if package.json exists
        if [ -f "package.json" ]; then
            log_message "Running npm install..."
            if npm install 2>&1 | tee -a "$LOG_FILE"; then
                log_message "${GREEN}NPM install completed${NC}"
            else
                log_message "${YELLOW}NPM install had warnings (continuing...)${NC}"
            fi
        fi
        
        # Run migrations if artisan exists
        if [ -f "artisan" ]; then
            log_message "Running Laravel migrations..."
            if php artisan migrate --force 2>&1 | tee -a "$LOG_FILE"; then
                log_message "${GREEN}Migrations completed${NC}"
            else
                log_message "${YELLOW}Migrations had warnings (continuing...)${NC}"
            fi
            
            # Clear Laravel caches
            log_message "Clearing Laravel caches..."
            php artisan cache:clear 2>&1 | tee -a "$LOG_FILE"
            php artisan config:clear 2>&1 | tee -a "$LOG_FILE"
            php artisan route:clear 2>&1 | tee -a "$LOG_FILE"
            php artisan view:clear 2>&1 | tee -a "$LOG_FILE"
            log_message "${GREEN}Cache cleared${NC}"
        fi
        
        log_message "${GREEN}Git auto-pull completed successfully${NC}"
        exit 0
    else
        log_message "${RED}Pull failed${NC}"
        exit 1
    fi
elif [ "$REMOTE" = "$BASE" ]; then
    log_message "${YELLOW}Local branch is ahead. Consider pushing changes.${NC}"
    exit 0
else
    log_message "${RED}Local and remote branches have diverged. Manual intervention required.${NC}"
    exit 1
fi
