#!/bin/bash

# Setup Git Auto-Pull Cron Job
# This script helps you set up the git auto-pull cron job easily

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
SCRIPT_PATH="$SCRIPT_DIR/git-auto-pull.sh"
PROJECT_DIR="$SCRIPT_DIR"

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🔧 Git Auto-Pull Cron Job Setup"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# Check if script exists
if [ ! -f "$SCRIPT_PATH" ]; then
    echo "❌ Error: git-auto-pull.sh not found at $SCRIPT_PATH"
    exit 1
fi

# Make script executable
chmod +x "$SCRIPT_PATH"
echo "✅ Made git-auto-pull.sh executable"

# Ensure log directory exists
mkdir -p "$PROJECT_DIR/storage/logs"
echo "✅ Created log directory: $PROJECT_DIR/storage/logs"

# Get current branch
CURRENT_BRANCH=$(cd "$PROJECT_DIR" && git rev-parse --abbrev-ref HEAD 2>/dev/null || echo "main")
echo "📋 Current branch: $CURRENT_BRANCH"
echo ""

# Ask for schedule
echo "Select cron schedule:"
echo "  1) Every 5 minutes"
echo "  2) Every 15 minutes"
echo "  3) Every hour"
echo "  4) Every day at 2 AM"
echo "  5) Custom (enter manually)"
echo ""
read -p "Enter choice (1-5): " choice

case $choice in
    1)
        CRON_SCHEDULE="*/5 * * * *"
        ;;
    2)
        CRON_SCHEDULE="*/15 * * * *"
        ;;
    3)
        CRON_SCHEDULE="0 * * * *"
        ;;
    4)
        CRON_SCHEDULE="0 2 * * *"
        ;;
    5)
        echo ""
        echo "Enter cron schedule (format: minute hour day month weekday)"
        echo "Example: */15 * * * * (every 15 minutes)"
        read -p "Schedule: " CRON_SCHEDULE
        ;;
    *)
        echo "Invalid choice. Using default: Every 15 minutes"
        CRON_SCHEDULE="*/15 * * * *"
        ;;
esac

# Create cron job entry
CRON_JOB="$CRON_SCHEDULE cd $PROJECT_DIR && $SCRIPT_PATH $PROJECT_DIR $CURRENT_BRANCH >> $PROJECT_DIR/storage/logs/git-auto-pull-cron.log 2>&1"

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "📝 Cron Job Entry:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "$CRON_JOB"
echo ""

# Ask for confirmation
read -p "Add this to crontab? (y/n): " confirm

if [ "$confirm" != "y" ] && [ "$confirm" != "Y" ]; then
    echo "❌ Cancelled. Cron job not added."
    echo ""
    echo "To add manually, run:"
    echo "  crontab -e"
    echo ""
    echo "Then paste:"
    echo "$CRON_JOB"
    exit 0
fi

# Check if cron job already exists
if crontab -l 2>/dev/null | grep -q "git-auto-pull.sh"; then
    echo ""
    echo "⚠️  Warning: A git-auto-pull cron job already exists!"
    read -p "Replace existing cron job? (y/n): " replace
    
    if [ "$replace" != "y" ] && [ "$replace" != "Y" ]; then
        echo "❌ Cancelled. Existing cron job kept."
        exit 0
    fi
    
    # Remove existing git-auto-pull cron jobs
    crontab -l 2>/dev/null | grep -v "git-auto-pull.sh" | crontab -
    echo "✅ Removed existing git-auto-pull cron job"
fi

# Add new cron job
(crontab -l 2>/dev/null; echo "$CRON_JOB") | crontab -

echo ""
echo "✅ Cron job added successfully!"
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "📋 Current Crontab:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
crontab -l
echo ""
echo "📝 Logs will be written to:"
echo "   - $PROJECT_DIR/storage/logs/git-auto-pull.log"
echo "   - $PROJECT_DIR/storage/logs/git-auto-pull-cron.log"
echo ""
echo "💡 To view logs:"
echo "   tail -f $PROJECT_DIR/storage/logs/git-auto-pull.log"
echo ""
