# Git Auto-Pull Cron Job Setup

## Quick Setup

### 1. Make the script executable (if not already)
```bash
chmod +x git-auto-pull.sh
```

### 2. Test the script manually first
```bash
# Test with current directory
./git-auto-pull.sh

# Test with specific directory and branch
./git-auto-pull.sh /path/to/your/project main
```

### 3. Add to crontab

Open crontab editor:
```bash
crontab -e
```

## Cron Job Examples

### Pull every 5 minutes
```cron
*/5 * * * * cd /Users/apple/Manish/SCI/bts-portal && /Users/apple/Manish/SCI/bts-portal/git-auto-pull.sh /Users/apple/Manish/SCI/bts-portal main >> /Users/apple/Manish/SCI/bts-portal/storage/logs/git-auto-pull-cron.log 2>&1
```

### Pull every hour
```cron
0 * * * * cd /Users/apple/Manish/SCI/bts-portal && /Users/apple/Manish/SCI/bts-portal/git-auto-pull.sh /Users/apple/Manish/SCI/bts-portal main >> /Users/apple/Manish/SCI/bts-portal/storage/logs/git-auto-pull-cron.log 2>&1
```

### Pull every day at 2 AM
```cron
0 2 * * * cd /Users/apple/Manish/SCI/bts-portal && /Users/apple/Manish/SCI/bts-portal/git-auto-pull.sh /Users/apple/Manish/SCI/bts-portal main >> /Users/apple/Manish/SCI/bts-portal/storage/logs/git-auto-pull-cron.log 2>&1
```

### Pull every 15 minutes (recommended for active development)
```cron
*/15 * * * * cd /Users/apple/Manish/SCI/bts-portal && /Users/apple/Manish/SCI/bts-portal/git-auto-pull.sh /Users/apple/Manish/SCI/bts-portal main >> /Users/apple/Manish/SCI/bts-portal/storage/logs/git-auto-pull-cron.log 2>&1
```

## Cron Schedule Format

```
* * * * *
│ │ │ │ │
│ │ │ │ └─── Day of week (0-7, where 0 and 7 = Sunday)
│ │ │ └───── Month (1-12)
│ │ └─────── Day of month (1-31)
│ └───────── Hour (0-23)
└─────────── Minute (0-59)
```

### Common Examples:
- `*/5 * * * *` - Every 5 minutes
- `*/15 * * * *` - Every 15 minutes
- `0 * * * *` - Every hour
- `0 0 * * *` - Every day at midnight
- `0 2 * * *` - Every day at 2 AM
- `0 0 * * 0` - Every Sunday at midnight
- `0 0 1 * *` - First day of every month

## Important Notes

1. **Update the paths** in the cron job to match your actual project directory
2. **Use absolute paths** for both the script and the project directory
3. **Ensure log directory exists**: `mkdir -p storage/logs`
4. **Test manually first** before adding to crontab
5. **Check logs regularly**: `tail -f storage/logs/git-auto-pull.log`

## Troubleshooting

### Check if cron is running
```bash
# View current crontab
crontab -l

# Check cron logs (macOS)
log show --predicate 'process == "cron"' --last 1h

# Check cron logs (Linux)
grep CRON /var/log/syslog
```

### Test cron environment
Create a test cron job:
```cron
* * * * * echo "Cron is working: $(date)" >> /tmp/cron-test.log
```

### Common Issues

1. **Permission denied**: Make sure the script is executable (`chmod +x git-auto-pull.sh`)
2. **Path issues**: Always use absolute paths in cron jobs
3. **Environment variables**: Cron runs with minimal environment, use absolute paths
4. **Git authentication**: If using SSH keys, ensure they're accessible to cron user

## Advanced: Using SSH Agent for Git

If your repository uses SSH authentication, you may need to set up SSH agent forwarding:

```bash
# Add to your crontab
SSH_AUTH_SOCK=/path/to/ssh-agent.sock
eval $(ssh-agent -s)
ssh-add ~/.ssh/id_rsa
```

Or use HTTPS with a personal access token instead of SSH.
